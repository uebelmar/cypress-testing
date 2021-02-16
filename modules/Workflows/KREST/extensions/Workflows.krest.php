<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Workflows\KREST\controllers\WorkflowKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('workflow', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/Workflows/mytasks',
        'class'       => WorkflowKRESTController::class,
        'function'    => 'getMyTasks',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/Workflows/forparent/{module}/{id}',
        'class'       => WorkflowKRESTController::class,
        'function'    => 'getRelatedWorkflows',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Workflows/settaskstatus/{id}/{status}',
        'class'       => WorkflowKRESTController::class,
        'function'    => 'setTaskStatus',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Workflows/addcomment/{id}',
        'class'       => WorkflowKRESTController::class,
        'function'    => 'addTaskComment',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/Workflows/{id}/close',
        'class'       => WorkflowKRESTController::class,
        'function'    => 'closeWorkflow',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
