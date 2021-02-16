<?php 
namespace SpiceCRM\modules\ServiceLocations;

use SpiceCRM\data\SugarBean;

class ServiceLocation extends SugarBean {
    public $module_dir = 'ServiceLocations';
    public $object_name = 'ServiceLocation'; //__NAMESPACE__.'ServiceLocation';
    public $table_name = 'servicelocations';
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
