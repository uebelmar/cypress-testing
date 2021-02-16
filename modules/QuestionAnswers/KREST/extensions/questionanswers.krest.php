<?php

use SpiceCRM\modules\QuestionAnswers\KREST\controllers\QuestionAnswersKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = SpiceCRM\includes\RESTManager::getInstance();

$RESTManager->registerExtension('questionnaires', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/QuestionAnswers/ofParticipation/byParent/{parentType}/{parentId}',
        'class'       => QuestionAnswersKRESTController::class,
        'function'    => 'getAnswers_byParent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/QuestionAnswers/ofParticipation/byParticipation/{participationId}',
        'class'       => QuestionAnswersKRESTController::class,
        'function'    => 'getAnswers_byParticipation',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/QuestionAnswers/ofParticipation/byParent/{parentType}/{parentId}',
        'class'       => QuestionAnswersKRESTController::class,
        'function'    => 'saveAnswers_byParent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);