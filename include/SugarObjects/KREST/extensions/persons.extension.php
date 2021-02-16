<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SugarObjects\KREST\controllers\PersonsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('person', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/{module}/convert/{id}/to/VCard',
        'class'       => PersonsController::class,
        'function'    => 'convertToVCard',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
