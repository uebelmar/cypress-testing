<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\DialogMail\KREST\controllers\DialogMailController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('dialogmail', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/DialogMail/DialogMail/Contact/{id}/mails',
        'class'       => DialogMailController::class,
        'function'    => 'getUsersMails',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/DialogMail/DialogMail/ProspectLists/{id}/transferToDialogMail',
        'class'       => DialogMailController::class,
        'function'    => 'prospectListToDialogMail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/DialogMail/DialogMail/ProspectLists/{id}/initialize',
        'class'       => DialogMailController::class,
        'function'    => 'getListStatistics',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/DialogMail/DialogMail/CampaignTasks/{id}/initialize',
        'class'       => DialogMailController::class,
        'function'    => 'getCampaignTaskStatistics',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/DialogMail/DialogMail/CampaignTasks/{id}/transferToDialogMail',
        'class'       => DialogMailController::class,
        'function'    => 'campaignTaskToDialogMail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
