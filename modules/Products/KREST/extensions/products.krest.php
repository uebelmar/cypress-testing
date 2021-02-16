<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Products\KREST\controllers\ProductKRESTController;


/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('productmanagement', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/products/{id}/productattributes',
        'class'       => ProductKRESTController::class,
        'function'    => 'ProductMapValidation',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/products/{id}/productattributes/direct',
        'class'       => ProductKRESTController::class,
        'function'    => 'ProductGetValue',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/products/{id}/productattributes/textgenerator',
        'class'       => ProductKRESTController::class,
        'function'    => 'ProductCleanValue',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);
