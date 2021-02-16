<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SpiceACLTerritories\KREST\controllers\SpiceACLTerritoriesController;
use SpiceCRM\modules\SpiceACLTerritories\SpiceACLTerritoriesRESTHandler;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('aclterritorries', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/territories',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'territories',
        'description' => 'Get Territories',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/territories/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'territory',
        'description' => 'Get Territory',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/territories/hash/{hash_id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'territoryHash',
        'description' => 'Get Territory Hash',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritories',
        'description' => 'Get SpiceACL Territores',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgelements',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesOrgElements',
        'description' => 'SpiceACL Territores Org Elements',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclterritories/core/orgelementvalues',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesSetOrgElementValues',
        'description' => 'SpiceACL Territores Set Org Element Values',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclterritories/core/orgobjecttypeelements',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesSetOrgObjectTypeElements',
        'description' => 'SpiceACL Territores Set Org Object Type Elements',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclterritories/core/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesDeleteOrgElements',
        'description' => 'SpiceACL Territores Delete Org Elements',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgelementvalues/{orgelementid}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesGetOrgElementValues',
        'description' => 'SpiceACL Territores Get Org Element Values',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclterritories/core/orgelementvalues/{spiceaclterritoryelement_id}/{elementvalue}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesDeleteOrgElementValues',
        'description' => 'SpiceACL Territores Delete Org Element Values',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgobjecttypes',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesGetOrgObjectsTypes',
        'description' => 'SpiceACL Territores Get Org Object Types',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgobjecttypes/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesGetOrgObjectsType',
        'description' => 'SpiceACL Territores Get Org Object Type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclterritories/core/orgobjecttypes/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesSetOrgObjectsType',
        'description' => 'SpiceACL Territores Set Org Object Type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclterritories/core/orgobjecttypes/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesDeleteOrgObjectsType',
        'description' => 'SpiceACL Territores Delete Org Object Type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgobjecttypes/bymodule/{module}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesGetOrgObjectTypeByModule',
        'description' => 'SpiceACL Territores Get Org Object Type by Module',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgobjecttypeelements/{spiceaclterritorytype_id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesGetOrgObjectTypeElements',
        'description' => 'SpiceACL Territores Get Org Object Type Elements',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclterritories/core/orgobjecttypeelements/{spiceaclterritoryelement_id}/{spiceaclterritorytype_id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesDeleteOrgObjectTypeElements',
        'description' => 'SpiceACL Territores Delete Org Object Type Elements',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgobjecttypemodules',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesGetOrgObjectTypeModules',
        'description' => 'SpiceACL Territores Get Org Object Type Modules',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclterritories/core/orgobjecttypemodules',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesSetOrgObjectTypeModules',
        'description' => 'SpiceACL Territores Set Org Object Type Modules',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'put',
        'route'       => '/spiceaclterritories/core/orgobjecttypemodules/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesSetOrgObjectTypes',
        'description' => 'SpiceACL Territores Set Org Object Types',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclterritories/core/orgobjecttypemodules/{module}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesDeleteOrgObjectTypeModules',
        'description' => 'SpiceACL Territores Delete Org Object Type Modules',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/territories',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclGetTeritories',
        'description' => 'SpiceACL Get Territores',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/territories/module/{module}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclGetTeritoriesForModule',
        'description' => 'SpiceACL Get Territores for Module',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclterritories/core/territories/check',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTeritoriesCheck',
        'description' => 'SpiceACL Territores Check',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclterritories/core/territories/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclAddTeritories',
        'description' => 'SpiceACL Add Territores',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclterritories/core/territories/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclDeleteTeritorry',
        'description' => 'SpiceACL Delete Territorry',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/core/orgobjectvalues/{objectid}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTeritorryGetOrgObjectValues',
        'description' => 'SpiceACL Territorry Get Object Values',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclterritories/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'getSpiceAclTeritorry',
        'description' => 'Get SpiceACL Territorry',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclterritories/core/{id}',
        'class'       => SpiceACLTerritoriesController::class,
        'function'    => 'spiceAclTerritoriesSetOrgElements',
        'description' => 'SpiceACL Territores Set Org Elements',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],

];

$RESTManager->registerRoutes($routes);
