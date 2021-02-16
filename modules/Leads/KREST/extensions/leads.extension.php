<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Leads\KREST\controllers\LeadsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('leads', '2.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Leads/createFromForm',
        'class'       => LeadsKRESTController::class,
        'function'    => 'createFromForm',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

