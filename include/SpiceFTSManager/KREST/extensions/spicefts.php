 <?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceFTSManager\KREST\controllers\SpiceFTSRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('fts', '1.0');

/**
 * routes
 */
$routes = [
    [
        'method'      => 'get',
        'route'       => '/fts/globalsearch',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getGlobalSearchResults',
        'description' => 'get global search results',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/fts/globalsearch',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getGlobalSearchResults',
        'description' => 'get global search results',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/globalsearch/{module}',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getSearchResultsForModuleByGet',
        'description' => 'get module search results',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/fts/globalsearch/{module}',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getSearchResultsForModuleByPost',
        'description' => 'get module search results',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/globalsearch/{module}/{searchterm}',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getGlobalSearchResultsForModuleSearchTermByGet',
        'description' => 'get search term results',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/fts/globalsearch/{module}/{searchterm}',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getGlobalSearchResultsForModuleSearchTermByPost',
        'description' => 'get search term results',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/searchmodules',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getGlobalSearchModules',
        'description' => 'all global search enabled modules',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/searchterm/{searchterm}',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'searchTerm',
        'description' => 'search in a module',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/check',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'check',
        'description' => 'check FTS connection',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/status',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getStatus',
        'description' => 'get some basic stats',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/stats',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'getStats',
        'description' => 'get elasticsearch stats',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'put',
        'route'       => '/fts/unblock}',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'unblock',
        'description' => 'get indexes settings',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/fts/fields/{module}',
        'class'       => SpiceFTSRESTController::class,
        'function'    => 'fields/{module}',
        'description' => 'getFTSModuleFields',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
];

$RESTManager->registerRoutes($routes);
