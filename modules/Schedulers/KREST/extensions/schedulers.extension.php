<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Schedulers\KREST\controllers\SchedulerController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('schedulers', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/Schedulers/jobslist',
        'class'       => SchedulerController::class,
        'function'    => 'ScheduleReturnJobList',
        'description' => 'returns a joblist',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Schedulers/{sid}/runjob',
        'class'       => SchedulerController::class,
        'function'    => 'ScheduleCompleteJob',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Schedulers/{sid}/schedulejob',
        'class'       => SchedulerController::class,
        'function'    => 'ScheduleSubmitJob',
        'description' => 'creates and submits a job',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
