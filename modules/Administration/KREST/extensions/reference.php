<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\modules\Administration\KREST\controllers\referenceController;
use SpiceCRM\includes\RESTManager;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('adminreference', '1.0');

/**
 * restrict routes to authenticated users
 */

$routes = [
    [
        'method'      => 'get',
        'route'       => '/reference',
        'class'       => referenceController::class,
        'function'    => 'getCurrentSystemConf',
        'description' => 'get the current system Configuration',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/load/languages/{languages}',
        'class'       => referenceController::class,
        'function'    => 'loadge',
        'description' => 'oad the system language',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/load/configs',
        'class'       => referenceController::class,
        'function'    => 'cleanUpDefaultConf',
        'description' => 'laad and cleanup the default configuration',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

