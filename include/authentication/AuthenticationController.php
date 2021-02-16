<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\authentication;

use http\Exception\UnexpectedValueException;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\SpiceCRMAuthenticate\SpiceCRMAuthenticate;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\ErrorHandlers\UnauthorizedException;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\LogicHook\LogicHook;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Administration\Administration;
use SpiceCRM\modules\Contacts\Contact;
use SpiceCRM\modules\SystemTenants\SystemTenant;
use SpiceCRM\modules\UserAccessLogs\UserAccessLog;
use SpiceCRM\modules\Users\User;
use SpiceCRM\includes\authentication\UserAuthenticate\UserAuthenticate;
use SpiceCRM\includes\authentication\LDAPAuthenticate\LDAPAuthenticate;
use SpiceCRM\includes\authentication\TwoFactorAuthenticate\TwoFactorAuthenticate;

class AuthenticationController
{


    /**
     * Stores the current token.
     * @var string|null
     */
    public $token = null;

    /**
     * Stores the User object of the user that is currently logged in.
     * @var User|null
     */
    private $currentUser = null;
    protected static $authControllerInstance = null;
    /**
     * @var UserAuthenticate
     */
    public $authController;

    /**
     * the systemtenant id if we are in a tenant
     *
     * @var null
     */
    public $systemtenantid = null;

    public $canChangePassword = false;

    /**
     * the name of the tenant
     *
     * @var null
     */
    public $systemtenantname = null;
    public $errorReason;
    public $errorCode;


    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * Getter for the current User object.
     *
     * @return User
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    public function isAuthenticated()
    {
        return $this->currentUser instanceof User;
    }

    public function isAdmin()
    {
        return $this->currentUser instanceof User && $this->currentUser->isAdmin();
    }

    /**
     * Setter for the current User object.
     *
     * @param User $userBean
     * @return bool
     */
    public function setCurrentUser(User $userBean)
    { //todo shouldnt we set currentuser on usersingleton?
        $this->currentUser = $userBean;
        return true;
    }

    /**
     * @param string|null $username
     * @param string|null $password
     * @param null $token
     * @param null $tokenIssuer
     * @return array
     * @throws UnauthorizedException
     */
    public function authenticate(string $username = null, string $password = null, $token = null, $tokenIssuer = null)
    {
        try {
            /** @var User $userObj */
            $userObj = null;
            if ($token) {
                $userObj = $this->handleTokenAuth($token, $tokenIssuer);
            } elseif ($username && $password) {
                $userObj = $this->handleUserPassAuth($username, $password);
            } else {
                throw new UnauthorizedException("Invalid authentication method", 6);
            }


            //check status field on user
            $this->checkUserStatus($userObj);


            if (($username && $password) || $tokenIssuer !== 'SpiceCRM') { //login was via user/pass therefore create session and log login
                $token = SpiceCRMAuthenticate::createSession($userObj);

                //should we log successful login?
                if (array_key_exists("logSuccessLogin", SpiceConfig::getInstance()->config)) {
                    $this->logSuccessLogin($userObj);
                }
            }

            $this->setCurrentUser($userObj);
            $this->handleTenants();

        } catch (UnauthorizedException $e) {
            //log login attempt
            /** @var UserAccessLog $userAccessLogObj */
            $userAccessLogObj = BeanFactory::getBean('UserAccessLogs');
            $userAccessLogObj->addRecord("loginfail", $username);


            if ($username) {
                $amountFailedLogins = $userAccessLogObj->getAmountFailedLoginsWithinByUsername($username);
                if ($amountFailedLogins > 10) { //todo use a config variable
                    //todo block user - via status on user ... or new field?
                }
            } else {
                //todo should we block by ip?
            }
            unset($userAccessLogObj);

            $this->errorReason = $e->getMessage();
            $this->errorCode = $e->getErrorCode();

            throw new UnauthorizedException($e->getMessage(), $e->getErrorCode());
        }

        return [ //todo find a solution to specify return format
            'token' => $token,
            'user' => $userObj,
            'pwChangeEnabled' => LDAPAuthenticate::isLdapEnabled() === false && $userObj->external_auth_only === false
        ];

    }

    private function handleTokenAuth($token, $tokenIssuer)
    {
        $userObj = null;
        $authenticationClass = "SpiceCRM\includes\authentication\\{$tokenIssuer}Authenticate\\{$tokenIssuer}Authenticate";
        if (class_exists($authenticationClass, true)) {
            $authenticationController = new $authenticationClass();
            $userObj = $authenticationController->authenticate($token);
        } else {
            throw new \Exception("AuthenticationClass {$authenticationClass} not found");
        }
        return $userObj;
    }

    private function logSuccessfulLogin(User $userObj)
    {
        $userAccessLogObj = BeanFactory::getBean('UserAccessLogs');
        if (!$userAccessLogObj->addRecord("loginsuccess", $userObj->user_name)) {
            unset($userAccessLogObj);
            throw new \Exception("Unable to log successful login");
        }
        unset($userAccessLogObj);
        return true;
    }

    private function checkUserStatus(User $userObj)
    {
        switch ($userObj->status) {
            case "Active":
                break;
            case "Inactive":
                throw new UnauthorizedException("User is inactive", 4);
            case "Blocked":
                throw new UnauthorizedException("User is blocked", 3);
            default:
                throw new UnauthorizedException("User Status is unknown", 5);
        }
        return true;
    }

