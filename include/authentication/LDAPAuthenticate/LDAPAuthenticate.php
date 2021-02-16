<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\authentication\LDAPAuthenticate;


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\UserAuthenticate\UserAuthenticate;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\ErrorHandlers\UnauthorizedException;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Users\User;

/**
 * This file is used to control the authentication process.
 * It will call on the user authenticate and controll redirection
 * based on the users validation
 *
 */
class LDAPAuthenticate
{

    private $server = null;
    private $port = 389;
    private $adminUser = null;
    private $adminPassword = null;
    private $baseDn = null;
    private $loginAttr = null;
    private $bindAttr = null;
    private $ldapConn = null;
    private $loginFilter = null;
    private $autoCreateUser = false;

    private $config = [
        'users' =>
            [
                'fields' =>
                    [
                        "givenName" => 'first_name',
                        "sn" => 'last_name',
                        "mail" => 'email1',
                        "telephoneNumber" => 'phone_work',
                        "facsimileTelephoneNumber" => 'phone_fax',
                        "mobile" => 'phone_mobile',
                        "street" => 'address_street',
                        "l" => 'address_city',
                        "st" => 'address_state',
                        "postalCode" => 'address_postalcode',
                        "c" => 'address_country'


                    ]
            ],
        'system' =>
            ['overwriteSugarUserInfo' => true,],
    ];



