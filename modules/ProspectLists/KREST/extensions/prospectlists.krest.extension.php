<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ProspectLists\KREST\controllers\ProspectListsKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('prospectlists', '1.0');
$routes = [
    [
        'method'      => 'post',
        'route'       => '/modules/ProspectLists/createfromlist/{listid}',
        'class'       => ProspectListsKRESTController::class,
        'function'    => 'createFromListId',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/modules/ProspectLists/exportFromList',
        'class'       => ProspectListsKRESTController::class,
        'function'    => 'exportFromList',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

