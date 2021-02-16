<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\UserQuotas\KREST\controllers\QuotaManagerController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('userquotas', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/quotamanager/users',
        'class'       => QuotaManagerController::class,
        'function'    => 'GetQuotaUser',
        'description' => 'gets a quota User',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/quotamanager/quotas/{year}',
        'class'       => QuotaManagerController::class,
        'function'    => 'GetQuota',
        'description' => 'gets a quota',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/quotamanager/quota/{userid}/{year}/{period}/{quota}',
        'class'       => QuotaManagerController::class,
        'function'    => 'SetQuota',
        'description' => 'sets a quota',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/quotamanager/quota/{userid}/{year}/{period}',
        'class'       => QuotaManagerController::class,
        'function'    => 'DeleteQuota',
        'description' => 'deletes a quota',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

