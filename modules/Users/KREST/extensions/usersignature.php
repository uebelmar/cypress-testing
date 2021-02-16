<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Users\KREST\controllers\UserSignatureController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('usersignatures', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => 'module/Users/{id}/signature',
        'class'       => UserSignatureController::class,
        'function'    => 'GetUserSignature',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => 'module/Users/{id}/signature',
        'class'       => UserSignatureController::class,
        'function'    => 'SetUserSignature',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
