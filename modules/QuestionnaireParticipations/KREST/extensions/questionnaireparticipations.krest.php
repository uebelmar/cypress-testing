<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\QuestionnaireParticipations\KREST\controllers\QuestionnaireParticipationsKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('questionnaireparticipationss', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/byReference/{referenceType}/{referenceId}/results',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getResults_byReference',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/{participationId}/results',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getResults',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations//byReference/{referenceType}/{referenceId}/questionnaireId',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getQuestionnaireId_byReference',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/{questionnaireParticipationId}/questionnaireId',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getQuestionnaireId',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/byReference/{referenceType}/{referenceId}/evaluation',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getEvaluation_byReference',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/{participationId}/evaluation',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getEvaluation',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/byReference/{referenceType}/{referenceId}/interpretations',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getInterpretations',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/{participationId}/interpretations',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getInterpretations_byParticipation',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/byReference/{referenceType}/{referenceId}/interpretationsSuggested',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getInterpretationsSuggested_byReference',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/QuestionnaireParticipations/{participationId}/interpretationsSuggested',
        'class'       => QuestionnaireParticipationsKRESTController::class,
        'function'    => 'getInterpretationsSuggested',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

