<?php

/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Contacts\KREST\controllers\ContactsPortalController;



/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('portal', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/portal/{id}/portalaccess',
        'class'       => ContactsPortalController::class,
        'function'    => 'ContactsContacHandling',
        'description' => 'handles the creting and updating of contacts',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/portal/{contactId}/portalaccess/{action:create|update}',
        'class'       => ContactsPortalController::class,
        'function'    => 'ConctactsUserHandling',
        'description' => 'handles the creating and updating of users',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/portal/{contactId}/testUsername',
        'class'       => ContactsPortalController::class,
        'function'    => 'ContactsFetchOneContact',
        'description' => 'fetches a ccontact form the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

