<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\KReports\KREST\controllers\KReportsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('kreports', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/KReports/published/{type}',
        'class'       => KReportsKRESTController::class,
        'function'    => 'getPublishedKReports',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

