<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ProjectPlannedActivities\KREST\controllers\ProjectPlannedActivitiesKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('projectplannedactivities', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/ProjectPlannedActivities/my/open',
        'class'       => ProjectPlannedActivitiesKRESTController::class,
        'function'    => 'getMyOpenActivities',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
