<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\QuestionOptionCategories\KREST\controllers\QuestionOptionCategoriesKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('questionnaireoptioncategories', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/QuestionOptionCategories/getList',
        'class'       => QuestionOptionCategoriesKRESTController::class,
        'function'    => 'getList',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

