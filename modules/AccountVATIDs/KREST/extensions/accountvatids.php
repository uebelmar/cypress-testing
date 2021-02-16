<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\AccountVATIDs\KREST\controllers\AccountVATIDsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('accountvatids', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/AccountVATIDs/{vatid}',
        'class'       => AccountVATIDsKRESTController::class,
        'function'    => 'GetSoapBody',
        'description' => 'gets the soap body of an curled url',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];


$RESTManager->registerRoutes($routes);