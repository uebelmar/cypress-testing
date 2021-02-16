<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\CompanyCodes\KREST\controllers\CompanyCodesKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('companycodes', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/CompanyCodes',
        'class'       => CompanyCodesKRESTController::class,
        'function'    => 'getCompanyCodes',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
