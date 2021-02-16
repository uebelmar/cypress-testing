<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\ServiceTickets\KREST\controllers\ServiceTicketsKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('simpleservice', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/modules/ServiceTickets/openinmyqueues',
        'class'       => ServiceTicketsKRESTController::class,
        'function'    => 'openInMyQueues',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/ServiceTickets/myopenitems',
        'class'       => ServiceTicketsKRESTController::class,
        'function'    => 'myOpenItems',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/ServiceTickets/{beanId}/prolong',
        'class'       => ServiceTicketsKRESTController::class,
        'function'    => 'prolong',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/modules/ServiceTickets/discoverparent/{parentType}/{parentId}',
        'class'       => ServiceTicketsKRESTController::class,
        'function'    => 'discoverparent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

