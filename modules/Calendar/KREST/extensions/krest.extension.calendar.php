<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Calendar\KREST\controllers\KRESTCalendarController;
use SpiceCRM\modules\Calendar\KREST\handlers\CalendarRestHandler;
/**
 * get a Rest Manager Instance
 */

$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('calendar', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/calendar/modules',
        'class'       => KRESTCalendarController::class,
        'function'    => 'KRESTGEtCalendarModules',
        'description' => 'gets a calendar module',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/calendar/calendars',
        'class'       => KRESTCalendarController::class,
        'function'    => 'KRESTGetCalendar',
        'description' => 'gets a calendar',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/calendar/other/{calendarid}',
        'class'       => KRESTCalendarController::class,
        'function'    => 'KRESTGetOtherCalendars',
        'description' => 'get other calendars depending on an id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/calendar/{user}',
        'class'       => KRESTCalendarController::class,
        'function'    => 'KRESTGEtUserCalendar',
        'description' => 'gets a calender dependind on the user ',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/calendar/users/{user}',
        'class'       => KRESTCalendarController::class,
        'function'    => 'KRESTGetUsersCalendar',
        'description' => 'get all calendars assigned to an user',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];
$RESTManager->registerRoutes($routes);
