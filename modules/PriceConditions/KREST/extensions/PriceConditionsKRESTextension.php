<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\modules\PriceConditions\KREST\controllers\PriceConditionsKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('priceconditions', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/PriceConditions/list/{module}/{id}',
        'class'       => PriceConditionsKRESTController::class,
        'function'    => 'getCustomerConditions',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/PriceConditions/configuration',
        'class'       => PriceConditionsKRESTController::class,
        'function'    => 'getConfiguration',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

