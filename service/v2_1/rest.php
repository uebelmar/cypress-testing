<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a rest entry point for rest version 3.1
 */
chdir('../..');
require_once('SugarWebServiceImplv2_1.php');
$webservice_class = 'SugarRestService';
$webservice_path = 'service/core/SugarRestService.php';
$webservice_impl_class = 'SugarWebServiceImplv2_1';
$registry_class = 'registry_v2_1';
$location = '/service/v2_1/rest.php';
$registry_path = 'service/v2_1/registry.php';
require_once('service/core/webservice.php');
