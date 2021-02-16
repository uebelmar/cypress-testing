<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Administration\KREST\controllers\adminController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('admin', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/admin/systemstats',
        'class'       => adminController::class,
        'function'    => 'systemstats',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/admin/generalsettings',
        'class'       => adminController::class,
        'function'    => 'getGeneralSettings',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/admin/writesettings',
        'class'       => adminController::class,
        'function'    => 'writeGeneralSettings',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
