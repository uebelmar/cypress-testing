<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesDocs;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SpiceNumberRanges\SpiceNumberRanges;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\includes\authentication\AuthenticationController;

class SalesDoc extends SugarBean {

    var $new_schema = true;
    var $module_dir = 'SalesDocs';
    var $object_name = 'SalesDoc';
    var $table_name = 'salesdocs';
    var $importable = false;


    function get_summary_text() {
        return $this->salesdoctype . ' ' . $this->salesdocnumber . ' ' . $this->account_op_name . ' ' . $this->salesdocdate;
    }

    function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    function fill_in_additional_detail_fields(){

        // get iso curency code
        if( isset( $GLOBALS['locale']->currencies[$this->currency_id] )) {
            $this->currency_display = $GLOBALS['locale']->currencies[$this->currency_id]['name'] . ' (' . $GLOBALS['locale']->currencies[$this->currency_id]['symbol'] . ')';
        }

        parent::fill_in_additional_detail_fields();
    }

    public function save($check_notify = false, $fts_index_bean = true) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        // issue a GUID if we have none ...
        if($this->id == '')
        {
            $this->id = create_guid();
            $this->new_with_id = true;
        }

        if(empty($this->salesdocnumber)){
            // determine the number range based on document type and company code
            $numberRange = $this->db->fetchByAssoc($this->db->query("SELECT numberrange from syssalesdocnumberranges WHERE syssalesdoctype='{$this->salesdoctype}' AND companycode_id='{$this->companycode_id}'"));
            if($numberRange){
                $this->salesdocnumber = SpiceNumberRanges::getNextNumber( $numberRange['numberrange'], ['withPrefix'=>true,'withPadding'=>true] );
            } else {
                $this->salesdocnumber = str_pad(SpiceNumberRanges::getNextNumberForField('SalesDocs', 'salesdocnumber'), 10, '0', STR_PAD_LEFT );
            }
        }

        // save the document
        $document =  parent::save($check_notify, $fts_index_bean);

        if($this->originating_id){
            $this->load_relationship('parent_documents');
            $this->parent_documents->add($this->originating_id);
        }

        // check if we have a save hook for the document type
        $params = $this->get_document_type_parameters();
        if($params['aftersavehook']){
            $hook = explode('->', $params['aftersavehook']);
            if(count($hook) == 2 && class_exists($hook[0])){
                $obj = new $hook[0]();
                if(method_exists($obj, $hook[1])){
                    $obj->{$hook[1]}($this);
                }
            }
        }

        return $document;
    }

    /**
     * saves the bean only without triggering the document based hooks
     *
     * @param bool $check_notify
     * @param bool $fts_index_bean
     * @return String
     */
    public function saveOnly($check_notify = false, $fts_index_bean = true){
        return parent::save($check_notify, $fts_index_bean);
    }

    private function get_document_type_parameters(){
        $params = $this->db->fetchByAssoc($this->db->query("SELECT * FROM syssalesdoctypes WHERE name = '{$this->salesdoctype}'"));
        return $params;
    }

    public function retrieve($id = -1, $encode = false, $deleted = true, $relationships = true)
    {
        $retrieved = parent::retrieve($id, $encode, $deleted, $relationships);

        if($retrieved){
            $items = $this->get_linked_beans('salesdocitems', 'SalesDocItem');

            // initialize to 0
            $this->amount_net = 0;
            $this->amount_gross = 0;
            $this->tax_amount = 0;

            // add all the item values
            foreach($items as $item){
                $this->amount_net += floatval($item->amount_net);
                $this->amount_gross += floatval($item->amount_gross);
                $this->tax_amount += floatval($item->tax_amount);
            }
            $this->salesdocitems = '';
        }

        return $retrieved;
    }


    function save_relationship_changes($is_update, $exclude = []) {

        //if account_id was replaced unlink the previous account_id.
        //this rel_fields_before_value is populated by sugarbean during the retrieve call.
        if (!empty($this->supconsultingorder_id)) {
            //unlink the old record.
            $this->load_relationship('supconsultingorders');
            $this->supconsultingorders->delete($this->id);
            $this->supconsultingorders->add($this->supconsultingorder_id);
        }
        parent::save_relationship_changes($is_update, $exclude);
    }

    function ACLAccess($view, $is_owner = 'not_set'){

        if($this->salesdoc_status != 'collected' and $this->salesdoc_status != 'created' and ($view == 'edit' || $view == 'delete')){
            # return false;
        }

        return parent::ACLAccess($view, $is_owner);
    }

    public function mark_deleted( $beanId ) {
        $salesDocItems = $this->get_linked_beans('salesdocitems', 'SalesDocItems');
        foreach ( $salesDocItems as $item ) $item->mark_deleted( $item->id );
        $salesVouchers = $this->get_linked_beans('salesvouchers', 'SalesVouchers');
        foreach ( $salesDocItems as $item ) $item->mark_deleted( $item->id );
        parent::mark_deleted($beanId);
    }

    /**
     * converts an existing document to a new documenttype
     *
     * @param $targetType
     */
    public function convertToType($targetType){

        $retArray = [];

        $moduleHandler = new ModuleHandler();

        // convert the header
        $convertRule = $this->db->fetchByAssoc($this->db->query("SELECT * FROM syssalesdoctypesflow WHERE salesdoctype_from = '{$this->salesdoctype}' AND salesdoctype_to = '$targetType'"));
        $convertMethod = explode('->', $convertRule['convert_method']);
        $convertClass = new $convertMethod[0];
        $target = BeanFactory::getBean('SalesDocs');
        $convertClass->{$convertMethod[1]}($this, $target);
        $target->salesdoctype = $targetType;

        // check if we should track and link the document
        if($convertRule['track'] != 0) {
            $target->originating_id = $this->id;
        }

        // add the doc to the response
        $retArray['SalesDoc'] = $moduleHandler->mapBeanToArray('SalesDocs', $target);

        $items = $this->get_linked_beans('salesdocitems', 'SalesDocItem');
        foreach($items as $item){
            // check if there is a copy rule for the itemtype
            $itemConvertRule = $this->db->fetchByAssoc($this->db->query("SELECT * FROM syssalesdocitemtypesflow WHERE salesdoctype_from = '{$this->salesdoctype}' AND salesdoctype_to = '$targetType' AND salesdocitemtype_from = '{$item->itemtype}'"));
            if($itemConvertRule) {
                $itemConvertMethod = explode('->', $itemConvertRule['convert_method']);
                $itemConvertClass = new $itemConvertMethod[0];
                $newItem = BeanFactory::getBean('SalesDocItems');
                $itemConvertClass->{$itemConvertMethod[1]}($item, $newItem, $itemConvertRule);
                $newItem->itemtype = $itemConvertRule['salesdocitemtype_to'];

                // check if we should track and link the items
                if($convertRule['track'] != 0) {
                    $newItem->originating_id = $item->id;
                }

                // add the item fully mapped to the response
                $retArray['SalesDocItems'][] = $moduleHandler->mapBeanToArray('SalesDocItemss', $newItem);
            }
        }

        return $retArray;
    }

}
