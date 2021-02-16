<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SystemDeploymentSystems\KREST\controllers\KDeploymentController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('kdeployment', '1.0');


// ToDo: currently not implemented
return;
/*
$routes =[
    [
        'method'      => 'get',
        'route'       => '/kdeployment/systems',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetSystem',
        'description' => 'get system infos',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/remoteReleasePackages',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetRemotePackage',
        'description' => 'get the remote packages',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/kdeployment/fetchReleasePackage/{id}/{system}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDFetchRemotePackage',
        'description' => 'fetches remote packages',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/fetchReleasePackageContent/{id}/{system}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDFetchPackageContent',
        'description' => 'fetches the content of a package',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/releasePackage/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDReleasePackage',
        'description' => 'releases a package',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/checkAccessPackage/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDCheckAccess',
        'description' => 'checks access for a user',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/backupPackage/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDWriteBackup',
        'description' => 'write a backup in the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment//writeFilesPackage/{id}}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDWriteLocalFiles',
        'description' => 'writes in a local file',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/remoteWriteFilesPackage/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDWriteRemoteFiles',
        'description' => 'writes in a remote file',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/writeDBPackage/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDWriteDb',
        'description' => 'writes in the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/markPackageDeployed/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDMarkDeployed',
        'description' => 'mark beans as deployed',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/rollbackPackage/{id',
        'class'       => KDeploymentController::class,
        'function'    => 'KDRollback',
        'description' => 'make a rollback on the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/repairPackage/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDRepairLocal',
        'description' => 'repairs a local database link',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/remoteRepairPackage/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDRemoteRepair',
        'description' => 'repairs remote database links',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/releasePackageHistory/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDReleasePackageHistory',
        'description' => 'gets the package history',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/remoteReleasePackageStatusUpdate/{package}/{status}/{system}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDUpdateRemotePackage',
        'description' => 'updates the remote packages',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/localReleasePackages',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetPackages',
        'description' => 'get packages from the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/sourceSystems',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetSourceSystem',
        'description' => 'gets systeminfos from normal and linked beans',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/kdeployment/delSystem/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDDeleteSystem',
        'description' => 'deletes the system configuration',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/addSystemLink/{id}/{link}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDInsertSystemLink',
        'description' => 'inserts a systemlink into systemdeploymentsystems',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/kdeployment/testConnection',
        'class'       => KDeploymentController::class,
        'function'    => 'KDTestConnection',
        'description' => 'tests the connection',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/kdeployment/distribute',
        'class'       => KDeploymentController::class,
        'function'    => 'KDImportSystem',
        'description' => 'updates the systemdeploymentsystems table',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/kdeployment/distribute/{id}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetDistribute',
        'description' => 'gets the distribution list from the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/getRepositories',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetGitRepo',
        'description' => 'creates a list from the user git repos',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/latestSwVersions/{swpacks}',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetSwVersion',
        'description' => 'gets the software version from the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/kdeployment/appConfig',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetAppConfig',
        'description' => 'get the app config from the database',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/kdeployment/RPfromZIPlocal',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetZipFromForm',
        'description' => 'receives a Zip file from a from',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/kdeployment/RPfromZIPremote',
        'class'       => KDeploymentController::class,
        'function'    => 'KDGetZipFromRest',
        'description' => 'gets a zip file from Rest',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);*/
