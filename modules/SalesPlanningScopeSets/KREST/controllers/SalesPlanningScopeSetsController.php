<?php
namespace SpiceCRM\modules\SalesPlanningScopeSets\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class SalesPlanningScopeSetsController {

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return $returnArray
     */
    public static function getScopeSets($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $returnArray = array();
        $returnArray[] = array(
            'id' => '',
            'name' => '-'
        );

        $scopesetObj = $db->query("SELECT id, name FROM salesplanningscopesets WHERE deleted='0'");
        while ($scopeset_record = $db->fetchByAssoc($scopesetObj)) {
            $returnArray[] = array(
                'id' => $scopeset_record['id'],
                'name' => $scopeset_record['name']
            );
        }

        return $res->withJson($returnArray);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return $returnArray
     */
    public function getScopeCharacteristics($req, $res, $args) {
        $db = DBManagerFactory::getInstance();
        $scopeSetId = $db->quote($args['scopeSetId']);
        if (!$scopeSetId || $scopeSetId == '') {
            return false;
        }

        $returnArray = array();
        $returnArray[] = array(
            'id' => 'territory',
            'name' => 'territory'
        );
        $scopesetObj = $db->query("SELECT salesplanningcharacteristics.id, salesplanningcharacteristics.name FROM salesplanningcharacteristics INNER JOIN salesplanningscopesets_salesplanningcharacteristics ON salesplanningscopesets_salesplanningcharacteristics.salesplanningcharacteristic_id = salesplanningcharacteristics.id WHERE salesplanningscopesets_salesplanningcharacteristics.deleted='0' AND salesplanningscopesets_salesplanningcharacteristics.salesplanningscopeset_id='" . $scopeSetId . "'");
        while ($scopeset_record = $db->fetchByAssoc($scopesetObj)) {
            $returnArray[] = array(
                'id' => $scopeset_record['id'],
                'name' => $scopeset_record['name'],
                'member_count' => self::get_member_count($scopeset_record['id']),

            );
        }

        return $res->withJson($returnArray);
    }

    /**
     * @param $parentId
     * @return $members
     */
    function get_member_count($parentId)
    {
        $db = DBManagerFactory::getInstance();
        $members = $db->fetchByAssoc($db->query("SELECT count(id) membercount FROM salesplanningcharacteristicvalues WHERE deleted='0' AND salesplanningcharacteristic_id='" . $parentId . "'"));
        return $members['membercount'];
    }

    /**
     * enhancement for integration with KReporter
     * @param $req
     * @param $res
     * @param $args
     * @return boolean
     */
    public function createFromKReport($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();

        $postBody = $req->getParsedBody();

        $scopeSetId = $args['scopeSetId'];
        $reportId = $args['reportId'];
        $nodeName = $postBody['nodeName'];
        $mapping = $postBody['mapping'];
        $thisScopeSet = BeanFactory::getBean('SalesPlanningScopeSets', $scopeSetId);

        $thisReport = BeanFactory::getBean('KReports');
        $thisReport->retrieve($reportId);

        $results = $thisReport->getSelectionResults(array());

        // process charvalues .. see if we have all ...
        // get the value we need to map or which are fixed values
        $fieldArray = array();
        $fixedArray = array();

        foreach ($mapping as $charId => $thisMappingEntry) {
            if ($thisMappingEntry['fieldvalue'] != '')
                $fieldArray[$thisMappingEntry['fieldvalue']] = array('charid' => $thisMappingEntry['charid'], 'value' => $thisMappingEntry['fieldvalue'], 'name' => $thisMappingEntry['fieldname']);
            else
                $fixedArray[$thisMappingEntry['charid']] = $thisMappingEntry['fixedvalue'];
        }

        // loop over the results
        foreach ($results as $resultRecord) {
            $valueIds = array();
            $thisTerritoryId = '';
            foreach ($fieldArray as $thisfieldvalue => $thisfielddata) {
                if ($thisfielddata['charid'] == 'territory') {
                    $SalesPlanningTerritory = BeanFactory::getBean('SalesPlanningTerritories');
                    $thisTerritoryId = $SalesPlanningTerritory->getTerritoryIdByKey($thisScopeSet->id, $resultRecord[$thisfielddata['value']]);
                } else {
                    $SalesPlanningCharacteristic = BeanFactory::getBean('SalesPlanningCharacteristics');
                    $valueId = $SalesPlanningCharacteristic->getValueIdByKey($thisfielddata['charid'], $resultRecord[$thisfielddata['value']], ($thisfielddata['name'] != '' ? $resultRecord[$thisfielddata['name']] : ''), true);
                    $valueIds[$thisfielddata['charid']] = $valueId;
                }
            }
            foreach ($fixedArray as $fixedCharId => $fixedValueId) {
                if ($fixedCharId == 'territory')
                    $thisTerritoryId = $fixedValueId;
                else
                    $valueIds[$fixedCharId] = $fixedValueId;
            }

            // add the planningnode
            $thisScopeSet->addNode($thisTerritoryId, $valueIds, ($nodeName != '' ? $resultRecord[$nodeName] : ''));
        }
        return $res->withJson(true);
    }
}
