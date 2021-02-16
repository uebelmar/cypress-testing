<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\SpiceImports\KREST\controllers\SpiceImportController;


/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('spiceimports', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/modules/SpiceImports/savedImports/{beanName}',
        'class'       => SpiceImportController::class,
        'function'    => 'SpiceImportGetSaves',
        'description' => 'get the saved spice imports',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/SpiceImports/filePreview',
        'class'       => SpiceImportController::class,
        'function'    => 'SpiceImportGetFilePreview',
        'description' => 'get the file reviews',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/modules/SpiceImports/upf',
        'class'       => SpiceImportController::class,
        'function'    => 'SpiceImportDeleteFile',
        'description' => 'delete the import files',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/modules/SpiceImports/import',
        'class'       => SpiceImportController::class,
        'function'    => 'SpiceImportSave',
        'description' => 'saves data from an impor',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/SpiceImports/import/{importId}/logs',
        'class'       => SpiceImportController::class,
        'function'    => 'SpiceImportSave',
        'description' => 'get the spice import log entries',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);