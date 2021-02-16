<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a rest entry point for rest version 3.1
 */
chdir('../..');
require_once('SugarWebServiceImplv3_1.php');
$webservice_class = 'SugarRestService';
$webservice_path = 'service/core/SugarRestService.php';
$webservice_impl_class = 'SugarWebServiceImplv3_1';
$registry_class = 'registry';
$location = '/service/v3_1/rest.php';
$registry_path = 'service/v3_1/registry.php';
require_once('service/core/webservice.php');
