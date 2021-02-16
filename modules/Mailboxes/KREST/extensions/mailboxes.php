<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Mailboxes\KREST\controllers\MailboxesController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('mailboxes', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/modules/Mailboxes/{id}/fetchemails',
        'class'       => MailboxesController::class,
        'function'    => 'fetchEmails',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/Mailboxes/dashlet',
        'class'       => MailboxesController::class,
        'function'    => 'getMailboxesForDashlet',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/Mailboxes/dashlet/{type}',
        'class'       => MailboxesController::class,
        'function'    => 'getMailboxesForDashlet',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

