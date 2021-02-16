<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Mailboxes\KREST\controllers\MailboxesController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('mailboxmanager', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/mailboxes/test',
        'class'       => MailboxesController::class,
        'function'    => 'testConnection',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/mailboxes/transports',
        'class'       => MailboxesController::class,
        'function'    => 'getMailboxTransports',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/mailboxes/getmailboxprocessors',
        'class'       => MailboxesController::class,
        'function'    => 'getMailboxProcessors',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/mailboxes/sendmail',
        'class'       => MailboxesController::class,
        'function'    => 'sendMail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/mailboxes/getmailboxes',
        'class'       => MailboxesController::class,
        'function'    => 'getMailboxes',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/mailboxes/setdefaultmailbox',
        'class'       => MailboxesController::class,
        'function'    => 'setDefaultMailbox',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

