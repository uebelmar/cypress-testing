<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Users\KREST\controllers\UsersKRESTController;
use SpiceCRM\includes\authentication\KREST\controllers\UserLoginController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('login', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/login',
        'class'       => UserLoginController::class,
        'function'    => 'getCurrentUserData',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/login',
        'class'       => UserLoginController::class,
        'function'    => 'getCurrentUserData',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/login',
        'class'       => UserLoginController::class,
        'function'    => 'loginDelete',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

