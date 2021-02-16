<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesPlanningCharacteristicValues;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class SalesPlanningCharacteristicValue extends SugarBean {

    public $module_dir = 'SalesPlanningCharacteristicValues';
    public $object_name = 'SalesPlanningCharacteristicValue';
    public $table_name = 'salesplanningcharacteristicvalues';
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
    var $additional_column_fields = array('salesplanningcharacteristic_name');
    // This is used to retrieve related fields from form posts.
    public $relationship_fields = array();

    public function __construct() {
        parent::__construct();
    }

    public function get_summary_text() {
        return $this->name;
    }

    /*
     * @param $valueId: string
     * @param $valueDisplay: string
     * @return $valueDisplay
     */
    public static function get_pathname($valueId, $valueDisplay) {
        $db = DBManagerFactory::getInstance();

        $retArray = ['displayname' => $valueDisplay];

        $retRow = $db->fetchByAssoc($db->query("SELECT salesplanningcharacteristics.*, salesplanningcharacteristicvalues.cvkey  FROM salesplanningcharacteristics INNER JOIN salesplanningcharacteristicvalues ON salesplanningcharacteristics.id = salesplanningcharacteristicvalues.salesplanningcharacteristic_id WHERE salesplanningcharacteristicvalues.id = '$valueId'"));
        if (is_array($retRow)) {
            if ($retRow['field_module'] != '' && $retRow['field_link'] != '') {
                $linkModule = BeanFactory::getBean($retRow['field_module']);
                $linkModule->retrieve_by_string_fields(array($retRow['field_link'] => $retRow['cvkey']));
                if ($linkModule->id != ''){
                    $retArray['linkmodule'] = $retRow['field_module'];
                    $retArray['linkid'] = $linkModule->id;
                }
            }
        }

        // return the array
        return $retArray;
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

}
