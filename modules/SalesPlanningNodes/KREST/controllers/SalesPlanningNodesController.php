<?php
namespace SpiceCRM\modules\SalesPlanningNodes\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SalesPlanningNode;
use SalesPlanningCharacteristicValue;
use SpiceCRM\includes\database\DBManagerFactory;

class SalesPlanningNodesController
{

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return $data
     */
    public static function getCharacteristicSelectionList($req, $res, $args)
    {
        global $mod_strings;
        $db = DBManagerFactory::getInstance();

        $allCharacteristics = [];
        $versionId = $db->quote($args['versionId']);

        if (empty($versionId)) {
            return $res->withJson(['success' => false, 'message' => 'Planning Version ID is missing! Please reload the page!']);
        }

        $pv = BeanFactory::getBean('SalesPlanningVersions');
        // retrieve the version
        if ($pv->retrieve($versionId)) {
            // get version scopeset
            $scopes = $pv->get_linked_beans("salesplanningscopesets", "SalesPlanningScopeSet");
            if (count($scopes) > 0) {
                // get characteristics
                $characteristics = $scopes[0]->get_linked_beans("salesplanningcharacteristics", "SalesPlanningCharacteristic");

                // define the first item as territory
                $allCharacteristics = [SalesPlanningNode::CHARACTERISTIC_TERRITORY => $mod_strings['LBL_SALES_PLANNING_TERRITORY']];
                foreach ($characteristics as $characteristic) {
                    $allCharacteristics[$characteristic->id] = [
                        'name' => $characteristic->name,
                        'sequence' => $characteristic->salesplanningscopesets_characteristic_sequence
                    ];
                }
            }
        }
        // set the response data
        $data = ['data' => []];
        foreach ($allCharacteristics as $characteristicId => $characteristicValues) {
                $data['data'][] = [
                    'id' => $characteristicId,
                    'value' => $characteristicValues['name'],
                    'sequence' => $characteristicValues['sequence']
                ];
        }

        usort($data['data'], function($a, $b) { return $a->id == SalesPlanningNode::CHARACTERISTIC_TERRITORY || $a->sequence < $b->sequence ? -1 : 1; });

        $data['results'] = count($data['data']);
        return $res->withJson($data);

    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return array
     */
    public function getNodeInfo($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();
        // define necessary variables
        $params = $req->getQueryParams();
        $versionId = $db->quote($args['versionId']);
        $pathArray = json_decode($params['pathArray']);
        $depth = count($pathArray);
        $levels = json_decode($params['characteristics']);

        $characteristicSelectionList = json_decode(self::getCharacteristicSelectionList($req, $res, $args));
        $maxDepth = count($characteristicSelectionList->data);

        $query = SalesPlanningNode::createQueryForNode($versionId, $pathArray, $depth, $levels);
        $resultSet = $db->query($query);

        if ($resultSet && $resultSet->num_rows && $resultSet->num_rows == 1) {
            while ($row = $db->fetchByAssoc($resultSet)) {
                // 2012-08-27 handle name output
                $thisNodeText = '';
                $currentDepth = 1;
                $nodecrumbs = [];
                while ($currentDepth < $depth) {
                    // get the next level for the breadcrumbs
                    $nodecrumb = SalesPlanningCharacteristicValue::get_pathname($row['id' . $currentDepth], $row['name' . $currentDepth]);
                    $nodecrumbs[] = $nodecrumb;

                        // also build a human readable text
                    if ($thisNodeText != '') $thisNodeText .= ' - ';
                    $thisNodeText .= $nodecrumb[displayname];

                    // go to the next depth level
                    $currentDepth++;
                }

                return $res->withJson([
                    'leaf' => ($depth - 1) >= $maxDepth, // -1, because /root/ is not counted as own depth level
                    'success' => true,
                    'node' => self::buildUniqueNodeId($row),
                    'nodeText' => $thisNodeText,
                    'nodecrumbs' => $nodecrumbs,
                    'planningNode' => $row['id'],
                    'marked_done' => ($row['marked_as_done'] == 1),
                    'notice' => $row['notice']
                ]);
            }
        }
        return $res->withJson(['success' => false]);
    }

    /**
     * @ todo unused method to delete?
     * @param $req
     * @param $res
     * @param $args
     * @return array
     */
    public function setNextTreeLevel($req, $res, $args)
    {

        $db = DBManagerFactory::getInstance();
        $params = $req->getParsedBody();
        $characteristic = $params['characteristicId'];
        if (empty($characteristic)) {
            return $res->withJson(['success' => false, 'message' => 'characteristic not set!']);
        }

        $treeLevels = SalesPlanningNode::getSessionVar("tree_levels");
        if (empty($treeLevels)) {
            $treeLevels = [];
            $treeReload = true;
            $nodeReload = false;
        } else {
            $treeReload = false;
            $nodeReload = true;
        }

        $treeLevels[] = $characteristic;
        SalesPlanningNode::setSessionVar("tree_levels", $treeLevels);

        $characteristicSelectionList = json_decode(self::getCharacteristicSelectionList($req, $res, $args));

        SalesPlanningNode::setSessionVar("max_depth", count($characteristicSelectionList>data) + count($treeLevels));

        return $res->withJson([
            'success' => true,
            'depth' => count($treeLevels),
            'expandedNode' => $params['expanded_node'],
            'treeReload' => $treeReload,
            'nodeReload' => $nodeReload,
            'lastSpecification' => (count($characteristicSelectionList->data) == 0)
        ]);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return array
     */
    public function getNodesList($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();
        $nodes = [];
        $params = $req->getQueryParams();
        $versionId = $db->quote($args['versionId']);
        $pathArray = json_decode($params['pathArray']);
        $levels = json_decode($params['characteristics']);
        $depth = count($pathArray);

        $query = SalesPlanningNode::createQueryForNode($versionId, $pathArray, $depth, $levels, false, ($params['undoneOnly']));
        $characteristicSelectionList = json_decode(self::getCharacteristicSelectionList($req, $res, $args));

        $resultSet = $db->query($query);
        while ($row = $db->fetchByAssoc($resultSet)) {
            $isLeaf = count($characteristicSelectionList->data) == 0 && $depth == count(SalesPlanningNode::getCharacteristicLevels());

            // check ACL access for territory - skip node if access is denied
            if($depth == 1){
                $terr = BeanFactory::getBean('SalesPlanningTerritories', $row['id' . $depth]);
                if(!$terr){
                    continue;
                }
            }

            // load node
            if (isset($row['id' . $depth])) {
                $nodes[] = [
                    'id' => self::buildUniqueNodeId($row),
                    'value' => $row['id' . $depth],
                    'name' => $row['name' . $depth],
                    'notice' => $row['description'],
                    'children' => null,
                    'done' => $row['marked_as_done'] == true,
                    'isLeaf' => $isLeaf
                ];
            }
        }
        return $res->withJson($nodes);
    }

    /**
     * @param $tableRow
     * @return string
     */
    public static function buildUniqueNodeId($tableRow)
    {
        $nodeId = [];
        foreach ($tableRow as $fieldName => $fieldValue) {
            if (strpos($fieldName, "id") === 0) {
                $nodeId[] = $fieldValue;
            }
        }
        return md5(implode("_", $nodeId));
    }


}
