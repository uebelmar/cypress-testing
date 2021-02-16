<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Administration\KREST\controllers\CleanUpController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('admincleanup', '1.0');

/**
 * routes
 */
$routes = [
    [
        'method'      => 'get',
        'route'       => '/cleanup/configs/check/incomplete[/{scope}]',
        'class'       => CleanUpController::class,
        'function'    => 'getIncompleteRecords',
        'description' => 'get Incomplete Records',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/cleanup/configs/check/unused[/{scope}]',
        'class'       => CleanUpController::class,
        'function'    => 'getUnusedRecords',
        'description' => 'get unused Records',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
// no function defined in CleanUpHandler, therefore nothing to return by this route
//    [
//        'method'      => 'get',
//        'route'       => '/cleanup/configs/check/duplications[/{scope}]',
//        'class'       => CleanUpController::class,
//        'function'    => 'getDuplications',
//        'description' => 'get duplications',
//        'options'     => ['noAuth' => false, 'adminOnly' => true],
//    ],
    [
        'method'      => 'get',
        'route'       => '/cleanup/stylecache',
        'class'       => CleanUpController::class,
        'function'    => 'cleanDompdfStyleCacheFile',
        'description' => 'clean dompdf caches styles',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
];

$RESTManager->registerRoutes($routes);

//$handler = new CleanUpHandler();
//
//$RESTManager->app->group('/cleanup', function(RouteCollectorProxy $group) use($handler) {
//    $group->group('/configs', function(RouteCollectorProxy $group) use($handler) {
//        $group->group('/check', function(RouteCollectorProxy $group) use($handler) {
//            $group->get('/incomplete[/{scope}]', function ($req, $res, $args) use($handler) {
//                if($args['scope']) {
//                    $handler->scope = $args['scope'];
//                }
//                return $res->withJson($handler->getIncompleteRecords());
//            });
//
//            $group->get('/unused[/{scope}]', function ($req, $res, $args) use($handler) {
//                if($args['scope']) {
//                    $handler->scope = $args['scope'];
//                }
//                return $res->withJson($handler->getUnusedRecords());
//            });
//
//            $group->get('/duplications[/{scope}]', function ($req, $res, $args) use($handler) {
//                if($args['scope']) {
//                    $handler->scope = $args['scope'];
//                }
//            });
//        });
//    });
//    $group->get('/stylecache', function ($req, $res, $args) use($handler) {
//        return $res->withJson($handler->cleanDompdfStyleCacheFile());
//    });
//});
