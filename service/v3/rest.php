<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a rest entry point for rest version 3.1
 */
chdir('../..');
require_once('SugarWebServiceImplv3.php');
$webservice_class = 'SugarRestService';
$webservice_path = 'service/core/SugarRestService.php';
$webservice_impl_class = 'SugarWebServiceImplv3';
$registry_class = 'registry';
$location = '/service/v3/rest.php';
$registry_path = 'service/v3/registry.php';
require_once('service/core/webservice.php');
