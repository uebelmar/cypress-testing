<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\EmailSchedules\KREST\controllers\EmailSchedulesKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('emailschedules', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/modules/EmailSchedules/saveSchedule',
        'class'       => EmailSchedulesKRESTController::class,
        'function'    => 'saveSchedule',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/modules/EmailSchedules/saveScheduleFromRelated',
        'class'       => EmailSchedulesKRESTController::class,
        'function'    => 'saveScheduleFromRelated',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/EmailSchedules/checkRelated/{module}/{id}',
        'class'       => EmailSchedulesKRESTController::class,
        'function'    => 'checkRelated',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/Users/{id}/myOpenSchedules',
        'class'       => EmailSchedulesKRESTController::class,
        'function'    => 'getOwnOpenSchedules',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
