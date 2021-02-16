<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Questionnaires\KREST\controllers\QuestionnairesKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('questionnaires', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/Questionnaires/{questionnaireId}/answers/allParticipations',
        'class'       => QuestionnairesKRESTController::class,
        'function'    => 'getQuestionnaireEvaluation',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
