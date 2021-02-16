<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ProjectWBSs\KREST\controllers\ProjectWbsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('projectmanagement', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/projectwbs/my/wbss',
        'class'       => ProjectWbsController::class,
        'function'    => 'GetUserWBS',
        'description' => 'gets the WBS of a user',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/projectwbs/{id}',
        'class'       => ProjectWbsController::class,
        'function'    => 'GetWBSList',
        'description' => 'gets the wbs from the database depending on the id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/projectwbs',
        'class'       => ProjectWbsController::class,
        'function'    => 'SaveWBS',
        'description' => 'saves the wbs',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/projectwbs/{id}',
        'class'       => ProjectWbsController::class,
        'function'    => 'DeleteWBS',
        'description' => 'deletes the wbs',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/ProjectWBSsHierarchy/{projectid}',
        'class'       => ProjectWbsController::class,
        'function'    => 'LinkWBS',
        'description' => 'gets the linkes wbs and maps them to an array',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/ProjectWBSsHierarchy/{projectid}/{addfields}',
        'class'       => ProjectWbsController::class,
        'function'    => 'WBSGetSummaryText',
        'description' => 'gets ',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];



$RESTManager->registerRoutes($routes);
