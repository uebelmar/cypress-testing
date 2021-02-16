<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\CleverReach\KREST\controllers\CleverReachController;
use Slim\Routing\RouteCollectorProxy;

$RESTManager = RESTManager::getInstance();


/**
 * register Extension
 */
$RESTManager->registerExtension('cleverreach', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/CleverReach/ProspectLists/{id}/initialize',
        'class'       => CleverReachController::class,
        'function'    => 'getListStatistics',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/CleverReach/ProspectLists/{id}',
        'class'       => CleverReachController::class,
        'function'    => 'transferToCleverReach',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/CleverReach/CampaignTasks/{id}initialize',
        'class'       => CleverReachController::class,
        'function'    => 'getCampaignTaskStatistics',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/CleverReach/CampaignTasks/{id}/transferToCleverReach',
        'class'       => CleverReachController::class,
        'function'    => 'campaignTaskToCleverReach',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/CleverReach/CampaignTasks/{id}/writeMailing',
        'class'       => CleverReachController::class,
        'function'    => 'getTargetGroups',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/CleverReach/CampaignTasks/{id}/sendMailing',
        'class'       => CleverReachController::class,
        'function'    => 'sendMailings',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/CleverReach/CampaignTasks/{id}stats',
        'class'       => CleverReachController::class,
        'function'    => 'getMailingStats',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/CleverReach/CampaignTasks/{id}/report',
        'class'       => CleverReachController::class,
        'function'    => 'getReportState',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
