<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

// set the autoloaders
require('include/utils/autoloader.php');
require_once dirname(__FILE__).'/vendor/autoload.php';


require_once('include/entryPoint.php');

ob_start();

require_once('soap/SoapError.php');
require_once('vendor/nusoap/nusoap.php');
require_once(get_custom_file_if_exists('modules/Contacts/Contact.php'));
require_once(get_custom_file_if_exists('modules/Accounts/Account.php'));
require_once(get_custom_file_if_exists('modules/Opportunities/Opportunity.php'));
require_once('service/core/SoapHelperWebService.php');
// CR1000426 cleanup backend, module Cases removed
//require_once(get_custom_file_if_exists('modules/Cases/Case.php'));//ignore notices
error_reporting(E_ALL ^ E_NOTICE);


global $HTTP_RAW_POST_DATA;

$administrator = BeanFactory::getBean('Administration');
$administrator->retrieveSettings();

$NAMESPACE = 'http://www.sugarcrm.com/sugarcrm';
$server = new soap_server;
$server->configureWSDL('sugarsoap', $NAMESPACE, SpiceConfig::getInstance()->config['site_url'].'/soap.php');

//New API is in these files
if(!empty($administrator->settings['portal_on'])) {
	require_once(get_custom_file_if_exists('soap/SoapPortalUsers.php'));
}

require_once(get_custom_file_if_exists('soap/SoapSugarUsers.php'));
//require_once('soap/SoapSugarUsers_version2.php');
require_once(get_custom_file_if_exists('soap/SoapData.php'));
require_once(get_custom_file_if_exists('soap/SoapDeprecated.php'));



/* Begin the HTTP listener service and exit. */
ob_clean();

if (!isset($HTTP_RAW_POST_DATA)){
    $HTTP_RAW_POST_DATA = file_get_contents('php://input');
}

require_once('include/resource/ResourceManager.php');
$resourceManager = ResourceManager::getInstance();
$resourceManager->setup('Soap');
$observers = $resourceManager->getObservers();
//Call set_soap_server for SoapResourceObserver instance(s)
foreach($observers as $observer) {
   if(method_exists($observer, 'set_soap_server')) {
   	  $observer->set_soap_server($server);
   }
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
global $soap_server_object;
$soap_server_object = $server;
$server->service($HTTP_RAW_POST_DATA);
ob_end_flush();
flush();
sugar_cleanup();
exit();
