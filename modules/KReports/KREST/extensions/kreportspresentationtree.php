<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\KReports\Plugins\Presentation\treeview\KReportsPresentationTreeKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('reportingmore', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/KReporter/Tree/{report}/columns',
        'class'       => KReportsPresentationTreeKRESTController::class,
        'function'    => 'buildColumnArray',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/KReporter/Tree/{report}/node/{node}',
        'class'       => KReportsPresentationTreeKRESTController::class,
        'function'    => 'getNode',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

