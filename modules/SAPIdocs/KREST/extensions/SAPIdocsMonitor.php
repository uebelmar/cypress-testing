<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SAPIdocs\KREST\controllers\SAPIdocsMonitorController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('sapidocmonitor', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/modules/SAPIdocs/{id}/process',
        'class'       => SAPIdocsMonitorController::class,
        'function'    => 'processIdoc',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
