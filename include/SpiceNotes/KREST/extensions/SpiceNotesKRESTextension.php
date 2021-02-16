<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceNotes\KREST\controllers\SpiceNotesKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('spicenotes', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/note',
        'class'       => SpiceNotesKRESTController::class,
        'function'    => 'getQuickNotesForBean',
        'description' => 'get the quicknotes for the beans',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/note',
        'class'       => SpiceNotesKRESTController::class,
        'function'    => 'saveQuickNote',
        'description' => 'saves the notes',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/note/{noteId}',
        'class'       => SpiceNotesKRESTController::class,
        'function'    => 'editQuickNote',
        'description' => 'edits the quicknotes',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/{beanName}/{beanId}/note/{noteId}',
        'class'       => SpiceNotesKRESTController::class,
        'function'    => 'deleteQuickNote',
        'description' => 'deletes the quick notes',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
