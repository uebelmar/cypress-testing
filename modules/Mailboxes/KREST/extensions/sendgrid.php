<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Mailboxes\KREST\controllers\SendgridController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('sendgrid', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/sendgridwebhooks/handler',
        'class'       => SendgridController::class,
        'function'    => 'SendGridHandleEvent',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);