    function hasPasswordExpired(User $userObj)
    {

        $type = '';
        if ($userObj->system_generated_password == '1') {
            return true;
        }

        if ($userObj->portal_only == '0') {
            global $mod_strings, $timedate;
            $res = SpiceConfig::getInstance()->config['passwordsetting'];
            if ($type != '') {
                switch ($res[$type . 'expiration']) {

                    case '1':
                        global $timedate;
                        if ($userObj->pwd_last_changed == '') {
                            $userObj->pwd_last_changed = $timedate->nowDb();
                            $userObj->save();
                        }

                        $expireday = $res[$type . 'expirationtype'] * $res[$type . 'expirationtime'];
                        $expiretime = $timedate->fromUser($userObj->pwd_last_changed)->add(new \DateInterval("P{$expireday}D"))->getTimeStamp();

                        if ($timedate->getNow()->getTimeStamp() < $expiretime)
                            return false;
                        else {
                            return true;
                        }
                        break;


                    case '2':
                        $login = $userObj->getPreference('loginexpiration');
                        $userObj->setPreference('loginexpiration', $login + 1);
                        $userObj->save();
                        if ($login + 1 >= $res[$type . 'expirationlogin']) {
                            return true;
                        } else {
                            return false;
                        }
                        break;

                    case '0':
                        return false;
                        break;
                }
            }
        }
    }

    /**
     * Handles the authentication with username/password credentials.
     * @return User | false
     */
    private function handleUserPassAuth(string $authUser, string $authPass)
    {
        //first check if ldap authentication is enabled
        if (LDAPAuthenticate::isLdapEnabled()) {
            /** @var LDAPAuthenticate $ldapController */
            $ldapController = new LDAPAuthenticate();
            try {
                if ($userObj = $ldapController->authenticate($authUser, $authPass)) {

                    return $userObj;
                }
            } catch (UnauthorizedException $e) {
                //todo log?
            }

        }

        //second use sugar authentication
        $sugarAuthenticationController = new UserAuthenticate();
        if ($userObj = $sugarAuthenticationController->authenticate($authUser, $authPass)) {
            //check if password is expired
            if ($this->hasPasswordExpired($userObj)) {
                throw new UnauthorizedException("Password expired", 2);
            }
            $this->canChangePassword = true;
            return $userObj;
        }
        throw new UnauthorizedException("Invalid Username/Password Combination", 1);

    }


    private function handleTenants()
    {
        /* switch to a different tenant if the tenant id is set for the user */
        if (!empty($this->getCurrentUser()->systemtenant_id)) {
            $tenant = new SystemTenant();
            $tenant->retrieve($this->getCurrentUser()->systemtenant_id);
            $tenant->switchToTenant();
            $this->systemtenantid = $tenant->id;
            $this->systemtenantname = $tenant->name;
        }
    }

    /**
     * Returns an array with basic data about the current user.
     *
     * @return array
     */
    public function getLoginData()
    {
        $authenticationController = AuthenticationController::getInstance();
        if ($authenticationController->getCurrentUser() === null) {
            throw new UnauthorizedException($authenticationController->errorReason, $authenticationController->errorCode);
        }
        $currentUser = $this->getCurrentUser();

        $loginData = [
            'admin' => $currentUser->is_admin == '1' ? true : false,
            'display_name' => $currentUser->get_summary_text(),
            'email' => $currentUser->email1,
            'first_name' => $currentUser->first_name,
            'id' => session_id(),
            'last_name' => $currentUser->last_name,
            'portal_only' => $currentUser->portal_only == '1' ? true : false,
            'user_name' => $currentUser->user_name,
            'userid' => $currentUser->id,
            'user_image' => $currentUser->user_image,
            'dev' => $currentUser->is_dev == '1' ? true : false,
            'companycode_id' => $currentUser->companycode_id,
            'tenant_id' => $currentUser->systemtenant_id,
            'tenant_name' => $this->systemtenantname,
            'obtainGDPRconsent' => false,
            'canchangepassword' => AuthenticationController::getInstance()->canChangePassword
        ];

        // Is it a portal user? And the GDPR consent for portal users is configured?
        if ($currentUser->portal_only and @SpiceConfig::getInstance()->config['portal_gdpr']['obtain_consent']) {
            $contactOfPortalUser = new Contact();
            $contactOfPortalUser->retrieve_by_string_fields(['portal_user_id' => $this->getCurrentUser()->id]);
            // gdpr_marketing_agreement not 'g' and not 'r' indicates that the user has not yet been asked for consent of GDPR in general (data AND marketing)
            if (($contactOfPortalUser->gdpr_marketing_agreement !== 'g' and $contactOfPortalUser->gdpr_marketing_agreement !== 'r')
                and !$contactOfPortalUser->gdpr_data_agreement) {
                $loginData['obtainGDPRconsent'] = true;
            }
        }

        return $loginData;
    }

    /**
     * Returns an instance of the authentication controller
     *
     * @param string $type this is the type of authentication you want to use default is SugarAuthenticate
     * @return AuthenticationController
     */
    public static function getInstance()
    {
        if (empty(self::$authControllerInstance)) {
            self::$authControllerInstance = new static();
        }

        return self::$authControllerInstance;
    }


    /**
     * Deletes the session if it was created without login.
     */
    public function cleanup()
    {
        if (!empty($this->tmpSessionId)) {
            session_destroy();
        }
    }


    /**
     * Called when a user requests to logout. Should invalidate the session and redirect
     * to the login page.
     */
    public function logout()
    {
        $this->getCurrentUser()->call_custom_logic('before_logout');
        $this->authController->logout();
        LogicHook::initialize();
        $GLOBALS['logic_hook']->call_custom_logic('Users', 'after_logout');
    }

}
