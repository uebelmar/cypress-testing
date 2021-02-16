<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SystemDeploymentPackages\KREST\controllers\KReleasePackageController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('systemdeploymentpackages', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentpackages',
        'class'       => KReleasePackageController::class,
        'function'    => 'GetDeploymentPackageList',
        'description' => 'get SystemDeploymentPackages list',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/systemdeploymentpackages',
        'class'       => KReleasePackageController::class,
        'function'    => 'SaveRPPackages',
        'description' => 'saves SystemDeploymentPackages',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/systemdeploymentpackages/{id}',
        'class'       => KReleasePackageController::class,
        'function'    => 'MarkPackagesDeleted',
        'description' => 'mark packages as deleted ',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentpackages/statusdom',
        'class'       => KReleasePackageController::class,
        'function'    => 'GetStatusGetStatus',
        'description' => 'KStatusStatus',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentpackages/typedom',
        'class'       => KReleasePackageController::class,
        'function'    => 'GetType',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentpackages/getCRs',
        'class'       => KReleasePackageController::class,
        'function'    => 'KGetCRs',
        'description' => 'get the SystemDeploymentPackages CRs',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentpackages/getCRList',
        'class'       => KReleasePackageController::class,
        'function'    => 'KGetCRList',
        'description' => 'gets the cr list',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentpackages/package',
        'class'       => KReleasePackageController::class,
        'function'    => 'KPackage',
        'description' => 'packages the SystemDeploymentPackages',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentpackages/release/{id}',
        'class'       => KReleasePackageController::class,
        'function'    => 'KReleasePackage',
        'description' => 'release the systemdeploymentpackage',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);

