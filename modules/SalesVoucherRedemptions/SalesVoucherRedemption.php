<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesVoucherRedemptions;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;

class SalesVoucherRedemption extends SugarBean
{
    //Sugar vars
    var $table_name = "salesvoucherredemptions";
    var $object_name = "SalesVoucherRedemption";
    var $module_dir = "SalesVoucherRedemptions";

    function __construct()
    {
        parent::__construct();
    }

    function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    function get_summary_text()
    {
        return $this->name;
    }

    /**
     * saves the redemption and alos calcualtes the new open amount on the voucher
     *
     * @param bool $check_notify
     * @param bool $fts_index_bean
     * @return string
     */
    function save($check_notify = FALSE, $fts_index_bean = TRUE){

        $ret = parent::save($check_notify, $fts_index_bean);

        // calculate the new open amount
        $voucher = BeanFactory::getBean('SalesVouchers', $this->salesvoucher_id);
        $redemptions = $voucher->get_linked_beans('salesvoucherredemptions', 'SalesVoucherRedemption');
        $voucher->voucher_value_open = $voucher->voucher_value;
        foreach ($redemptions as $redemption){
            $voucher->voucher_value_open -= $redemption->redemption_amount;
        }

        // if open value is 0 or less .. set status to redeemed
        if($voucher->voucher_value_open <= 0){
            $voucher->voucher_value_open = 0;
            $voucher->voucher_status = 'redeemed';
        }

        $voucher->save();

        return $ret;

    }
}
