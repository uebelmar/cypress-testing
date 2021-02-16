<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\modules\Potentials\KREST\controllers\PotentialsKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('potentialmanagement', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/Potentials/uncaptured/{companycode}/{accountid}',
        'class'       => PotentialsKRESTController::class,
        'function'    => 'getRevenues',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

