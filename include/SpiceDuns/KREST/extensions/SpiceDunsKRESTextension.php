<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceDuns\KREST\controllers\SpiceDunsKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('duns', '1.0');

/**
 * routes
 */
$routes = [
    [
        'method'      => 'get',
        'route'       => '/SpiceDuns',
        'class'       => SpiceDunsKRESTController::class,
        'function'    => 'getDuns',
        'description' => 'get DUNS',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
