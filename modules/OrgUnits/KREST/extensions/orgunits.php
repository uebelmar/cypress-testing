<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\OrgUnits\KREST\controllers\OrgUnitsController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('orgunits', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/modules/OrgUnits',
        'class'       => OrgUnitsController::class,
        'function'    => 'GetBeanFullList',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
