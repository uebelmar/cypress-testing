<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceDictionary\KREST\controllers\SpiceDictionaryKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('dictionary', '1.0');

/**
 * routes
 */

$routes = [
    [
        'method'      => 'get',
        'route'       => '/system/dictionary/domains',
        'class'       => SpiceDictionaryKRESTController::class,
        'function'    => 'getDomains',
        'description' => 'get domains',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/system/dictionary/domains',
        'class'       => SpiceDictionaryKRESTController::class,
        'function'    => 'postDomains',
        'description' => 'set domains',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/system/dictionary/domains/appliststrings',
        'class'       => SpiceDictionaryKRESTController::class,
        'function'    => 'getAppListStrings',
        'description' => 'get AppListStrings',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/system/dictionary/definitions',
        'class'       => SpiceDictionaryKRESTController::class,
        'function'    => 'getDefinitions',
        'description' => 'get definitions',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/system/dictionary/definitions',
        'class'       => SpiceDictionaryKRESTController::class,
        'function'    => 'postDefinitions',
        'description' => 'post definitions',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],

];

$RESTManager->registerRoutes($routes);


//$RESTManager->app->group('/system/dictionary', function (RouteCollectorProxy $group) {
//    $group->group('/domains', function (RouteCollectorProxy $group) {
//        $group->get('', [new SpiceDictionaryKRESTController(), 'getDomains']);
//        $group->get('/appliststrings', [new SpiceDictionaryKRESTController(), 'getAppListStrings']);
//        $group->post('', [new SpiceDictionaryKRESTController(), 'postDomains']);
//    });
//    $group->group('/definitions', function (RouteCollectorProxy $group) {
//        $group->get('', [new SpiceDictionaryKRESTController(), 'getDefinitions']);
//        $group->post('', [new SpiceDictionaryKRESTController(), 'postDefinitions']);
//    });
//});
