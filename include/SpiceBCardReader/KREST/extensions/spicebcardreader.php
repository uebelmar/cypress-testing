<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceBCardReader\KREST\controllers\KRESTBCardReaderController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('cardreader', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/cardreader/processBusinessCard',
        'class'       => KRESTBCardReaderController::class,
        'function'    => 'processBusinessCard',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
