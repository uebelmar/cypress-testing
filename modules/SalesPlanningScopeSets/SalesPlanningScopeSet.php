<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesPlanningScopeSets;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\modules\SalesPlanningNodes\SalesPlanningNode;


class SalesPlanningScopeSet extends SugarBean {

    public $module_dir = 'SalesPlanningScopeSets';
    public $object_name = 'SalesPlanningScopeSet';
    public $table_name = 'salesplanningscopesets';
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
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = array('assigned_user_name', 'assigned_user_id');
    public $relationship_fields = array();

    public function __construct() {
        parent::__construct();
    }

    public function get_summary_text() {
        return $this->name;
    }

    public function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    public function addNode($territory, $charValues, $nodename = '') {
        $db = DBManagerFactory::getInstance();

        // build SQL to see if we have a node already that matches the territory and characteristics
        $sqlString = "SELECT salesplanningnodes.id FROM salesplanningnodes ";
        foreach($charValues as $charId => $charValueId)
        {
            $sqlString .= " INNER JOIN salesplanningnodes_salesplanningcharacteristicvalues AS j" . str_replace('-', '', $charId) . " ON j" . str_replace('-', '', $charId) . ".salesplanningnode_id = salesplanningnodes.id AND j" . str_replace('-', '', $charId) . ".salesplanningcharacteristicvalue_id='". $charValueId ."' ";
        }
        $sqlString .= " WHERE salesplanningnodes.salesplanningterritory_id='$territory' AND salesplanningnodes.deleted=0 AND salesplanningnodes.salesplanningscopeset_id = '$this->id'";
        $nodeIdObj = $db->query($sqlString);
        $nodeIdRecord = $db->fetchByAssoc($nodeIdObj);
        if($nodeIdRecord){

            return $nodeIdRecord['id'];
        }
        else
        {
            $thisNode = new SalesPlanningNode();
            $thisNode->salesplanningterritory_id = $territory;
            $thisNode->salesplanningscopeset_id = $this->id;
            $thisNode->name = $nodename;
            $thisNode->save();
            $thisNode->load_relationship('salesplanningcharacteristicvalues');
            foreach($charValues as $charId => $charValueId)
                $thisNode->salesplanningcharacteristicvalues->add($charValueId);
            return $thisNode->id;
        }
    }

}
