<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

// require the autoloader
require_once 'vendor/autoload.php';

//change directories to where this file is located.
//this is to make sure it can find dce_config.php
chdir(dirname(__FILE__));

require_once('include/entryPoint.php');

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    sugar_die("cron.php is CLI only.");
}

if(empty($current_language)) {
	$current_language = SpiceConfig::getInstance()->config['default_language'];
}

$app_list_strings = return_app_list_strings_language($current_language);
$app_strings = return_application_language($current_language);

$authController = AuthenticationController::getInstance();
$authController->setCurrentUser(BeanFactory::getBean('Users'));

$current_user = $authController->getCurrentUser();
$current_user->getSystemUser();

LoggerManager::getLogger()->debug('--------------------------------------------> at cron.php <--------------------------------------------');
$cron_driver = !empty(SpiceConfig::getInstance()->config['cron_class'])? SpiceConfig::getInstance()->config['cron_class']:'SugarCronJobs';
LoggerManager::getLogger()->debug("Using $cron_driver as CRON driver");

if(file_exists("custom/include/SugarQueue/$cron_driver.php")) {
   require_once "custom/include/SugarQueue/$cron_driver.php";
} else {
   require_once "include/SugarQueue/$cron_driver.php";
}

$jobq = new $cron_driver();
$jobq->runCycle();

$exit_on_cleanup = true;

sugar_cleanup(false);
// some jobs have annoying habit of calling sugar_cleanup(), and it can be called only once
// but job results can be written to DB after job is finished, so we have to disconnect here again
// just in case we couldn't call cleanup
if(class_exists('DBManagerFactory')) {
	$db = DBManagerFactory::getInstance();
	$db->disconnect();
}

// If we have a session left over, destroy it
if(session_id()) {
    session_destroy();
}

if($exit_on_cleanup) exit($jobq->runOk()?0:1);
