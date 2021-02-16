<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesDocItems;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use stdClass;

class SalesDocItem extends SugarBean
{

    var $new_schema = true;
    var $module_dir = 'SalesDocItems';
    var $object_name = 'SalesDocItem';
    var $table_name = 'salesdocitems';
    var $importable = false;

    /**
     * @var null the convertrules
     */
    var $convertrules = null;

    function __construct()
    {
        parent::__construct();
    }

    function get_summary_text()
    {
        return $this->name;
    }

    function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    function getTaxDisplay()
    {
        $db = DBManagerFactory::getInstance();
        $taxRecord = $db->fetchByAssoc($db->query("SELECT taxcategoryname FROM syssalesdoctaxcategories WHERE taxcategoryid = '{$this->tax_category}'"));
        if ($taxRecord && $taxRecord['taxcategoryname']) {
            return $taxRecord['taxcategoryname'];
        }
        return $this->tax_category;
    }

    function getUOMiso()
    {
        $db = DBManagerFactory::getInstance();
        $uomRecord = $db->fetchByAssoc($db->query("SELECT iso FROM uomunits WHERE id = '{$this->uom_id}'"));
        return $uomRecord['iso'];
    }

    /**
     * override save to handle a potential hook for the itemtype
     *
     * @param bool $check_notify
     * @param bool $fts_index_bean
     * @return string
     */
    function save($check_notify = false, $fts_index_bean = true)
    {
        $item = parent::save($check_notify, $fts_index_bean);

        // check if we have a save hook for the document type
        $params = $this->get_item_type_parameters();
        if ($params['aftersavehook']) {
            $hook = explode('->', $params['aftersavehook']);
            if (count($hook) == 2 && class_exists($hook[0])) {
                $obj = new $hook[0]();
                if (method_exists($obj, $hook[1])) {
                    $obj->{$hook[1]}($this);
                }
            }
        }

        if ($this->originating_id) {
            $this->load_relationship('parent_documentitems');
            $this->parent_documentitems->add($this->originating_id);
        }

        return $item;
    }

    private function get_item_type_parameters()
    {
        $params = $this->db->fetchByAssoc($this->db->query("SELECT * FROM syssalesdocitemtypes WHERE name = '{$this->itemtype}'"));
        return $params;
    }

    /**
     * retrives all linked items that have been converted thus far and nets out the open values in term of quantity and total value
     */
    public function getOpenItemValues()
    {
        $q = new stdClass();
        $q->quantity = $this->quantity;
        $q->amount_net = $this->amount_net;

        $linkedItems = $this->get_linked_beans('child_documentitems', 'SalesDocItem');
        foreach ($linkedItems as $linkedItem) {
            $salesDoc = BeanFactory::getBean('SalesDocs', $linkedItem->salesdoc_id);
            if($rule = $this->getConvertRule($salesDoc->salesdoctype, $linkedItem->itemtype)){
                switch($rule['quantityhandling']){
                    case '-':
                        $q->quantity -= $linkedItem->quantity;
                        $q->amount_net -= $linkedItem->amount_net;
                        break;
                    case '+':
                        $q->quantity += $linkedItem->quantity;
                        $q->amount_net += $linkedItem->amount_net;
                        break;
                }
            }
        }

        return $q;
    }

    /**
     * find a amtching convert rule
     *
     * @param $targetDocType
     * @param $targetItemType
     * @return mixed
     */
    private function getConvertRule($targetDocType, $targetItemType){
        if(!$this->convertrules){
            $this->getConvertRules();
        }
        foreach($this->convertrules as $convertrule){
            if($convertrule['salesdoctype_to'] == $targetDocType && $convertrule['salesdocitemtype_to'] == $targetItemType){
                return $convertrule;
            }
        }

        return false;
    }

    /**
     * retrieves all applicable convert rules for the document item
     */
    private function getConvertRules()
    {
        $this->convertrules = [];
        // get the salesDocto get the type
        $salesDoc = BeanFactory::getBean('SalesDocs', $this->salesdoc_id);
        $rules = $this->db->query("SELECT * FROM syssalesdocitemtypesflow WHERE salesdoctype_from='$salesDoc->salesdoctype' AND salesdocitemtype_from='$this->itemtype'");
        while ($rule = $this->db->fetchByAssoc($rules)) {
            $this->convertrules[] = $rule;
        }
    }
}
