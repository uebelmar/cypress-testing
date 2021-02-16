<?php
namespace SpiceCRM\modules\ServiceEquipments;

use SpiceCRM\data\SugarBean;

class ServiceEquipment extends SugarBean {
    public $module_dir = 'ServiceEquipments';
    public $object_name = 'ServiceEquipment';
    public $table_name = 'serviceequipments';
    public $new_schema = true;
    
    public $additional_column_fields = Array();

    public $relationship_fields = Array(
    );


    public function get_summary_text(){
        return $this->name . ' / ' . $this->serialnr . ' / ' . $this->servicelocation_name;
    }

    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }

    public function fill_in_additional_detail_fields() {
        parent::fill_in_additional_detail_fields();

        //@todo: check where clause compatibility with oracle and mssql
        $this->active_maintenance_contracts_exists = $this->get_linked_beans_count( 'salesdocs', 'SalesDoc', null, '( salesdoctype = "MC" or salesdoctype = "MC_BC" ) AND valid_from_date <= CURDATE() and valid_until_date >= CURDATE()' ) > 0;
        $this->active_order_exists = $this->get_linked_beans_count( 'serviceorders', 'ServiceOrder', null, "( date_start <= UTC_TIMESTAMP() AND date_end >= UTC_TIMESTAMP() ) " ) > 0;

    }
    
}
