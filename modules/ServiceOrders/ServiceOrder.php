<?php
namespace SpiceCRM\modules\ServiceOrders;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SpiceNumberRanges\SpiceNumberRanges;

class ServiceOrder extends SugarBean {
    public $module_dir = 'ServiceOrders';
    public $object_name = 'ServiceOrder';
    public $table_name = 'serviceorders';
    public $new_schema = true;

    public $additional_column_fields = Array();

    public $relationship_fields = Array(
    );

    public $sysnumberranges = true; //entries in table sysnumberranges required!


    public function get_summary_text(){
        return $this->serviceorder_number;
    }

    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }

    function ACLAccess($view, $is_owner = 'not_set'){

//        if($this->serviceorder_status == 'signed' && ($view == 'edit' || $view == 'delete')){
//            return false;
//        }

        return parent::ACLAccess($view, $is_owner);
    }

    public function retrieve($id = -1, $encode = false, $deleted = true, $relationships = true)
    {
        $bean = parent::retrieve($id, $encode, $deleted, $relationships);

        if(!empty($this->contact_id)){
            $contact = BeanFactory::getBean('Contacts', $this->contact_id);
            $this->email1 = $contact->email1;
        }

        return $bean;
    }

    public function save($check_notify = false, $fts_index_bean = true){
        global $timedate;

        //set serviceorder_number
        if(empty($this->serviceorder_number)){
            $this->serviceorder_number = str_pad(SpiceNumberRanges::getNextNumberForField('ServiceOrders', 'serviceorder_number'), 10, '0', STR_PAD_LEFT );
            //@todo: remove after name problem was sold
            $this->name = $this->serviceorder_number;
        }

        return parent::save($check_notify, $fts_index_bean);
    }

}
