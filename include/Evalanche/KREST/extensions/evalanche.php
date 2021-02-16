<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\Evalanche\KREST\controllers\EvalancheController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('evalanche', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/Evalanche/CampaignTasks/{id}/templates',
        'class'       => EvalancheController::class,
        'function'    => 'getTemplates',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/Evalanche/CampaignTasks/{id}/report',
        'class'       => EvalancheController::class,
        'function'    => 'getMailingStats',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/EvalancheCampaignTasks/{id}/sendmailing',
        'class'       => EvalancheController::class,
        'function'    => 'sendMailing',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Evalanche/ProspectLists/{id}/sync',
        'class'       => EvalancheController::class,
        'function'    => 'synchronizeTargetLists',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Evalanche/ProspectLists/{id}/stats',
        'class'       => EvalancheController::class,
        'function'    => 'getProspectListStatistic',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Evalanche/CampaignTasks/{id}/sync',
        'class'       => EvalancheController::class,
        'function'    => 'campaignTaskToEvalanche',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
