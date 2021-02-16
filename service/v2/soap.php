<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a soap entry point for soap version 2
 */
chdir('../..');
$webservice_class = 'SugarSoapService2';
$webservice_path = 'service/v2/SugarSoapService2.php';
$registry_class = 'registry';
$registry_path = 'service/v2/registry.php';
$webservice_impl_class = 'SugarWebServiceImpl';
$location = '/service/v2/soap.php';
require_once('service/core/webservice.php');




		
