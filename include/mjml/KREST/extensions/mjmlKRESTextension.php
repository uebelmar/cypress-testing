<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use Slim\App;
use SpiceCRM\includes\mjml\KREST\controllers\MJMLKRESTController;
use SpiceCRM\includes\RESTManager;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('mjml', '1.0');


$routes = [
    [
        'method'      => 'post',
        'route'       => '/mjml/parseJsonToHtml',
        'class'       => MJMLKRESTController::class,
        'function'    => 'parseJsonToHtml',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
