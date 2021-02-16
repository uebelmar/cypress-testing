<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a soap entry point for soap version 3
 */
chdir('../..');
require_once('SugarWebServiceImplv2_1.php');
$webservice_class = 'SugarSoapService2';
$webservice_path = 'service/v2/SugarSoapService2.php';
$registry_class = 'registry_v2_1';
$registry_path = 'service/v2_1/registry.php';
$webservice_impl_class = 'SugarWebServiceImplv2_1';
$location = '/service/v2_1/soap.php';
require_once('service/core/webservice.php');