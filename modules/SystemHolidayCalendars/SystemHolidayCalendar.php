<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SystemHolidayCalendars;

use SpiceCRM\data\SugarBean;

class SystemHolidayCalendar extends SugarBean {
    public $module_dir = 'SystemHolidayCalendars';
    public $object_name = 'SystemHolidayCalendar';
    public $table_name = 'systemholidaycalendars';


    /**
     * returns an array wiht future holidays within the next 365 days as well as recurring holidays in that period
     *
     * @param int $horizon the horizon to look out in days
     */
    public function getHolidays($horizon = 365){
        global $timedate;

        $holidayArray = [];

        $curDate = new DateTime();
        $endDate = new DateTime();
        $endDate->add(new DateInterval("P{$horizon}D"));
        $holidays = $this->get_linked_beans('systemholidaycalendardays', 'SystemHolidayCalendarDay', [], 0, -99, 0, "(holiday_date>'{$curDate->format($timedate->get_db_date_format())}' AND holiday_date<'{$endDate->format($timedate->get_db_date_format())}') OR annually = 1");
        foreach($holidays as $holiday){
            if($holiday->annually == 1){
                $holidayDate = new DateTime($holiday->holiday_date);
                $holidayDate->setDate($curDate->format('Y'), $holidayDate->format('m'), $holidayDate->format('d'));
                if($holidayDate < $curDate){
                    $holidayDate->add(new DateInterval('P1Y'));
                }
                $holidayArray[] = $holidayDate->format($timedate->get_db_date_format());
            } else {
                $holidayArray[] = $holiday->holiday_date;
            }
        }

        return $holidayArray;
    }

}
