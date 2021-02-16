<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Questions\KREST\controllers\QuestionsKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('questions', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/Questions/{questionId}/answervalues/{participationId}',
        'class'       => QuestionsKRESTController::class,
        'function'    => 'getAnswerValues',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Questions/{questionId}/answervalues/{participationId}',
        'class'       => QuestionsKRESTController::class,
        'function'    => 'postAnswerValues',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

