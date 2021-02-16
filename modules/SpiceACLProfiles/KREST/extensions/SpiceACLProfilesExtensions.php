<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SpiceACLProfiles\KREST\controllers\SpiceACLProfilesController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('aclmanager', '1.0');


$routes = [

    [
        'method'      => 'get',
        'route'       => '/spiceaclprofiles/foruser/{userrid}',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclProfilesForUser',
        'description' => 'Get SpiceACL Profiles for user',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclprofiles/{id}/activate',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclProfilesActivate',
        'description' => 'SpiceACL Profile Activate',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclprofiles/{id}/deactivate',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclProfilesDeactivate',
        'description' => 'SpiceACL Profile Deactivate',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclprofiles/{id}/aclobjects',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclProfilesObjects',
        'description' => 'SpiceACL Profile Objects',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclprofiles/{id}/aclobjects/{objectid}',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclProfilesObject',
        'description' => 'SpiceACL Profile Object',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclprofiles/{id}/aclobjects/{objectid}',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclProfilesObjectDelete',
        'description' => 'SpiceACL Profile Object Delete',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclprofiles/{id}/aclusers',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclProfilesUsers',
        'description' => 'SpiceACL Profile Users',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclprofiles/{id}/aclusers',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclAddProfilesUsers',
        'description' => 'SpiceACL Add Profile Users',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclprofiles/{id}/aclusers/{userid}',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclAddProfilesUser',
        'description' => 'SpiceACL Add Profile User',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclprofiles/{id}/aclusers/{userid}',
        'class'       => SpiceACLProfilesController::class,
        'function'    => 'spiceAclDeleteProfilesUser',
        'description' => 'SpiceACL Delete Profile User',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],

];

$RESTManager->registerRoutes($routes);
