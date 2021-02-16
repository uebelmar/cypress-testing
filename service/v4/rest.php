<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a rest entry point for rest version 4
 */
chdir('../..');
require_once('SugarWebServiceImplv4.php');
$webservice_class = 'SugarRestService';
$webservice_path = 'service/core/SugarRestService.php';
$webservice_impl_class = 'SugarWebServiceImplv4';
$registry_class = 'registry';
$location = '/service/v4/rest.php';
$registry_path = 'service/v4/registry.php';
require_once('service/core/webservice.php');
