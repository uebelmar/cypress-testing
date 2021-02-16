<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\ServiceTicketSLATimes;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class ServiceTicketSLATime extends SugarBean {
    public $module_dir = 'ServiceTicketSLATimes';
    public $object_name = 'ServiceTicketSLATime';
    public $table_name = 'serviceticketslatimes';


    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }

    private function retrieveCalendarWorkingDays($calendarid){
        $db = DBManagerFactory::getInstance();

        $calendar = BeanFactory::getBean('ServiceCalendars', $calendarid);

        $daysArray = [];
        $workingDays = $db->query("SELECT DISTINCT DAYOFWEEK as dayindicator FROM servicecalendartimes WHERE servicecalendar_id = '$calendarid' ORDER BY DAYOFWEEK");
        while($workingDay = $db->fetchByAssoc($workingDays)){
            $daysArray[] = $workingDay['dayindicator'];
        }

        $holidays = [];
        if($calendar->systemholidaycalendar_id){
            $holidayCalendar = BeanFactory::getBean('SystemHolidayCalendars', $calendar->systemholidaycalendar_id);
            $holidays = $holidayCalendar->getHolidays();
        }

        return ['workingdays' => $daysArray, 'holidays' => $holidays];
    }

    public function timeToResolution($startDate, $calendarid){
        return $this->time_to_resolution ? $this->determineSLADate($startDate, $calendarid, $this->time_to_resolution, $this->time_to_resolution_unit) : null;
    }

    public function timeToResponse($startDate, $calendarid){
        return $this->time_to_resolution ? $this->determineSLADate($startDate, $calendarid, $this->time_to_response, $this->time_to_response_unit) : null;
    }

    private function determineSLADate($startDate, $calendarid, $duration, $unit)
    {
        global $timedate;

        $responsedate = new DateTime($startDate, new DateTimeZone('UTC'));

        switch ($unit) {
            case 'h':
                $responsedate->add(new DateInterval("PT{$duration}H"));
                break;
            case 'd':
                $responsedate->add(new DateInterval("P{$duration}D"));
                break;
            case 'wd':
                if($calendarid) {
                    $calendar = BeanFactory::getBean('ServiceCalendars', $calendarid);
                    $responsedate= $calendar->addNWorkingDays($responsedate, $duration);
                } else {
                    $responsedate->add(new DateInterval("P{$duration}D"));
                }
                break;
            case 'wh':
                if($calendarid) {
                    $calendar = BeanFactory::getBean('ServiceCalendars', $calendarid);
                    $responsedate= $calendar->addNWorkingHours($responsedate, $duration);
                } else {
                    $responsedate->add(new DateInterval("PT{$duration}H"));
                }
                break;
        }
        return $responsedate->format($timedate->get_db_date_time_format());
    }

}
