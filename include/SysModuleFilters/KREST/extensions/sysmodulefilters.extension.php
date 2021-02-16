<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SysModuleFilters\KREST\controllers\SysModuleFiltersController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('sysmodulefilters', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/sysmodulefilters/{module}',
        'class'       => SysModuleFiltersController::class,
        'function'    => 'getFilters',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/sysmodulefilters/{module}/{filter',
        'class'       => SysModuleFiltersController::class,
        'function'    => 'getFilter',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/sysmodulefilters/{module}/{filter',
        'class'       => SysModuleFiltersController::class,
        'function'    => 'saveFilter',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/sysmodulefilters/{module}/{filter',
        'class'       => SysModuleFiltersController::class,
        'function'    => 'deleteFilter',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
