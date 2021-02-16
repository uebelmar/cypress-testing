<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SysTrashCan\KREST\controllers\SysTrashCanRecoveryController;
use SpiceCRM\includes\authentication\AuthenticationController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('systrashcan', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/systrashcan',
        'class'       => SysTrashCanRecoveryController::class,
        'function'    => 'getUserTrashRecords',
        'description' => 'Logs inbound Mailgun messages',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systrashcan/related/{transactionid}/{recordid}',
        'class'       => SysTrashCanRecoveryController::class,
        'function'    => 'getRelatedTrashRecords',
        'description' => 'Logs inbound Mailgun messages',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/systrashcan/recover/{id}',
        'class'       => SysTrashCanRecoveryController::class,
        'function'    => 'getRecoveredTrashRecords',
        'description' => 'Logs inbound Mailgun messages',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);