<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a soap entry point for soap version 4
 */
chdir('../..');
require_once('SugarWebServiceImplv4.php');
$webservice_class = 'SugarSoapService2';
$webservice_path = 'service/v2/SugarSoapService2.php';
$registry_class = 'registry_v4';
$registry_path = 'service/v4/registry.php';
$webservice_impl_class = 'SugarWebServiceImplv4';
$location = '/service/v4/soap.php';
require_once('service/core/webservice.php');