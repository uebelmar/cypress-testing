<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Accounts\KREST\controllers\AccountsVIESController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('accountvies', '1.0');

/**
 * restrict routes to authenticated users
 */
$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/Accounts/VIES/{vatid}',
        'class'       => AccountsVIESController::class,
        'function'    => 'getVatResponse',
        'description' => 'get the response of an url',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);