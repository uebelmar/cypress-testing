<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\ServiceCalendarTimes;

use SpiceCRM\data\SugarBean;

class ServiceCalendarTime extends SugarBean {
    public $module_dir = 'ServiceCalendarTimes';
    public $object_name = 'ServiceCalendarTime';
    public $table_name = 'servicecalendartimes';

    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }


}
