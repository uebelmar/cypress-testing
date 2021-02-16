<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ServiceFeedbacks\KREST\controllers\ServiceFeedbacksKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('servicefeedbacks', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/survey/{identificationToken}',
        'class'       => ServiceFeedbacksKRESTController::class,
        'function'    => 'getFullQuestionnaire',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'put',
        'route'       => '/survey/{identificationToken}/feedback',
        'class'       => ServiceFeedbacksKRESTController::class,
        'function'    => 'saveAnswers',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'put',
        'route'       => '/survey/{identificationToken}',
        'class'       => ServiceFeedbacksKRESTController::class,
        'function'    => 'saveAnswers',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/ServiceFeedbacks/{identificationToken}/survey',
        'class'       => ServiceFeedbacksKRESTController::class,
        'function'    => 'getFullQuestionnaire',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'put',
        'route'       => '/module/ServiceFeedbacks/{identificationToken}/survey',
        'class'       => ServiceFeedbacksKRESTController::class,
        'function'    => 'saveAnswers',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
