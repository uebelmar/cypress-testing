<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\MediaFiles\KREST\controllers\MediaFilesController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('mediafiles', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/MediaFiles/{mediaId}/file',
        'class'       => MediaFilesController::class,
        'function'    => 'getMediaFile',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/MediaFiles/{mediaId}/base64',
        'class'       => MediaFilesController::class,
        'function'    => 'getMediaFileBase64',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/MediaFiles/{mediaId}/file/th/{thumbSize}',
        'class'       => MediaFilesController::class,
        'function'    => 'getThumbnail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/MediaFiles/{mediaId}/file/mw/{maxWidth}',
        'class'       => MediaFilesController::class,
        'function'    => 'getImageWithMaxWidth',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/MediaFiles/{mediaId}/file/mwh/{maxWidth}/{maxHeight}',
        'class'       => MediaFilesController::class,
        'function'    => 'getImageWithMaxWidthAndHeight',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
