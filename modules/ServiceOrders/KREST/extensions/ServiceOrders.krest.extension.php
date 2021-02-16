<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ServiceOrders\KREST\controllers\ServiceOrdersKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('service', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/modules/ServiceOrders/Planner/records',
        'class'       => ServiceOrdersKRESTController::class,
        'function'    => 'getPlannerRecords',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/ServiceOrders/discoverparent/{parentType}/{parentId}',
        'class'       => ServiceOrdersKRESTController::class,
        'function'    => 'discoverparent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

