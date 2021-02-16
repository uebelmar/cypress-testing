<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\Logger\KREST\controllers\LogViewController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();


/**
 * register the Extension
 */
$RESTManager->registerExtension('crmlog', '1.0');

$routes = [

    [
        'method'      => 'get',
        'route'       => '/crmlog',
        'class'       => LogViewController::class,
        'function'    => 'CRMLogGetLines',
        'description' => 'get the lines of the crmlogger',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/crmlog/{begin:\d{10}}/{end:\d{10}}',
        'class'       => LogViewController::class,
        'function'    => 'CRMLogWithTime',
        'description' => 'get the logs within a timeframe',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/crmlog/fullLine/{lineId}',
        'class'       => LogViewController::class,
        'function'    => 'CRMLogFullLine',
        'description' => 'get the full line logs',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/crmlog/userlist',
        'class'       => LogViewController::class,
        'function'    => 'CRMLogGetAllUser',
        'description' => 'get the log from all user',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/krestlog',
        'class'       => LogViewController::class,
        'function'    => 'SpiceLogGetLines',
        'description' => 'get the lines of the spice logger',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/krestlog/{begin:\d{10}}/{end:\d{10}}',
        'class'       => LogViewController::class,
        'function'    => 'SpiceLogWithTime',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/krestlog/fullLine/{lineId}',
        'class'       => LogViewController::class,
        'function'    => 'SpiceLogFullLine',
        'description' => 'get the full line logs',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
         'method'      => 'get',
         'route'       => '/krestlog/routes',
         'class'       => LogViewController::class,
         'function'    => 'SpiceLogRoutes',
         'description' => 'get the log routes',
         'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/krestlog/userlist',
        'class'       => LogViewController::class,
        'function'    => 'SpiceLogGetAllUser',
        'description' => 'get the spice log from all users',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);
