<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Calls\KREST\controllers\CallsKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('calls', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/modules/Calls/{id}/setstatus/{userid}/{status}',
        'class'       => CallsKRESTController::class,
        'function'    => 'setStatus',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
