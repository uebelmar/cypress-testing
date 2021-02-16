<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesDocs;

use SpiceCRM\includes\database\DBManagerFactory;

class SalesDocsLoader{

    /**
     * load the sales doc types
     *
     * @return array
     */
    public function loadSalesDocTypes(){
        $db = DBManagerFactory::getInstance();

        $retArray = [];
        $types = $db->query("SELECT * FROM syssalesdoctypes");
        while ($type = $db->fetchByAssoc($types)){
            // select the item types for the document type
            $type['itemtypes'] = [];
            $itemtypes = $db->query("SELECT salesdocitemtype FROM syssalesdoctypesitemtypes WHERE salesdoctype='{$type['name']}'");
            while($itemtype = $db->fetchByAssoc($itemtypes)){
                $type['itemtypes'][] = $itemtype['salesdocitemtype'];
            }

            $retArray[] = $type;
        }
        return $retArray;

    }

    /**
     * load the item types
     *
     * @return array
     */
    public function loadSalesDocItemTypes(){
        $db = DBManagerFactory::getInstance();

        $retArray = [];
        $types = $db->query("SELECT * FROM syssalesdocitemtypes");
        while ($type = $db->fetchByAssoc($types)){
            $retArray[] = $type;
        }
        return $retArray;

    }

    public function loadSalesDocTaxCategories(){
        $db = DBManagerFactory::getInstance();
        $retArray = [];
        $retArray = array();
        $taxcategories = $db->query("SELECT * FROM syssalesdoctaxcategories");
        while($taxcategory = $db->fetchByAssoc($taxcategories)){
            $retArray[] = array(
                'taxcategoryid' => $taxcategory['taxcategoryid'],
                'taxcategoryname' => $taxcategory['taxcategoryname'],
                'taxpercentage' => $taxcategory['taxpercentage']
            );
        }
        return $retArray;

    }

    /**
     * load the sales doc types flow
     *
     * @return array
     */
    public function loadSalesDocTypesFlow(){
        $db = DBManagerFactory::getInstance();

        $retArray = [];
        $typeflows = $db->query("SELECT * FROM syssalesdoctypesflow");
        while ($typeflow = $db->fetchByAssoc($typeflows)){
            $retArray[] = [
                'from' => $typeflow['salesdoctype_from'],
                'to' => $typeflow['salesdoctype_to']
            ];
        }
        return $retArray;
    }
}
