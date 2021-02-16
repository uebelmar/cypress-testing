<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\ServiceTicketStages;

use SpiceCRM\data\SugarBean;

class ServiceTicketStage extends SugarBean {
    public $module_dir = 'ServiceTicketStages';
    public $object_name = 'ServiceTicketStage';
    public $table_name = 'serviceticketstages';
    public $new_schema = true;

    function bean_implements($interface) {
        switch($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    function ACLAccess($view, $is_owner = 'not_set'){

        switch($view){
            case 'edit':
            case 'delete':
                return false;
                break;
        }

        return parent::ACLAccess($view, $is_owner);
    }
    
}
