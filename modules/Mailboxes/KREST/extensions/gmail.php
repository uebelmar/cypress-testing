<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Mailboxes\KREST\controllers\GmailController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('gmail', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/mailboxes/gmail/getMailboxLabels',
        'class'       => GmailController::class,
        'function'    => 'getMailboxLabels',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

