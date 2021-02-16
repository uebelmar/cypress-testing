 <?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceFTSManager\KREST\controllers\FTSManagerRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('adminftsmanager', '1.0');

/**
 * routes
 */
$routes = [
    [
        'method'      => 'get',
        'route'       => '/ftsmanager/core/modules',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'getModules',
        'description' => 'get modules',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/ftsmanager/core/index',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'getIndex',
        'description' => 'get index',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/ftsmanager/core/nodes',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'getNodes',
        'description' => 'get nodes',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/ftsmanager/core/fields',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'getFields',
        'description' => 'get fields',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/ftsmanager/core/analyzers',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'getAnalyzers',
        'description' => 'get analyzers',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/ftsmanager/core/initialize',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'initialize',
        'description' => 'initialize',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/ftsmanager/{module}/fields',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'getFTSFields',
        'description' => 'get fts fields',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/ftsmanager/{module}/settings',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'getFTSSettings',
        'description' => 'get fts settings for specific module',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/ftsmanager/{module}',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'deleteIndexSettings',
        'description' => 'delete fts settings for specific module',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/ftsmanager/{module}',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'setFTSFields',
        'description' => 'set fts fields for specific module',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/ftsmanager/{module}/index',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'index',
        'description' => 'index data',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/ftsmanager/{module}/index',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'deleteIndex',
        'description' => 'delete index',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/ftsmanager/{module}/index/reset',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'resetIndex',
        'description' => 'reset index',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
];

$RESTManager->registerRoutes($routes);
