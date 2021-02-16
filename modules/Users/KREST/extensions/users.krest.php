<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Users\KREST\controllers\UsersKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('users', '1.0');



$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}',
        'class'       => UsersKRESTController::class,
        'function'    => 'saveUser',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/Users/{id}',
        'class'       => UsersKRESTController::class,
        'function'    => 'setUserInactive',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/Users/{id}/deactivate',
        'class'       => UsersKRESTController::class,
        'function'    => 'getDeactivateUserStats',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}/deactivate',
        'class'       => UsersKRESTController::class,
        'function'    => 'deactivateUser',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}/activate',
        'class'       => UsersKRESTController::class,
        'function'    => 'activateUser',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

