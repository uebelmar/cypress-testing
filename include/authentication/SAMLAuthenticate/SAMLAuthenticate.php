<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\authentication\SAMLAuthenticate;



use SpiceCRM\includes\SugarAuthenticate\SugarAuthenticate;

/**
 * This file is used to control the authentication process. 
 * It will call on the user authenticate and controll redirection 
 * based on the users validation
 *
 */


require_once('modules/Users/authentication/SugarAuthenticate/SugarAuthenticate.php');
require_once('modules/Users/authentication/SAMLAuthenticate/lib/onelogin/saml.php');
class SAMLAuthenticate extends SugarAuthenticate {
	var $userAuthenticateClass = 'SAMLAuthenticateUser';
	var $authenticationDir = 'SAMLAuthenticate';
	/**
	 * Constructs SAMLAuthenticate
	 * This will load the user authentication class
	 *
	 * @return SAMLAuthenticate
	 */
	function __construct(){
		parent::__construct();
	}

    /**
     * pre_login
     * 
     * Override the pre_login function from SugarAuthenticate so that user is
     * redirected to SAML entry point if other is not specified
     */
    function pre_login()
    {
        parent::pre_login();

        $this->redirectToLogin($GLOBALS['app']);
    }

    /**
     * Called when a user requests to logout
     *
     * Override default behavior. Redirect user to special "Logged Out" page in
     * order to prevent automatic logging in.
     */
    public function logout() {
        session_destroy();
        ob_clean();
        header('Location: index.php?module=Users&action=LoggedOut');
        sugar_cleanup(true);
    }

    /**
     * Redirect to login page
     * 
     * @param SugarApplication $app
     */
    public function redirectToLogin(SugarApplication $app)
    {
        require(get_custom_file_if_exists('modules/Users/authentication/SAMLAuthenticate/settings.php'));

        $loginVars = $app->createLoginVars();

        // $settings - variable from modules/Users/authentication/SAMLAuthenticate/settings.php
        $settings->assertion_consumer_service_url .= htmlspecialchars($loginVars); 
        
        $authRequest = new SamlAuthRequest($settings);
        $url = $authRequest->create();

        $app->redirect($url);
    }
}
