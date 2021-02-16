<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\CampaignTasks\KREST\controllers\CampaignTasksKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('campaigntasks', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/CampaignTasks/{campaignid}/items',
        'class'       => CampaignTasksKRESTController::class,
        'function'    => 'getCampaignTaskItems',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/CampaignTasks/{campaigntaskid}/activate',
        'class'       => CampaignTasksKRESTController::class,
        'function'    => 'activateCampaignTask',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/CampaignTasks/{campaignid}/export',
        'class'       => CampaignTasksKRESTController::class,
        'function'    => 'exportCampaignTask',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/CampaignTasks/{campaigntaskid}/sendtestmail',
        'class'       => CampaignTasksKRESTController::class,
        'function'    => 'sendCampaignTaskTestEmail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/CampaignTasks/{campaigntaskid}/queuemail',
        'class'       => CampaignTasksKRESTController::class,
        'function'    => 'queueCampaignTaskEmail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/CampaignTasks/liveCompile/{module}/{parent}',
        'class'       => CampaignTasksKRESTController::class,
        'function'    => 'liveCompileEmailBody',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/CampaignTasks/export/reports',
        'class'       => CampaignTasksKRESTController::class,
        'function'    => 'getExportReports',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
