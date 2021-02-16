<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\KnowledgeDocuments\KREST\controllers\KnowledgeDocumentsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('knowledgebase', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/KnowledgeDocument/{id}/release/all',
        'class'       => KnowledgeDocumentsKRESTController::class,
        'function'    => 'releaseAllChildren',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/KnowledgeDocument/List/modifySortSequence',
        'class'       => KnowledgeDocumentsKRESTController::class,
        'function'    => 'modifySortSequence',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

