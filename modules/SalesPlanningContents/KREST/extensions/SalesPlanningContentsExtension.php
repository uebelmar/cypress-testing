<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SalesPlanningContents\KREST\controllers\SalesPlanningContentsController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('salesplanningcontents', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/SalesPlanningContents/version/{versionId}/Node/{nodeId}/Content',
        'class'       => SalesPlanningContentsController::class,
        'function'    => 'getNodeContent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/SalesPlanningContents/version/{versionId}/Node/{nodeId}/Update',
        'class'       => SalesPlanningContentsController::class,
        'function'    => 'update',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/SalesPlanningContents/version/{versionId}/Node/{nodeId}/markDone',
        'class'       => SalesPlanningContentsController::class,
        'function'    => 'markDone',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/SalesPlanningContents/version/{versionId}/Node/{nodeId}/unmarkDone',
        'class'       => SalesPlanningContentsController::class,
        'function'    => 'unmarkDone',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/SalesPlanningContents/version/{versionId}/Node/{nodeId}/setNotice',
        'class'       => SalesPlanningContentsController::class,
        'function'    => 'setNotice',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

