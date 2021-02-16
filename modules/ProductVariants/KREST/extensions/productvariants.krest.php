<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ProductVariants\KREST\controllers\ProductVariantsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('productvariants', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/productvariants/productgroup/{id}',
        'class'       => ProductVariantsController::class,
        'function'    => 'ProductCleanUpDataType',
        'description' => 'changes the set of value by datatype',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/productvariants/product/{id}',
        'class'       => ProductVariantsController::class,
        'function'    => 'ProductSearchParam',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);

