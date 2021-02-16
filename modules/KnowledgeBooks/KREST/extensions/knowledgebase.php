<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\KnowledgeBooks\KREST\controllers\KnowledgeBooksKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('knowledgebasepublic', '1.0');


$routes = [
    [
        'method'      => 'get',
        'route'       => '/knowledgebase/book',
        'class'       => KnowledgeBooksKRESTController::class,
        'function'    => 'getBookList',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/knowledgebase/book/{bookId}',
        'class'       => KnowledgeBooksKRESTController::class,
        'function'    => 'getBook',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/knowledgebase/document/{documentId}',
        'class'       => KnowledgeBooksKRESTController::class,
        'function'    => 'getDocumentContent',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/knowledgebase/sitemap',
        'class'       => KnowledgeBooksKRESTController::class,
        'function'    => 'getSitemap',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
