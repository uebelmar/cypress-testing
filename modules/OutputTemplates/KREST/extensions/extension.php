<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\OutputTemplates\KREST\controllers\OutputTemplatesController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('outputtemplates', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/OutputTemplates/formodule/{module}',
        'class'       => OutputTemplatesController::class,
        'function'    => 'getModuleTemplates',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/OutputTemplates/previewhtml',
        'class'       => OutputTemplatesController::class,
        'function'    => 'previewhtml',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/OutputTemplates/previewpdf',
        'class'       => OutputTemplatesController::class,
        'function'    => 'previewpdf',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/OutputTemplates/{id}/compile/{bean_id}',
        'class'       => OutputTemplatesController::class,
        'function'    => 'compile',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/OutputTemplates/{id}/convert/{bean_id}/to/{format}',
        'class'       => OutputTemplatesController::class,
        'function'    => 'convertToFormat',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/OutputTemplates/{id}/convert/{bean_id}/to/{format}/base64',
        'class'       => OutputTemplatesController::class,
        'function'    => 'convertToBase64',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

