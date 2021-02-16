<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Inquiries\KREST\controllers\InquiriesKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('inquiries', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Inquiries/fromavada/{module}',
        'class'       => InquiriesKRESTController::class,
        'function'    => 'createFromAvada',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/Inquiries/catalogs',
        'class'       => InquiriesKRESTController::class,
        'function'    => 'getCatalogs',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

