<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SalesPlanningCharacteristics\KREST\controllers\SalesPlanningCharacteristicsController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('salesplanningcharacteristics', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/SalesPlanningCharacteristics/CharacteristicValues/{characteristicId}',
        'class'       => SalesPlanningCharacteristicsController::class,
        'function'    => 'getCharacteristicValues',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
