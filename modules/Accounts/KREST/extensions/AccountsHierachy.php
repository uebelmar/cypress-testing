<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Accounts\KREST\controllers\AccountsHierarchyController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('accounthierachy', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/AccountsHierachy/{id}',
        'class'       => AccountsHierarchyController::class,
        'function'    => 'getAccountHierarchyId',
        'description' => 'get the id of an account',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/AccountsHierachy/{id}/{addfields}',
        'class'       => AccountsHierarchyController::class,
        'function'    => 'getACLAction',
        'description' => 'get the id of an account',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);