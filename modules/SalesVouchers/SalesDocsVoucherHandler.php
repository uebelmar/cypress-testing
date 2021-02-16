<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesVouchers;


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class SalesDocsVoucherHandler
{
    /**
     * In case the sales document is for a voucher sale,
     * this function has to be used to create the assigned sales vouchers.
     *
     * @param $salesDoc The Sales Document
     * @return integer Number of creates sales vouchers
     */
    public function handleVoucherSales(&$salesDoc)
    {
        if(empty($salesDoc->salesdoc_status) || ($salesDoc->salesdoc_status == 'vscreated' && $salesDoc->fetched_row['salesdoc_status'] == 'vsnew')) {
            $salesDoc->salesdoc_status = 'vscreated';
            $salesDoc->saveOnly(false, true);
        }if($salesDoc->fetched_row['salesdoc_status'] != $salesDoc->salesdoc_status){
            $salesDocItems = $salesDoc->get_linked_beans('salesdocitems', 'SalesDocItems');
            switch($salesDoc->salesdoc_status){
                case 'vspaid':
                    foreach ($salesDocItems as $item) {
                        $this->setStatusForSalesDocItemVouchers($item, 'paid');
                    }
                    break;
            }
        }
        return true;
    }

    /**
     * creates the voucher for one item
     *
     * @param $salesDocItem
     * @return bool
     */
    public function createVouchers(&$salesDocItem)
    {
        if($salesDocItem->voucherhandled) return;

        // load the salesdoc
        $salesDoc = BeanFactory::getBean('SalesDocs', $salesDocItem->salesdoc_id);

        if($salesDoc->salesdoc_status == 'vscreated') {
            $salesVouchers = $salesDocItem->get_linked_beans('salesvouchers', 'SalesVouchers');
            if(count($salesVouchers) == 0) {
                $this->createVoucherFromItem($salesDocItem, $salesDoc);
            }
            $salesDocItem->voucherhandled = true;
        }
        return true;
    }

    private function createVoucherFromItem($item, $salesDoc)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        for ($i = 0; $i < $item->quantity; $i++) {
            $voucher = BeanFactory::getBean('SalesVouchers');
            $voucher->voucher_type = 'v'; # v for "value"
            $voucher->voucher_value = $item->amount_net_per_uom;
            $voucher->voucher_value_open = $item->amount_net_per_uom;
            $voucher->currency_id = $salesDoc->currency_id;

            $voucher->parent_type = 'SalesDocItems';
            $voucher->parent_id = $item->id;

            // get the template from the product
            $product = BeanFactory::getBean('Products', $item->product_id);
            $voucher->outputtemplate_id = $product->outputtemplate_id;

            $voucher->voucher_status = 'created';
            $voucher->assigned_user_id = $current_user->id;

            $voucher->save();
        }
    }


    /**
     * Cancel all vouchers of a specific sales document (status === 'canceled').
     *
     * @param $salesDoc The Sales Document
     */
    public function setStatusForSalesDocItemVouchers($salesDocItem, $status)
    {
        $salesVouchers = $salesDocItem->get_linked_beans('salesvouchers', 'SalesVouchers');
        foreach ($salesVouchers as $voucher) {
            $voucher->voucher_status = $status;
            $voucher->save();
        }
    }
}
