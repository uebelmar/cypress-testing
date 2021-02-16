<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SalesPlanningNodes\KREST\controllers\SalesPlanningNodesController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('salesplanningnodes', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/SalesPlanningNodes/version/{versionId}/CharacteristicList',
        'class'       => SalesPlanningNodesController::class,
        'function'    => 'getCharacteristicSelectionList',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/SalesPlanningNodes/version/{versionId}/NodesList',
        'class'       => SalesPlanningNodesController::class,
        'function'    => 'getNodesList',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/SalesPlanningNodes/version/{versionId}/NodeInfo',
        'class'       => SalesPlanningNodesController::class,
        'function'    => 'getNodeInfo',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
