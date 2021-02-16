<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SalesDocs\KREST\controllers\SalesDocsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('salesdocuments', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/SalesDocs/{id}/reject',
        'class'       => SalesDocsKRESTController::class,
        'function'    => 'rejectDocument',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/SalesDocs/{id}/convert/{targettype}',
        'class'       => SalesDocsKRESTController::class,
        'function'    => 'convertDocument',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

