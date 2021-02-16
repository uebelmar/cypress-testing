<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\StarFaceVOIP\KREST\controllers\StarFaceVOIPKRESTController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('starface', '1.0', ['socketurl_frontend' => SpiceConfig::getInstance()->config['starface']['socketurl_frontend']]);
#$RESTManager->app->post('/StarFaceVOIP/events/handle', [new StarFaceVOIPKRESTController(), 'handleEvent']);


$routes = [
    [
        'method'      => 'post',
        'route'       => '/StarFaceVOIP/events/handle',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'handleEvent',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/StarFaceVOIP/eventhandler',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'handleEvent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/StarFaceVOIP/login',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'login',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/StarFaceVOIP/keepalive',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'keepalive',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/StarFaceVOIP/preferences',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'setPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/StarFaceVOIP/preferences',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'getPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/StarFaceVOIP/call',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'initiateCall',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/StarFaceVOIP/call/{callid}',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'hangupCall',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/StarFaceVOIP/events',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'subscribeEvents',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/StarFaceVOIP/events',
        'class'       => StarFaceVOIPKRESTController::class,
        'function'    => 'unsubscribeEvents',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];


$RESTManager->registerRoutes($routes);