    /**
     * @return LDAPAuthenticate
     */
    static function getInstance()
    {
        if (self::$instance === null) {
            //set instance
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @return bool
     */
    public static function isLdapEnabled()
    {
        $config = self::getLdapConfig();
        return count($config) > 0 ? true : false;
    }

    /**
     * This function is called by the authcontroller
     * @param STRING $name
     * @param STRING $password
     * @return User | false
     *
     * Contributions by Erik Mitchell erikm@logicpd.com
     */
    public function authenticate($name, $password)
    {
        //merge to config... maybe find a better solution?
        $this->config = array_merge($this->config, ['servers' => $this->getLdapConfig()]);

        //if servers are configured and extension is not loaded throw an error
        if (count($this->config['servers']) && !extension_loaded("ldap")) {
            throw new \Exception("Ldap Module not loaded");
        }
        if (count($this->config['servers'])) {
            foreach ($this->config['servers'] as $authSys) {
                //set current system
                $this->server = $authSys['hostname'];
                if ($authSys['port']) {
                    $this->port = $authSys['port'];
                }
                $this->adminUser = $authSys['admin_user'];
                $this->adminPassword = $authSys['admin_password'];
                $this->baseDn = $authSys['base_dn'];
                $this->loginAttr = $authSys['login_attr'];
                $this->bindAttr = $authSys['bind_attr'];
                $this->loginFilter = $authSys['loginFilter'];
                $this->ldapAuthentication = $authSys['ldap_authentication'];
                $this->groups = $authSys['ldap_groups'];
                $this->autoCreateUser = $authSys['auto_create_users'];

                if ($userObj = $this->ldapAuthenticate($name, $password)) {
                    return $userObj;
                }

            }
            throw new UnauthorizedException();
        }
    }

    /**
     * @return ldap_link
     */
    private function ldapConn()
    {

        if (SpiceConfig::getInstance()->config['developerMode'] == true) {
            ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
        }
        LoggerManager::getLogger()->debug("ldapauth: Connecting to LDAP server: $this->server");
        $ldapConn = ldap_connect($this->server, $this->port);
        if ($this->ldapConn === false) {
            $message = 'Unable to ldap_connect. check syntax of server and port (' . $server . ':' . $port . '). ldap server has not been contacted in this stage.';
            LoggerManager::getLogger()->fatal($message);
            return false;
        }
        ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldapConn, LDAP_OPT_REFERRALS, 0); // required for AD

        $this->ldapConn = $ldapConn;
        return true;
    }

    private function ldapAuthenticate($name, $password)
    {

        if ($this->ldapConn === null) {
            if (!$this->ldapConn()) {
                return false;
            }
        }

        // If constant is defined, set the timeout (PHP >= 5.3)
        if (defined('LDAP_OPT_NETWORK_TIMEOUT')) {
            // Network timeout, lower than PHP and DB timeouts
            ldap_set_option($this->ldapConn, LDAP_OPT_NETWORK_TIMEOUT, 50); //todo clarify...should we add this to the authSystem?
        }
        /**
         * IMPORTANT! ldap_bind fails in some cases (ms server 2019 AD) if there are special chars in the password
         * see https://stackoverflow.com/a/35239071
         */
        if ($this->ldapAuthentication) {

            // MRF - Bug #18578 - punctuation was being passed as HTML entities, i.e. &amp;
            //$bind_password = html_entity_decode($this->adminPassword);
            $bind = ldap_bind($this->ldapConn, $this->adminUser, md5($this->adminPassword));
        } else {
            //try to bind anonymous. note: since ms server 2003, authentication is required!
            $bind = ldap_bind($this->ldapConn);
        }
        if ($bind === false) {
            $message = "unable to bind to ldap via ldap_bind. assuming wrong credentials. to get more logs add ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);";
            LoggerManager::getLogger()->warn($message);
            throw new \Exception($message);//todo clarify if we should return false (for authentication failed) or throw an error
        }

        // Authentication succeeded, get info from LDAP directory
        $attrs = array_keys($this->config['users']['fields']);

        $name_filter = $this->getUserNameFilter($name);

        if ($this->groups) {
            $attr = array_merge($attr, explode(",", $this->groups));
        }


        $ldapSearchResult = ldap_search($this->ldapConn, $this->baseDn, $this->getUserNameFilter($name), $attrs);
        if ($ldapSearchResult === false) {
            //todo log? seems this user is unknown in ad...
            return false;
        }
        $ldapGetEntriesResult = ldap_get_entries($this->ldapConn, $ldapSearchResult);


        $this->ldapUserInfo = $this->mapUserFields($ldapGetEntriesResult);

        //we should check that a user is a member of a specific group

        /**
         * if (!empty($this->ldapSettings['ldap_group'])) {
         * LoggerManager::getLogger()->debug("LDAPAuth: scanning group for user membership");
         * $group_user_attr = $this->ldapSettings['ldap_group_user_attr'];
         * $group_attr = $this->ldapSettings['ldap_group_attr'];
         * if (!isset($ldapGetEntriesResult[0][$group_user_attr])) {
         * LoggerManager::getLogger()->fatal("ldapauth: $group_user_attr not found for user $name cannot authenticate against an LDAP group");
         * ldap_close($this->ldapConn);
         * return '';
         * } else {
         * $user_uid = $ldapGetEntriesResult[0][$group_user_attr];
         * if (is_array($user_uid)) {
         * $user_uid = $user_uid[0];
         * }
         * // If user_uid contains special characters (for LDAP) we need to escape them !
         * $user_uid = str_replace(["(", ")"], ["\(", "\)"], $user_uid);
         *
         * }
         *
         * // build search query and determine if we are searching for a bare id or the full dn path
         * $group_name = $this->ldapSettings['ldap_group_name'] . ","
         * . $this->ldapSettings['ldap_group_dn'];
         * LoggerManager::getLogger()->debug("ldapauth: Searching for group name: " . $group_name);
         * $user_search = "";
         * if (!empty($this->ldapSettings['ldap_group_attr_req_dn'])
         * && $this->ldapSettings['ldap_group_attr_req_dn'] == 1) {
         *
         * LoggerManager::getLogger()->debug("ldapauth: Checking for group membership using full user dn");
         * $user_search = "($group_attr=" . $group_user_attr . "=" . $user_uid . "," . $this->base_dn . ")";
         * } else {
         * $user_search = "($group_attr=" . $user_uid . ")";
         * }
         * LoggerManager::getLogger()->debug("ldapauth: Searching for user: " . $user_search);
         *
         * //user is not a member of the group if the count is zero get the logs and return no id so it fails login
         * if (!isset($user_uid)
         * || ldap_count_entries($this->ldapConn, ldap_search($this->ldapConn, $group_name, $user_search)) == 0) {
         *
         * LoggerManager::getLogger()->fatal("ldapauth: User ($name) is not a member of the LDAP group");
         * $user_id = var_export($user_uid, true);
         * LoggerManager::getLogger()->debug(
         * "ldapauth: Group DN:{$this->ldapSettings['ldap_group_dn']}"
         * . " Group Name: " . $this->ldapSettings['ldap_group_name']
         * . " Group Attribute: $group_attr  User Attribute: $group_user_attr :(" . $user_uid . ")"
         * );
         *
         * ldap_close($this->ldapConn);
         * return '';
         * }
         * }
         **/

        ldap_close($this->ldapConn);
        $userId = false;
        /** @var User $userClass */
        $userClass = BeanFactory::getBean("Users");
        try {
            $userObj = $userClass::findByUserName($name);
            if ($userObj->status === "Active") {
                return $userObj;
            }
        } catch (NotFoundException $e) {
            //create a new user
            if ($this->autoCreateUser) {
                try {
                    $this->createUser($name);
                } catch (\Exception $e) {
                    LoggerManager::getLogger()->error("Unable to create user for " . $name);
                }

            }
        }

        return $authSuccess;
    }


    private static function getLdapConfig()
    {
        $rows = [];
        $db = DBManagerFactory::getInstance();

        $query = $db->query("SELECT * from ldap_settings order by priority");
        while ($row = $db->fetchByAssoc($query)) {
            $rows[] = $row;
        }
        return $rows;
    }


    /**
     * takes in a name and creates the appropriate search filter for that user name including any additional filters specified in the system settings page
     * @param $name
     * @return String
     */
    private function getUserNameFilter($name)
    {
        $name_filter = "(" . $this->loginAttr . "=" . $name . ")";
        //add the additional user filter if it is specified
        if (!empty($this->loginFilter)) {
            $add_filter = $this->loginFilter;
            if (substr($add_filter, 0, 1) !== "(") {
                $add_filter = "(" . $add_filter . ")";
            }
            $name_filter = "(&" . $name_filter . $add_filter . ")";
        }
        return $name_filter;
    }

    /**
     * Creates a user with the given User Name and returns the id of that new user
     * populates the user with what was set in ldapUserInfo
     *
     * @param STRING $name
     * @return STRING $id
     */
    private function createUser($name)
    {

        $user = BeanFactory::getBean('Users');
        $user->user_name = $name;
        foreach ($this->ldapUserInfo as $key => $value) {
            $user->$key = $value;
        }
        $user->employee_status = 'Active';
        $user->status = 'Active';
        $user->is_admin = 0;
        $user->external_auth_only = 1; //todo where should we get this value from?
        if (!$user->save()) {
            throw new \Exception("Unable to save User");
        }
        return true;

    }

    /**
     * this is called when a user logs in
     *
     * @param STRING $name
     * @param STRING $password
     * @return boolean
     */
    /**
     * function loadUserOnLogin($name, $password)
     * {
     *
     * global $mod_strings;
     *
     * // Check if the LDAP extensions are loaded
     * if (!function_exists('ldap_connect')) {
     * $error = $mod_strings['LBL_LDAP_EXTENSION_ERROR'];
     * LoggerManager::getLogger()->fatal($error);
     * $_SESSION['login_error'] = $error;
     * return false;
     * }
     *
     * global $login_error;
     * $GLOBALS['ldap_config'] = BeanFactory::getBean('Administration');
     * $GLOBALS['ldap_config']->retrieveSettings('ldap');
     * LoggerManager::getLogger()->debug("Starting user load for " . $name);
     * if (empty($name) || empty($password)) return false;
     *
     * $user_id = $this->authenticateUser($name, $password);
     * if (empty($user_id)) {
     * //check if the user can login as a normal sugar user
     * LoggerManager::getLogger()->fatal('SECURITY: User authentication for ' . $name . ' failed');
     * return false;
     * }
     * $this->loadUserOnSession($user_id);
     * return true;
     * }
     **/


    /**
     * @param array $info
     * @return array
     */
    private function mapUserFields(array $info): array
    {
        $return = [];
        foreach ($this->config['users']['fields'] as $key => $value) {
            //MRF - BUG:19765
            $key = strtolower($key);
            if (isset($info[0]) && isset($info[0][$key]) && isset($info[0][$key][0])) {
                $return[$value] = $info[0][$key][0];
            }
        }
        return $return;
    }
}
