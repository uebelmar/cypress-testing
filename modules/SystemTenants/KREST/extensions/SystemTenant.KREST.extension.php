<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\ErrorHandlers\BadRequestException;
use SpiceCRM\modules\SystemTenants\KREST\controllers\SystemTenantsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('systemtenants', '1.0');


$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/SystemTenants/{id}/initialize',
        'class'       => SystemTenantsKRESTController::class,
        'function'    => 'initialize',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/SystemTenants/{id}/loaddemodata',
        'class'       => SystemTenantsKRESTController::class,
        'function'    => 'loadDemoData',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
