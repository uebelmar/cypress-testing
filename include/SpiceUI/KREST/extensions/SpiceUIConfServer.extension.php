<?php

/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceUI\KREST\controllers\SpiceUIConfServerController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('configrepository', '1.0');

/**
 * restrict routes to authenticated users and only if the system acts as repository
 * also enable general unautohrized access if system is public repository
 */
#global $sugar_config;
//todo clarify ... do we need this if? should we add a auth middleware?
if((SpiceConfig::getInstance()->config['configrepository']['public'] !== true) || SpiceConfig::getInstance()->config['configrepository']['enabled'] !== true) return;


$routes = [
    [
        'method'      => 'get',
        'route'       => '/config',
        'class'       => SpiceUIConfServerController::class,
        'function'    => 'getAvailable',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/repositoryitems',
        'class'       => SpiceUIConfServerController::class,
        'function'    => 'getRepositoryItems',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/repositorymodules',
        'class'       => SpiceUIConfServerController::class,
        'function'    => 'getRepositoryModules',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/{packages}/{version}',
        'class'       => SpiceUIConfServerController::class,
        'function'    => 'getConfig',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/language/{language}/{package}/{version}',
        'class'       => SpiceUIConfServerController::class,
        'function'    => 'getLanguageLabels',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => 'config/language/{language}',
        'class'       => SpiceUIConfServerController::class,
        'function'    => 'getLanguageLabels',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

