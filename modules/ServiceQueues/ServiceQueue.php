<?php
namespace SpiceCRM\modules\ServiceQueues;

use SpiceCRM\data\SugarBean;

class ServiceQueue extends SugarBean {
    public $module_dir = 'ServiceQueues';
    public $object_name = 'ServiceQueue';
    public $table_name = 'servicequeues';
    public $new_schema = true;
    
    public $additional_column_fields = Array();

    public $relationship_fields = Array(
    );



    public function get_summary_text(){
        return $this->name;
    }

    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }
    
    
}
