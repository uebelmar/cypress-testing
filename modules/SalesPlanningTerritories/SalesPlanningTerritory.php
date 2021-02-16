<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesPlanningTerritories;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class SalesPlanningTerritory extends SugarBean
{

    public $module_dir = 'SalesPlanningTerritories';
    public $object_name = 'SalesPlanningTerritory';
    public $table_name = 'salesplanningterritories';
    public $importable = false;

    public function __construct()
    {
        parent::__construct();

    }


    static public function getTerritoryIdByKey($scopeset, $territoryname)
    {
        $db = DBManagerFactory::getInstance();

        $territoriesObj = $db->query("SELECT id FROM salesplanningterritories WHERE name='$territoryname' and deleted=0");
        if ($db->getRowCount($territoriesObj) > 0) {
            $territoresRow = $db->fetchByAssoc($territoriesObj);
            return $territoresRow['id'];
        } else {
            $newTerritory = new SalesPlanningTerritory();
            $newTerritory->name = $territoryname;
            $newTerritory->save();
            $newTerritory->load_relationship('salesplanningscopesets');
            $newTerritory->salesplanningscopesets->add($scopeset);
            return $newTerritory->id;
        }
    }
}
