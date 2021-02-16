<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\SpiceACLObjects\KREST\controllers\SpiceACLObjectsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('spiceaclobjects', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/spiceaclobjects',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'spiceAclObjects',
        'description' => 'Get Spice Auth Object',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclobjects/createdefaultobjects',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'createDefaultObjects',
        'description' => 'Create default objects',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclobjects/authtypes',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'authTypes',
        'description' => 'Get Auth Types',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclobjects/authtypes/{id}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'deleteAuthType',
        'description' => 'Delete Auth Type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclobjects/authtypes/{id}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'getAuthType',
        'description' => 'Get Auth Type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclobjects/authtypes/{id}/authtypefields/{field}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'createAuthTypeField',
        'description' => 'Create Auth Type Field',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclobjects/authtypes/{id}/authtypefields/{fieldid}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'deleteAuthTypeField',
        'description' => 'Delete Auth Type Field',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceaclobjects/authtypes/{id}/authtypeactions',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'getAuthTypeAction',
        'description' => 'Get Auth Type Action',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclobjects/authtypes/{id}/authtypeactions/{action}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'createAuthTypeAction',
        'description' => 'Create Auth Type Action',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclobjects/authtypes/{id}/authtypeactions/{actionid}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'deleteAuthTypeAction',
        'description' => 'Delete Auth Type Action',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceaclobjects/activation/{id}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'activate',
        'description' => 'Activate',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceaclobjects/activation/{id}',
        'class'       => SpiceACLObjectsController::class,
        'function'    => 'deleteActivation',
        'description' => 'Delete Activacion',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
