<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SAPIdocs\KREST\controllers\SAPIdocsManagerController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('sapidocsmanager', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/SAPIdocsManager/segments',
        'class'       => SAPIdocsManagerController::class,
        'function'    => 'getSegments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/SAPIdocsManager/segments',
        'class'       => SAPIdocsManagerController::class,
        'function'    => 'getSegments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
