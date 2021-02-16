<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Trackers\KREST\controllers\TrackersKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('trackers', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/modules/Trackers/recent',
        'class'       => TrackersKRESTController::class,
        'function'    => 'getRecent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

