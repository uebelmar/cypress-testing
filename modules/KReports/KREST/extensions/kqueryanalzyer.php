<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\KReports\Plugins\Integration\kqueryanalizer\controller\pluginkqueryanalizercontroller;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('kreportsqueryanalyzer', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/KReporter/plugins/kqueryanalizer/get_sql',
        'class'       => pluginkqueryanalizercontroller::class,
        'function'    => 'get_sql',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

