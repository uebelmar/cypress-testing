<?php
/***** SPICE-HEADER-SPACEHOLDER *****/


use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\Alcatel\KREST\controllers\AlcatelKRESTController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use Slim\Routing\RouteCollectorProxy;

/**
 * ge a rest manager instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the extension
 */
$RESTManager->registerExtension('alcatel', '1.0', ['socketurl_frontend' => SpiceConfig::getInstance()->config['alcatel']['socketurl_frontend']]);

$routes = [
    [
        'method'      => 'post',
        'route'       => '/alcatel/eventhandler',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'handleEvent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/alcatel/login',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'login',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/alcatel/keepalive',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'keepAlive',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/alcatel/dialable',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'dialable',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/alcatelpreferences',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'setPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/alcatelpreferences',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'getPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/alcatel/call',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'initiateCall',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/alcatel/call/{callid}',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'hangupCall',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/alcatel/events/handle',
        'class'       => AlcatelKRESTController::class,
        'function'    => 'handleEvent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
