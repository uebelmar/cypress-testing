<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\includes\SpiceTags\KREST\controllers\SpiceTagsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('spicetags', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/SpiceTags/{query}',
        'class'       => SpiceTagsKRESTController::class,
        'function'    => 'searchTags',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/SpiceTags',
        'class'       => SpiceTagsKRESTController::class,
        'function'    => 'searchPostTags',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
