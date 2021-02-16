<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\KREST\controllers\coreController;

$RESTManager = RESTManager::getInstance();

/**
 * register the extension
 */

$RESTManager->registerExtension('core', '2.0', ['edit_mode' => SpiceConfig::getInstance()->config['workbench_edit_mode']['mode'] ?: 'custom']);

$routes = [
    [
        'method'      => 'get',
        'route'       => '/',
        'class'       => coreController::class,
        'function'    => 'getExtensions',
        'description' => 'get the loaded Extensions',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/sysinfo',
        'class'       => coreController::class,
        'function'    => 'getSysinfo',
        'description' => 'get vital sysinfo for the startup',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/language',
        'class'       => coreController::class,
        'function'    => 'getLanguage',
        'description' => 'routes for the language',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    // begin workaround when {language} is empty and the / for the route is set
    [
        'method'      => 'get',
        'route'       => '/language/',
        'class'       => coreController::class,
        'function'    => 'getLanguage',
        'description' => 'routes for the language',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    // end
    [
        'method'      => 'get',
        'route'       => '/language/{language}',
        'class'       => coreController::class,
        'function'    => 'getLanguage',
        'description' => 'routes for the language',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/system/guid',
        'class'       => coreController::class,
        'function'    => 'generateGuid',
        'description' => 'helper to generate a GUID',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/tmpfile',
        'class'       => coreController::class,
        'function'    => 'storeTmpFile',
        'description' => 'called from teh proxy to store a temp file storeTmpFile',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/httperrors',
        'class'       => coreController::class,
        'function'    => 'postHttpErrors',
        'description' => 'logs http errors',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/shorturl/{key}',
        'class'       => coreController::class,
        'function'    => 'getRedirection',
        'description' => 'get redirection data for a short url',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
