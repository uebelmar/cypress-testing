<?php
namespace SpiceCRM\modules\SalesPlanningCharacteristics;
use SpiceCRM\data\SugarBean;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\modules\SalesPlanningCharacteristicValues\SalesPlanningCharacteristicValue;


class SalesPlanningCharacteristic extends SugarBean {

    public $module_dir = 'SalesPlanningCharacteristics';
    public $object_name = 'SalesPlanningCharacteristic';
    public $table_name = 'salesplanningcharacteristics';
    public $importable = false;
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $modified_user_link;
    public $created_by;
    public $created_by_name;
    public $created_by_link;
    public $description;
    public $deleted;
    // This is used to retrieve related fields from form posts.
    public $relationship_fields = array();

    public function __construct() {
        parent::__construct();
    }

    public function get_summary_text() {
        return $this->name;
    }

    /*
     * enable acl check for this module
     * @param $interface: string
     * @return boolean
     */
    public function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    /*
     * @param $charId: string
     * @param $cvkey: string
     * @param $create: boolean = false
     * @return $id: string | ''
     */
    static public function getValueIdByKey($charId, $cvkey, $cvname, $create = false) {
        $db = DBManagerFactory::getInstance();

        $queryObj = $db->query("SELECT id FROM salesplanningcharacteristicvalues WHERE salesplanningcharacteristic_id='$charId' AND deleted=0 and cvkey='$cvkey'");
        // if more that one exists return empty string
        if($db->getRowCount($queryObj) > 1)
        {
            return '';
        }
        // if exactly one exists return its id
        elseif($db->getRowCount($queryObj) == 1)
        {
            $queryRecord = $db->fetchByAssoc($queryObj);
            return $queryRecord['id'];
        }
        // if $create is true create a new one
        elseif($create)
        {
            $SalesPlanningCharValue = new SalesPlanningCharacteristicValue();
            $SalesPlanningCharValue->salesplanningcharacteristic_id = $charId;
            $SalesPlanningCharValue->cvkey = $cvkey;
            $SalesPlanningCharValue->name = ($cvname != '' ? $cvname : $cvkey);
            $SalesPlanningCharValue->save();
            return $SalesPlanningCharValue->id;
        }
        else
            return'';
    }

}
