<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\modules\CampaignLog\KREST\controllers\CampaignLogController;
use SpiceCRM\includes\RESTManager;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('telecockpit', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/CampaignLog/{campaignlogid}/{status}',
        'class'       => CampaignLogController::class,
        'function'    => 'GetCampaignLogByStatus',
        'description' => 'gets campaign logs by status',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
