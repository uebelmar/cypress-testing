<?php
namespace SpiceCRM\modules\ServiceCalls;

use SpiceCRM\data\SugarBean;

class ServiceCall extends SugarBean {
    public $module_dir = 'ServiceCalls';
    public $object_name = 'ServiceCall';
    public $table_name = 'servicecalls';
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
