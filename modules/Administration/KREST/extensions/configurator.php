<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Administration\KREST\controllers\ConfiguratorController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('adminconfigurator', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/configurator/editor/{category}',
        'class'       => ConfiguratorController::class,
        'function'    => 'CheckForConfig',
        'description' => 'checks if an config exists if not create an stdclass',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/configurator/editor/{category}',
        'class'       => ConfiguratorController::class,
        'function'    => 'WriteConfToDb',
        'description' => 'writes not forbidden categories to the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/configurator/entries/{table}',
        'class'       => ConfiguratorController::class,
        'function'    => 'ConvertToHTMLDecoded',
        'description' => 'converts the arguments to an html decoded value',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/configurator/{table}/{id}',
        'class'       => ConfiguratorController::class,
        'function'    => 'CheckMetaData',
        'description' => 'checks the metadata and handles them',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/configurator/{table}/{id}',
        'class'       => ConfiguratorController::class,
        'function'    => 'WriteConfig',
        'description' => 'writes config to database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/configurator/update',
        'class'       => ConfiguratorController::class,
        'function'    => 'ConfigMergeArrays',
        'description' => 'merges the postbody and postparams arrays together',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/configurator/load',
        'class'       => ConfiguratorController::class,
        'function'    => 'LoadDefaultConfig',
        'description' => 'loads clears the default config',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/configurator/objectrepository',
        'class'       => ConfiguratorController::class,
        'function'    => 'ConcatRepoQuery',
        'description' => 'concats the repository queries together',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],

];

$RESTManager->registerRoutes($routes);

