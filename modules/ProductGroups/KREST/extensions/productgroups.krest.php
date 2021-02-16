<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ProductGroups\KREST\controllers\ProductGroupsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('productgroups', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/productgroups/tree',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'getTreeNodes',
        'description' => 'load report categories for the ui loadtasks',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/productgroups/tree/{nodeid}',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'getTreeNodes',
        'description' => 'load report categories for the ui loadtasks',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/productgroups/{id}/productattributes',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'ProductWriteValidation',
        'description' => 'links validation values to a product',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/productgroups/{id}/productattributes/direct',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'ProductGetRelatedAttributes',
        'description' => 'get the related attributes of a product',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/ProductGroups/{id}/productattributes/textgenerator',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'ProductParseTextDataType',
        'description' => 'changes text datatypes to another',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/ProductGroups/{id}/productattributes/textgenerator',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'ProductWriteTextProductBody',
        'description' => 'writes a textbody in the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/ProductGroups/{id}/productattributes/longtextgenerator',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'ProductParseLongTextDataType',
        'description' => 'changes datatypes to another',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/ProductGroups/{id}/productattributes/longtextgenerator',
        'class'       => ProductGroupsKRESTController::class,
        'function'    => 'ProductWriteLongTextProductBody',
        'description' => 'inserts a new body in the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],

];
$RESTManager->registerRoutes($routes);

