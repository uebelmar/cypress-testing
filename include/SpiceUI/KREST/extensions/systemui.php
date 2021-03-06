<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceUI\KREST\controllers\SpiceUIActionsetsController;
use SpiceCRM\includes\SpiceUI\KREST\controllers\SpiceUIFieldsetsController;
use SpiceCRM\includes\SpiceUI\KREST\controllers\SpiceUILoadtasksController;
use SpiceCRM\includes\SpiceUI\KREST\controllers\SpiceUIModelValidationsController;
use SpiceCRM\includes\SpiceUI\KREST\controllers\SystemUIController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('spiceui', '2.0', SpiceConfig::getInstance()->config['ui']);

$routes = [
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/loadtasks',
        'class'       => SpiceUILoadtasksController::class,
        'function'    => 'getLoadTasks',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/loadtasks/{loadtaskid}',
        'class'       => SpiceUILoadtasksController::class,
        'function'    => 'executeLoadTask',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/modules/{module}/listtypes',
        'class'       => SystemUIController::class,
        'function'    => 'SystemAddListType',
        'description' => 'add a list type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/modules/{module}/listtypes/{id}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemSetListType',
        'description' => 'sets a list type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceui/core/modules/{module}/listtypes/{id}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemDeleteListType',
        'description' => 'delete a list type',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/components',
        'class'       => SystemUIController::class,
        'function'    => 'SystemReturnListType',
        'description' => 'returns list types',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/roles/{userid}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetAllRoles',
        'description' => 'get all roles of users',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/roles/{roleid}/{userid}/{default}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemSetUserRole',
        'description' => 'sets the system user ids',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceui/core/roles/{roleid}/{userid}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemDeleteUserRole3',
        'description' => 'deletes the roles of an user',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/componentmodulealreadyexists',
        'class'       => SystemUIController::class,
        'function'    => 'SystemCheckForExist',
        'description' => 'checks if an module module allready exists',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/componentdefaultalreadyexists',
        'class'       => SystemUIController::class,
        'function'    => 'SystemCheckForDefault',
        'description' => 'checks if an default component allready exists',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/componentsets',
        'class'       => SystemUIController::class,
        'function'    => 'SystemSetComponentSet',
        'description' => 'sets the component sets',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/fieldsets',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetFieldSet',
        'description' => 'gets the fieldsets',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/fieldsets',
        'class'       => SpiceUIFieldsetsController::class,
        'function'    => 'setFieldSets',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/actionsets',
        'class'       => SpiceUIActionsetsController::class,
        'function'    => 'setActionSets',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/fieldsetalreadyexists',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetFieldSet',
        'description' => 'checks if an fieldset already exists',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/fielddefs',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetFieldDefs',
        'description' => 'gets the definition of a fieldset',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/servicecategories',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetServiceCategory',
        'description' => 'gets the service category',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/servicecategories/tree',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetServiceTree',
        'description' => 'gets the service category tree',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/servicecategories/tree',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetServiceTreeBody',
        'description' => 'gets the service category tree with a parsed body',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/selecttree/trees',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetSelectTree',
        'description' => 'selects a tree without param',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/selecttree/list/{id}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetSelectTreeListById',
        'description' => 'selects a tree list by id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/selecttree/tree/{id}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetSelectTreeById',
        'description' => 'selects a tree by id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/selecttree/tree',
        'class'       => SystemUIController::class,
        'function'    => 'SystemSetSelectTree',
        'description' => 'writes a tree in the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/selecttree/newtree',
        'class'       => SystemUIController::class,
        'function'    => 'SystemSetTree',
        'description' => 'creates a new tree with from a parsed body',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/modelvalidations',
        'class'       => SpiceUIModelValidationsController::class,
        'function'    => 'getModelValidations',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/core/modelvalidations/{module}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetModuleValidation',
        'description' => 'gets a module validation',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceui/core/modelvalidations}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemSetModelValidation',
        'description' => 'sets a model validation',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceui/core/modelvalidations/{id}',
        'class'       => SystemUIController::class,
        'function'    => 'SystemDeleteModelValidation',
        'description' => 'deletes a model validation',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/admin/navigation',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetAdminNav',
        'description' => 'get the admin navigation',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceui/admin/modules',
        'class'       => SystemUIController::class,
        'function'    => 'SystemGetAllModules',
        'description' => 'gets all modells from the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];


$RESTManager->registerRoutes($routes);


