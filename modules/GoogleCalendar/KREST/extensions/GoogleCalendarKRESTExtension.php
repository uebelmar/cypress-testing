<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\GoogleCalendar\GoogleCalendarRestHandler;
use SpiceCRM\modules\GoogleCalendar\KREST\controllers\GoogleCalendarKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('google_calendar', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/google/calendar/config/{userid}',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'getConfiguration',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/google/calendar/getbeans',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'GoogleCalendarGetBeans',
        'description' => 'gets a new calendar bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/google/calendar/getcalendars',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'GoogleCalendarGetCalendar',
        'description' => 'gets a new calendar',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/google/calendar/getbeanmappings',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'GoogleCalendarGetBeanMapping',
        'description' => 'get the calendar bean mapping',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/google/calendar/savebeanmappings',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'GoogleCalendarSaveMapping',
        'description' => 'saves the calender bean mapping',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/google/calendar/sync',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'GoogleCalendarSync',
        'description' => 'synchronize the google calendar',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/google/calendar/notifications/{userid}/{scope}',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'startSubscription',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/google/calendar/notifications/{userid}/{scope}',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'stopSubscription',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/google/calendar/getgoogleevents',
        'class'       => GoogleCalendarKRESTController::class,
        'function'    => 'GoogleCalendarGetEvents',
        'description' => 'get google calendar events ',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);


