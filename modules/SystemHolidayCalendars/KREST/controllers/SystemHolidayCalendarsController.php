<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SystemHolidayCalendars\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class SystemHolidayCalendarsController
{
    public function loadHolidays($req, $res, $args){
        

        $seed = BeanFactory::getBean('SystemHolidayCalendars', $args['id']);
        if (!$seed || !$seed->ACLAccess('edit')) {
            throw (new ForbiddenException("Forbidden to edit Calendar"));
        }

        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );
        $url = "https://calendarific.com/api/v2/holidays?api_key=". SpiceConfig::getInstance()->config['calendarific']['api_key']."&country={$args['country']}&year={$args['year']}&type=national";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $holidays = curl_exec($ch);

        $holidays = json_decode($holidays);

        foreach($holidays->response->holidays as $holiday){
            $sysHoliday = BeanFactory::getBean('SystemHolidayCalendarDays');
            $sysHoliday->name = $holiday->name;
            $sysHoliday->description = $holiday->description;
            $sysHoliday->holiday_date = $holiday->date->iso;
            $sysHoliday->systemholidaycalendar_id = $args['id'];
            $sysHoliday->save();
        }

        return $res->withJson($holidays->response->holidays);
    }
}
