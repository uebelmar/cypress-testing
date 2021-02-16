<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Meetings\KREST\controllers\MeetingsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('meetings', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/modules/Meetings/{id}/setstatus/{userid}/{status}',
        'class'       => MeetingsKRESTController::class,
        'function'    => 'setStatus',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

