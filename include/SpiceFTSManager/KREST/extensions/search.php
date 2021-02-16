<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceFTSManager\KREST\controllers\FTSManagerRESTController;
use SpiceCRM\includes\SpiceFTSManager\SpiceFTSHandler;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('search', '1.0');

/**
 * routes
 */
$routes = [
    [
        'method'      => 'post',
        'route'       => '/search',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'search',
        'description' => 'process the search',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/search/phonenumber',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'searchPhone',
        'description' => 'process search based on a phonenumber',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/search/export',
        'class'       => FTSManagerRESTController::class,
        'function'    => 'export',
        'description' => 'process the export for an fts request',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
