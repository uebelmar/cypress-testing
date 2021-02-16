<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesPlanningNodes;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class SalesPlanningNode extends SugarBean {

    const CHARACTERISTIC_TERRITORY = '_territories';


    public $module_dir = 'SalesPlanningNodes';
    public $object_name = 'SalesPlanningNode';
    public $table_name = 'salesplanningnodes';
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
    public $additional_column_fields = [];
    public $relationship_fields = [];


    public function __construct(){
        parent::__construct();

    }

    public function get_summary_text(){
        return $this->name;
    }

    public function bean_implements($interface){
        switch($interface) {
            case 'ACL':return true;
        }
        return false;
    }


    // for session based settings
    public static function clearSession() {
       unset($_SESSION['KINAMU']['SalesPlanning'][__CLASS__]);
    }

    public static function setSessionVar($name, $value) {
        $_SESSION['KINAMU']['SalesPlanning'][__CLASS__][$name] = $value;
    }

    public static function getSessionVar($name) {
        return $_SESSION['KINAMU']['SalesPlanning'][__CLASS__][$name];
    }


    public static function getLevel($path) {
        if(empty($path)) return 0;
        return count(explode("/", ltrim($path, "/")));
    }


    public static function getCharacteristicForLevel($level) {
        $treeLevels = SalesPlanningNode::getSessionVar("tree_levels");
        return (isset($treeLevels[$level]) ? $treeLevels[$level] : null);
    }


    public static function getCharacteristicLevels() {
        return SalesPlanningNode::getSessionVar("tree_levels");
    }


    public static function createQueryForNode($versionId, $nodes, $depth, $levels, $nodeIdsOnly = false, $showUndoneOnly = false, $wildcardNodes = true) {

        $lvl = 0;
        $joinTables = [];
        $selectFields = [];
        $orderByFields = [];
        $groupBy = [];
        $whereCond = [];

        foreach($levels as $currDepth => $level) {
            if($currDepth >= $depth) break;

            $jt = "lvl" . $lvl++;
            if($level == self::CHARACTERISTIC_TERRITORY) {
                $joinTables[$jt] = "JOIN salesplanningterritories AS {$jt} ON {$jt}.id = pn.salesplanningterritory_id AND {$jt}.deleted = 0";
            }
            else {
                $joinTables[$jt] = " JOIN salesplanningnodes_salesplanningcharacteristicvalues AS jt{$jt} ON jt{$jt}.salesplanningnode_id = pn.id AND jt{$jt}.deleted = 0";
                $joinTables[$jt] .= " JOIN salesplanningcharacteristicvalues AS {$jt} ON {$jt}.id = jt{$jt}.salesplanningcharacteristicvalue_id AND {$jt}.deleted = 0";
                $joinTables[$jt] .= " AND {$jt}.salesplanningcharacteristic_id = '{$level}'" . ($wildcardNodes == true ? "" : (" AND {$jt}.cvkey <> '*'"));
            }
            $selectFields[] = "{$jt}.id AS id{$lvl}, {$jt}.name AS name{$lvl}";
            $orderByFields[] = "{$jt}.name ASC";
            $groupBy[] = "id{$lvl}";
        }

        if($depth > 1) {
            // a node is expanded (we need to specify the nodes)
            for($i = 1; $i < $depth; $i++)
                $whereCond[] = "lvl".($i-1).".id = '" . $nodes[$i] . "'";
        }

        if(count($whereCond) == 0) {
            $whereCond[] = "1";
        }

        //added maretval 2016-04-28: check on planningnodes.deleted!
        $whereCond[] = "pn.deleted=0";
        //end
        if($nodeIdsOnly) {
            // simplify the SQL if only the nodeids are required
            $orderByFields = [];
            $groupBy = [];
            $selectFields = [];
            $basicFields = array("pn.id");
        }
        else {
            $basicFields = array("pn.id", "pnm.notice", "pnm.marked_as_done");
        }

        if($showUndoneOnly) {
            $whereCond[] = "(pnm.marked_as_done = 0 OR pnm.marked_as_done IS NULL)";
        }

        $sql = "SELECT " . (count($basicFields) > 0 ? implode(", ", $basicFields) : "");
        $sql .= " " . (count($selectFields) > 0 ? ", " . implode(", ", $selectFields) : "");
        $sql .= " FROM salesplanningversions AS pv";
        $sql .= " JOIN salesplanningscopesets AS ss ON ss.id = pv.salesplanningscopeset_id";
        $sql .= " JOIN salesplanningnodes AS pn ON pn.salesplanningscopeset_id = ss.id";
        $sql .= " " .implode("\n", $joinTables);
        $sql .= " LEFT JOIN salesplanningnodes_masterdata AS pnm ON pnm.salesplanningversion_id = pv.id";
        $sql .= " AND pnm.salesplanningnode_id = pn.id AND pnm.deleted = 0";
        $sql .= " WHERE pv.id = '" . $versionId . "' AND pv.deleted = 0 AND " . implode(" AND ", $whereCond);
        $sql .= " " . ((count($groupBy) > 0) ? "GROUP BY " . implode(", ", $groupBy) : "");
        $sql .= " " . ((count($orderByFields) > 0) ? "ORDER BY " . implode(", ", $orderByFields) : "");

        return $sql;
    }


    public static function returnNodesForPath($planningVersionId, $pathArray, $levels, $wildcardNodes = true) {
        $db = DBManagerFactory::getInstance();
        $nodeList = [];
        $depth = count($pathArray);
        $sql = self::createQueryForNode($planningVersionId, $pathArray, $depth, $levels,true, false, $wildcardNodes);
        $resultSet = $db->query($sql);
        while($row = $db->fetchByAssoc($resultSet)) {
            $nodeList[] = $row['id'];
        }
        return $nodeList;
    }
}
