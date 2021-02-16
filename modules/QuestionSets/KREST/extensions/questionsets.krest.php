<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\QuestionSets\KREST\controllers\QuestionSetsKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('questionsets', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/QuestionSets/renderer/{questionsetId}',
        'class'       => QuestionSetsKRESTController::class,
        'function'    => 'renderer',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionSets/{questionsetId}/answervalues/{participationId}',
        'class'       => QuestionSetsKRESTController::class,
        'function'    => 'getAnswerValues',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
