<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Users\KREST\controllers\UsersPreferencesKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('userpreferences', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/user/{userId}/preferences/{category}',
        'class'       => UsersPreferencesKRESTController::class,
        'function'    => 'getPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/user/{userId}/preferences/{category}{names}',
        'class'       => UsersPreferencesKRESTController::class,
        'function'    => 'getUserPreferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/user/{userId}/preferences/{category}',
        'class'       => UsersPreferencesKRESTController::class,
        'function'    => 'set_user_preferences',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

