<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\WorkflowDefinitions\KREST\controllers\WorkflowDefinitionsKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('workflowdefinitons', '1.0');


$RESTManager->registerExtension('WorkflowDefinitons', '1.0');
$RESTManager->adminAccessOnly('/WorkflowDefinitons/*');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/WorkflowDefinitons/{module}',
        'class'       => WorkflowDefinitionsKRESTController::class,
        'function'    => 'getDefinition',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/WorkflowDefinitons/{module}/{id}',
        'class'       => WorkflowDefinitionsKRESTController::class,
        'function'    => 'setDefinition',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
