<?php


namespace SpiceCRM\modules\SalesDocs;

/**
 * Class SalesDocConvert
 *
 * defines core sales doc convert routines
 * they are specified in table syssalesdoctypesflow and syssalesdocitemtypesflow
 *
 * @package SpiceCRM\modules\SalesDocs
 */
class SalesDocConvert
{
    /**
     * generic fields to exclude from Convert for SalesDoc
     *
     * @var string[]
     */
    var $excludeSalesDocGeneric = ['id', 'date_entered', 'date_modified', 'date_indexed', 'modified_user_id', 'created_by', 'deleted', 'assigned_user_id', 'salesdocnumber', 'salesdocdate', 'salesdoctype', 'tags', 'outputtemplate_id'];

    /**
     * generic fields to exclude from Convert for SalesDocItems
     *
     * @var string[]
     */
    var $excludeSalesItemGeneric = ['id', 'date_entered', 'date_modified', 'date_indexed', 'modified_user_id', 'created_by', 'deleted', 'assigned_user_id', 'salesdoc_id', 'itemtype', 'tags'];


    /**
     * a generic convert function for the header
     *
     * @param $salesDocFrom
     * @param $salesDocto
     */
    public function convertSalesDocGeneric($salesDocFrom, &$salesDocto){
        foreach ($salesDocFrom->field_name_map as $fieldName => $fieldData){
            if($fieldData['type'] != 'link' && array_search($fieldName, $this->excludeSalesDocGeneric) === false){
                $salesDocto->$fieldName = $salesDocFrom->$fieldName;
            }
        }
    }

    /**
     * a generic convert function for the items
     *
     * @param $salesDocFrom
     * @param $salesDocto
     * @param $convertrule .. the record from the database for the convert
     */
    public function convertSalesDocItemGeneric($salesDocItemFrom, &$salesDocItemto, $convertrule = []){
        foreach ($salesDocItemFrom->field_name_map as $fieldName => $fieldData){
            if($fieldData['type'] != 'link' && array_search($fieldName, $this->excludeSalesItemGeneric) === false){
                $salesDocItemto->$fieldName = $salesDocItemFrom->$fieldName;
            }
            $openQuantities = $salesDocItemFrom->getOpenItemValues();
            $salesDocItemto->quantity = $openQuantities->quantity > 0 ?: 0;
            $salesDocItemto->amount_net = $openQuantities->amount_net > 0 ?: 0;
        }
        $salesDocItemto->originating_id = $salesDocItemFrom->id;
    }
}
