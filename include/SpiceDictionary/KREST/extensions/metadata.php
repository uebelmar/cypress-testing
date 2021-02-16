<?php

/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceDictionary\KREST\controllers\MetadataRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('metadata', '1.0');


/**
 * routes
 */

$routes = [
    [
        'method'      => 'get',
        'route'       => '/metadata/modules',
        'class'       => MetadataRESTController::class,
        'function'    => 'getModules',
        'description' => 'get modules',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/metadata/vardefs/{module}',
        'class'       => MetadataRESTController::class,
        'function'    => 'getVarDefsForModule',
        'description' => 'get vardefs for module',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
];
$RESTManager->registerRoutes($routes);

//$KRESTModuleHandler = new ModuleHandler($RESTManager->app);
//$RESTManager->app->group('/metadata', function (RouteCollectorProxy $group) use ( $KRESTModuleHandler) {
//    $group->get('/modules', function($req, $res, $args) use ($KRESTModuleHandler) {
//        return $res->withJson($KRESTModuleHandler->get_modules());
//    });
//    $group->get('/vardefs/{module}', function($req, $res, $args) use ($KRESTModuleHandler) {
//        $bean = BeanFactory::getBean($args['module']);
//        return $res->withJson($bean->field_name_map);
//    });
//});
