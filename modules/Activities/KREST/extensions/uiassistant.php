<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Activities\KREST\controllers\UiAssistantController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('uiassistant', '1.0');

/**
 * restrict routes to authenticated users
 */

$routes = [
    [
        'method'      => 'get',
        'route'       => '/assistant/list',
        'class'       => UiAssistantController::class,
        'function'    => 'getUiAssist',
        'description' => 'Logs inbound Mailgun messages',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);
