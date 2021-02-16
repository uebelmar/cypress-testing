<?php
namespace SpiceCRM\modules\ServiceOrderEfforts;

use SpiceCRM\data\SugarBean;

/**
 * CR1000392
 * Class ServiceOrderEffort
 * Will represent the type of job to do as a position item in the order
 * quarterly maintenance, training, repair....
 */
class ServiceOrderEffort extends SugarBean {
    public $module_dir = 'ServiceOrderEfforts';
    public $object_name = 'ServiceOrderEffort';
    public $table_name = 'serviceorderefforts';
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
