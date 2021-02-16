<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\utils\KREST\controllers\RESTUtilsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('utils', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/pdf/toImage/base64data/{filepath}',
        'class'       => RESTUtilsController::class,
        'function'    => 'RestPDFToBaseImage',
        'description' => 'convert a pdf to a base64 image',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/pdf/toImageurl/{filepath}',
        'class'       => RESTUtilsController::class,
        'function'    => 'RestPDFToUrlImage',
        'description' => 'converts a pdf to Url image',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/pdf/upload/tmp',
        'class'       => RESTUtilsController::class,
        'function'    => 'PutToTmpPdfPath',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/pdf/upload/uploadsDir',
        'class'       => RESTUtilsController::class,
        'function'    => 'PutToUpPath',
        'description' => 'puts the content to an upload path',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

