<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\Five9VOIP\KREST\controllers\Five9VOIPKRESTController;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();


/**
 * register the Extension
 */
#global $sugar_config;
$RESTManager->registerExtension('five9', '1.0', ['socketurl_frontend' => SpiceConfig::getInstance()->config['starface']['socketurl_frontend']]);

$routes = [
    [
        'method'      => 'post',
        'route'       => '/Five9/login',
        'class'       => Five9VOIPKRESTController::class,
        'function'    => 'login',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Five9/preferences',
        'class'       => Five9VOIPKRESTController::class,
        'function'    => 'setPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/Five9/preferences',
        'class'       => Five9VOIPKRESTController::class,
        'function'    => 'getPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Five9',
        'class'       => Five9VOIPKRESTController::class,
        'function'    => 'initiateCall',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/Five9/{callid',
        'class'       => Five9VOIPKRESTController::class,
        'function'    => 'hangupCall',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
