<?php

/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\includes\SpiceBeanGuides\KREST\controllers\SpiceBeanGuidesKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('spicebeanguides', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/spicebeanguide/{module}',
        'class'       => SpiceBeanGuidesKRESTController::class,
        'function'    => 'getStages',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spicebeanguide/{module}/{beanid}',
        'class'       => SpiceBeanGuidesKRESTController::class,
        'function'    => 'getBeanStages',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
