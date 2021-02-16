<?php

/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;

use SpiceCRM\modules\Documents\KREST\controllers\DocumentsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('documents', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Documents/{id}/revisionFromBase64',
        'class'       => DocumentsKRESTController::class,
        'function'    => 'revisionFromBase64',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

