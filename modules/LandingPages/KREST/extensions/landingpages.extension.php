<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\LandingPages\KREST\controllers\LandingPagesController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('landingpages', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/landingpage/{id}/{beanId}',
        'class'       => LandingPagesController::class,
        'function'    => 'getPageContent',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/landingpage/{id}/{beanId}',
        'class'       => LandingPagesController::class,
        'function'    => 'saveAnswer',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

