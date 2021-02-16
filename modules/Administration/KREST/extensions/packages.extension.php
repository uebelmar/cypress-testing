<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Administration\KREST\controllers\PackageController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('adminpackages', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/packages/repositories',
        'class'       => PackageController::class,
        'function'    => 'getRepositories',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/packages[/{repository}]',
        'class'       => PackageController::class,
        'function'    => 'getPackages',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/packages/package/{package}[/{repository}]',
        'class'       => PackageController::class,
        'function'    => 'loadPackage',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'put',
        'route'       => '/packages/package/{package}[/{repository}]',
        'class'       => PackageController::class,
        'function'    => 'loadPackage',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/packages/package/{package}',
        'class'       => PackageController::class,
        'function'    => 'deletePackage',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/packages/language/{language}[/{repository}]',
        'class'       => PackageController::class,
        'function'    => 'loadLanguage',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'put',
        'route'       => '/packages/language/{language}[/{repository}]',
        'class'       => PackageController::class,
        'function'    => 'loadLanguage',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/packages/language/{language}',
        'class'       => PackageController::class,
        'function'    => 'deleteLanguage',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/packages/language/{language}',
        'class'       => PackageController::class,
        'function'    => 'setDefaultLanguage',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
