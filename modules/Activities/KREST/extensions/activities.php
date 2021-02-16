<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Activities\KREST\controllers\ActivitiesKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('activities', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Activities/fts/{parentmodule}/{parentid}',
        'class'       => ActivitiesKRESTController::class,
        'function'    => 'loadFTSActivities',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/Activities/{parentmodule}/{parentid}',
        'class'       => ActivitiesKRESTController::class,
        'function'    => 'loadHistory',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
