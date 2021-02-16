<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a rest entry point for rest version 2
 */
chdir('../..');
$webservice_class = 'SugarRestService';
$webservice_path = 'service/core/SugarRestService.php';
$webservice_impl_class = 'SugarRestServiceImpl';
$registry_class = 'registry';
$location = '/service/v2/rest.php';
$registry_path = 'service/v2/registry.php';
require_once('service/core/webservice.php');
