<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\SystemHolidayCalendars\KREST\controllers\SystemHolidayCalendarsController;

$RESTManager = RESTManager::getInstance();
$RESTManager->registerExtension('holidaycalendars', '2.0', ['calendarific' => isset(SpiceConfig::getInstance()->config['calendarific']['api_key']) ? true : false]);

$routes = [
    [
        'method'      => 'get',
        'route'       => '/modules/SystemHolidayCalendars/{id}/calendarific/{country}/{year}',
        'class'       => SystemHolidayCalendarsController::class,
        'function'    => 'loadHolidays',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

