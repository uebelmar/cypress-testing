<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a soap entry point for soap version 3.1
 */
chdir('../..');
require_once('SugarWebServiceImplv3_1.php');
$webservice_class = 'SugarSoapService2';
$webservice_path = 'service/v2/SugarSoapService2.php';
$registry_class = 'registry_v3_1';
$registry_path = 'service/v3_1/registry.php';
$webservice_impl_class = 'SugarWebServiceImplv3_1';
$location = '/service/v3_1/soap.php';
require_once('service/core/webservice.php');