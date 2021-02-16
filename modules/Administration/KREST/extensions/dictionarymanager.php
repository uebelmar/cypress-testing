<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Administration\KREST\controllers\DictionaryController;
use SpiceCRM\modules\Administration\KREST\controllers\adminController;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\modules\Administration\KREST\controllers\DictionaryManagerController;

/**
 * get a Rest Manager Instance
 */

$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('admindictionary', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/dictionary/list/{table}',
        'class'       => DictionaryManagerController::class,
        'function'    => 'GetDictionaryFields',
        'description' => 'get the dictionaryfields from the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/dictionary/browser/{module}/nodes',
        'class'       => DictionaryController::class,
        'function'    => 'getNodes',
        'description' => 'builds a note array',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/dictionary/browser/{module}/fields',
        'class'       => DictionaryController::class,
        'function'    => 'getFields',
        'description' => 'builds a field array',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/dictionary/browser/{module}/dbcolumns',
        'class'       => adminController::class,
        'function'    => 'getDBColumns',
        'description' => 'get all columns from the module-table in the database allowed as admin',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/repair/sql',
        'class'       => adminController::class,
        'function'    => 'buildSQLArray',
        'description' => 'buildind the query for a relationship repair',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/repair/database',
        'class'       => adminController::class,
        'function'    => 'repairAndRebuild',
        'description' => 'repairs and rebuilds the database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/repair/language',
        'class'       => adminController::class,
        'function'    => 'repairLanguage',
        'description' => 'clears language cache and repairs the language extensions',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/repair/aclroles',
        'class'       => adminController::class,
        'function'    => 'repairACLRoles',
        'description' => 'repairs ACL Roles',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/repair/cache',
        'class'       => adminController::class,
        'function'    => 'repairCache',
        'description' => 'clears the vardef cache, executes rebuilding of vardefs extensions and',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/repair/dbcolumns',
        'class'       => adminController::class,
        'function'    => 'repairDBColumns',
        'description' => 'delete all the given columns in the database ',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);