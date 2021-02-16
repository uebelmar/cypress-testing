<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\GoogleCalendar\KREST\controllers\GoogleCalendarKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('gsuitewebhooks', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/gsuitewebhooks/handler',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'handle',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

