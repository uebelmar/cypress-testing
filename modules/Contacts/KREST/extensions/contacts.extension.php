<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Contacts\KREST\controllers\ContactsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('exchange', '1.0');

$routes = [
    [
        'method'      => 'put',
        'route'       => '/module/Contacts/{id}/exchangeSync',
        'class'       => ContactsController::class,
        'function'    => 'ewsSync',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/Contacts/{id}/exchangeSync',
        'class'       => ContactsController::class,
        'function'    => 'ewsDelete',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
