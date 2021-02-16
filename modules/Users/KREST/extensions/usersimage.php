<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Users\KREST\controllers\UserImageController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('userimages', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}/image',
        'class'       => UserImageController::class,
        'function'    => 'GetImageData',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

