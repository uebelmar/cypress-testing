<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Mailboxes\KREST\controllers\TwilioController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('twilio', '1.0');


$routes = [
    [
        'method'      => 'post',
        'route'       => '/twiliowebhooks/status',
        'class'       => TwilioController::class,
        'function'    => 'updateStatus',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/twiliowebhooks/inbound',
        'class'       => TwilioController::class,
        'function'    => 'saveInboundMessage',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

