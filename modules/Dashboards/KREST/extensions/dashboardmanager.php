<?php

/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Dashboards\KREST\controllers\DashboardManagerController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('dashboards', '1.0');
$routes = [
    [
        'method'      => 'get',
        'route'       => '/dashboards/dashlets',
        'class'       => DashboardManagerController::class,
        'function'    => 'GetGlobalAndCustomDashlet',
        'description' => 'selects all global and custom dashlets',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/dashboards/dashlets/{id}',
        'class'       => DashboardManagerController::class,
        'function'    => 'ReplaceDashlet',
        'description' => 'replaces into the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/dashboards/dashlets/{id}',
        'class'       => DashboardManagerController::class,
        'function'    => 'DeleteDashlet',
        'description' => 'deletes a dashlet depending on the id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/dashboards',
        'class'       => DashboardManagerController::class,
        'function'    => 'GetAllDashboards',
        'description' => 'selects everything form dashboards',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/dashboards/{id}',
        'class'       => DashboardManagerController::class,
        'function'    => 'GetDashboardID',
        'description' => 'gets an dashboard id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/dashboards/{id}',
        'class'       => DashboardManagerController::class,
        'function'    => 'InsertINtoDashboardComponent',
        'description' => 'Inserts into an dashboard component',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

