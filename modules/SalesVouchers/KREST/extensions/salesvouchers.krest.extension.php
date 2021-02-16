<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SalesVouchers\KREST\controllers\SalesVouchersKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('salesvouchers', '1.0');


$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/SalesVouchers/buy',
        'class'       => SalesVouchersKRESTController::class,
        'function'    => 'buyVouchers',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

