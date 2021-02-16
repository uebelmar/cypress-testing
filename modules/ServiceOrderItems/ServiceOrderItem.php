<?php
namespace SpiceCRM\modules\ServiceOrderItems;

use SpiceCRM\data\SugarBean;

class ServiceOrderItem extends SugarBean {
    public $module_dir = 'ServiceOrderItems';
    public $object_name = 'ServiceOrderItem';
    public $table_name = 'serviceorderitems';

    public function get_summary_text(){
        return $this->name;
    }

// Berechtigung
    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }
}
