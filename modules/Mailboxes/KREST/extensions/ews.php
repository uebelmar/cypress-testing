<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Mailboxes\KREST\controllers\EwsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('ews', '1.0');


$routes = [
    [
        'method'      => 'post',
        'route'       => '/ewswebhooks/mailbox/{mailboxId}',
        'class'       => EwsController::class,
        'function'    => 'handle',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/mailboxes/ews/getmailboxfolders',
        'class'       => EwsController::class,
        'function'    => 'getMailboxFolders',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);


