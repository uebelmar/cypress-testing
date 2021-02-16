<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Mailboxes\KREST\controllers\MailgunWebhooksController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('mailgun', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/mailgunwebhooks/inbound',
        'class'       => MailgunWebhooksController::class,
        'function'    => 'inbound',
        'description' => 'Logs inbound Mailgun messages',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/mailgunwebhooks/handler',
        'class'       => MailgunWebhooksController::class,
        'function'    => 'handle',
        'description' => 'Handles inbound Mailgun messages',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
