<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceReminders\KREST\controllers\SpiceRemindersKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('spicereminders', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/SpiceReminders',
        'class'       => SpiceRemindersKRESTController::class,
        'function'    => 'getReminders',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/SpiceReminders/{module}/{id}/{date}',
        'class'       => SpiceRemindersKRESTController::class,
        'function'    => 'addReminder',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/SpiceReminders/{module}/{id}',
        'class'       => SpiceRemindersKRESTController::class,
        'function'    => 'deleteReminder',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
