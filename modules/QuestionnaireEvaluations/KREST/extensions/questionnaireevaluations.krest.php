<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\QuestionnaireEvaluations\KREST\controllers\QuestionnaireEvaluationsKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('questionnaireevaluations', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireEvaluations/values/byReference/{referenceModule}/{referenceId}',
        'class'       => QuestionnaireEvaluationsKRESTController::class,
        'function'    => 'getEvaluationValues',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/QuestionnaireEvaluations/generate/byReference/{referenceModule}/{referenceId}',
        'class'       => QuestionnaireEvaluationsKRESTController::class,
        'function'    => 'generateEvaluation',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

