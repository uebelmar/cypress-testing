<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesVouchers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SpiceNumberRanges\SpiceNumberRanges;

class SalesVoucher extends SugarBean
{
    //Sugar vars
    var $table_name = "salesvouchers";
    var $object_name = "SalesVoucher";
    var $new_schema = true;
    var $module_dir = "SalesVouchers";

    function __construct()
    {
        parent::__construct();
    }

    function get_summary_text()
    {
        return $this->voucher_number;
    }

    function save($check_notify = FALSE, $fts_index_bean = TRUE){

        if(empty($this->parent_id)){
            $contact = BeanFactory::getBean('Contacts');
            if ($contact->retrieve_by_email_address($this->email) !== false) {
                $this->parent_type = 'Contacts';
                $this->parent_id = $contact->id;
            }
        }

        if(empty($this->voucher_number)){
                $this->voucher_number = SpiceNumberRanges::getNextNumberForField('SalesVouchers', 'voucher_number');

                // add a random number
                $rand = rand(0, 99999);
                $this->voucher_number .= '-' . str_pad($rand, 0, 5);

                $this->name = $this->voucher_number;
        }

        return parent::save($check_notify, $fts_index_bean);
    }



}
