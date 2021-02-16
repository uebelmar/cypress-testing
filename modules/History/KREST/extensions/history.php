<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\History\KREST\controllers\HistoryKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('history', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/History/fts/{parentmodule}/{parentid}',
        'class'       => HistoryKRESTController::class,
        'function'    => 'loadFTSHistory',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/History/{parentmodule}/{parentid}',
        'class'       => HistoryKRESTController::class,
        'function'    => 'loadHistory',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

