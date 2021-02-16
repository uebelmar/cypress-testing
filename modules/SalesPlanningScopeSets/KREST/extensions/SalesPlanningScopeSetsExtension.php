<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SalesPlanningScopeSets\KREST\controllers\SalesPlanningScopeSetsController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('salesplanningscopesets', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/SalesPlanningScopeSets/getScopeCharacteristics/{scopeSetId}',
        'class'       => SalesPlanningScopeSetsController::class,
        'function'    => 'getScopeCharacteristics',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/SalesPlanningScopeSets/{scopeSetId}/createFromKReport/{reportId}',
        'class'       => SalesPlanningScopeSetsController::class,
        'function'    => 'createFromKReport',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
