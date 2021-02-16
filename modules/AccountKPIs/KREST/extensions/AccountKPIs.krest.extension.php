<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\AccountKPIs\KREST\controllers\AccountKpiKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('accountkpis', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/AccountKPIs/{accountid}/getsummary',
        'class'       => AccountKpiKRESTController::class,
        'function'    => 'getAccountKpi',
        'description' => 'get sales account details',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);