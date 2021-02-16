<?php

namespace SpiceCRM\modules\SalesPlanningContents\KREST\controllers;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\modules\SalesPlanningContents\SalesPlanningContent;
use SpiceCRM\modules\SalesPlanningContentData\SalesPlanningContentData;
use SpiceCRM\modules\SalesPlanningNodes\SalesPlanningNode;
use SpiceCRM\modules\SalesPlanningNodes\KREST\controllers\SalesPlanningNodesController;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class SalesPlanningContentsController {

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return boolean
     */
    public function setNotice($req, $res, $args) {
        global $timedate;
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();
        // define necessary variables
        $params = $req->getParsedBody();
        $data = [];
        $data['modified_by'] = $current_user->id;
        $data['date_modified'] = gmdate($timedate->get_db_date_time_format());
        $data['notice'] = $params['notice'];
        $versionId = $db->quote($args['versionId']);
        $nodeId = $db->quote($args['nodeId']);

        // get the id of the master data
        $resultSet = $db->query("SELECT id FROM salesplanningnodes_masterdata WHERE salesplanningversion_id = '{$versionId}' AND salesplanningnode_id = '{$nodeId}' AND deleted = 0");
        if($resultSet && $resultSet->num_rows && $resultSet->num_rows > 0) {
            // update the record
            while($row = $db->fetchByAssoc($resultSet)) {
                $fieldsValues = [];
                foreach ($data as $field => $value) {
                    $fieldsValues[] = "$field = '$value'";
                }
                $db->query("UPDATE salesplanningnodes_masterdata  SET " . implode(", ", $fieldsValues) . " WHERE id ='{$row['id']}'");
            }
        }
        // insert a new record if it does not exist
        else {
            $data['id'] = create_guid();
            $data['salesplanningversion_id'] = $versionId;
            $data['salesplanningnode_id'] = $nodeId;

            $data['date_entered'] = $data['date_modified'];
            $data['created_by'] = $data['modified_by'];

            $data['deleted'] = 0;
            $data['marked_as_done'] = 0;

            $fieldsNames = [];
            $fieldsValues = [];
            foreach ($data as $field => $value) {
                $fieldsNames[] = $field;
                $fieldsValues[] = $value;
            }
            $db->query("INSERT INTO salesplanningnodes_masterdata (" .implode(", ", $fieldsNames) .")  VALUES('" . implode("', '", $fieldsValues) . "')");
        }

        $result = array("success" => true);
        return $res->withJson($result);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @param $done = true
     * @return boolean
     */
    public function markDone($req, $res, $args, $done = true) {
        global $timedate;
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();
        // define necessary variables
        $versionId = $db->quote($args['versionId']);
        $nodeId = $db->quote($args['nodeId']);
        $data = [];
        $data['modified_by'] = $current_user->id;
        $data['date_modified'] = gmdate($timedate->get_db_date_time_format());
        $data['marked_as_done'] = ($done == true ? 1 : 0);

        $resultSet = $db->query("SELECT id FROM salesplanningnodes_masterdata WHERE salesplanningversion_id = '{$versionId}' AND salesplanningnode_id = '{$nodeId}' AND deleted = 0");
        if($resultSet && $resultSet->num_rows && $resultSet->num_rows > 0) {

            // update the record
            while($row = $db->fetchByAssoc($resultSet)) {
                $fieldsValues = [];
                foreach ($data as $field => $value) {
                    $fieldsValues[] = "$field = '$value'";
                }
                $db->query("UPDATE salesplanningnodes_masterdata  SET " . implode(", ", $fieldsValues) . " WHERE id ='{$row['id']}'");
            }
        }
        // insert a new record if it does not exist
        else {
            $data['id'] = create_guid();
            $data['salesplanningversion_id'] = $versionId;
            $data['salesplanningnode_id'] = $nodeId;

            $data['date_entered'] = $data['date_modified'];
            $data['created_by'] = $data['modified_by'];

            $data['deleted'] = 0;
            $data['notice'] = "";

            $fieldsNames = [];
            $fieldsValues = [];
            foreach ($data as $field => $value) {
                $fieldsNames[] = $field;
                $fieldsValues[] = $value;
            }
            $db->query("INSERT INTO salesplanningnodes_masterdata (" .implode(", ", $fieldsNames) .")  VALUES('" . implode("', '", $fieldsValues) . "')");
        }

        $result = array("success" => true);
        return $res->withJson($result);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return boolean
     */
    public function unmarkDone($req, $res, $args) {
        return self::markDone($req, $res, $args, false);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getGridColumns($req, $res, $args) {
        $db = DBManagerFactory::getInstance();
        $recordId = $db->quote($args['recordId']);
        $cols = SalesPlanningContent::getGridColumns($recordId);
        return $res->withJson($cols);
    }

    /**
     * retrieve the node content table in period and do calculate cell values if the field has formula
     * or callback function
     * @param $req
     * @param $res
     * @param $args
     * @return $result
     */
    public function getNodeContent($req, $res, $args) {
        global $app_list_strings;
        $db = DBManagerFactory::getInstance();
        // define necessary variables
        $data = [];
        $periods = [];
        $periodsRawDate = [];
        $params = $req->getQueryParams();
        $versionId = $db->quote($args['versionId']);
        $nodeId = $db->quote($args['nodeId']);
        $pathArray = json_decode($params['pathArray']);
        $depth = count($pathArray);
        $levels = json_decode($params['characteristics']);

        $characteristicSelectionList = json_decode(SalesPlanningNodesController::getCharacteristicSelectionList($req, $res, $args));
        $maxDepth = count($characteristicSelectionList->data);
        $pv = BeanFactory::getBean('SalesPlanningVersions');

        // define the periods if the version exists
        if($pv->retrieve($versionId)) {
            $segments = SalesPlanningContent::getPeriods($pv->date_start, $pv->periode_unit, $pv->periode_segments);
            $i = 0;
            foreach($segments as $segment) {
                $i++;
                $periods[] = 'data_' . $pv->periode_unit . '_p' . $i;
                $periodsRawDate[] = $segment['date_raw'];
            }
        }

        // 2012-01-04 moved up C. Knoll
        // determine the nodes for calculation
        $nodes = SalesPlanningNode::returnNodesForPath($versionId, $pathArray, $levels);

        $resultSetQuery = "SELECT salesplanningcontent_id FROM salesplanningversions_salesplanningcontents";
        $resultSetQuery .= " WHERE salesplanningversion_id = '" . $versionId . "' AND deleted = 0";
        $resultSet = $db->query($resultSetQuery);

        // loop through version contents
        while($row = $db->fetchByAssoc($resultSet)) {

            $id = $row['salesplanningcontent_id'];
            $pc = BeanFactory::getBean('SalesPlanningContents');
            // retrieve the version content
            if($pc->retrieve($id)) {
                // get content fields
                $fields = $pc->get_linked_beans("salesplanningcontentfields", "SalesPlanningContentField", ["sort_order ASC"]);
                //MODIFICATION kerlehto 2014-02-28 added sort as getBeans refuses to sort
                usort($fields, function($f1, $f2) { if($f1->sort_order == $f2->sort_order) return 0; return ($f1->sort_order < $f2->sort_order ? -1 : 1); });

                foreach($fields as $field) {

                    $dataValues = [];
                    // load values with the stored data
                    if($field->storable) {
                        // if is node
                        if(($depth - 1) >= $maxDepth) {
                            foreach($periods as $key => $period) {
                                $dataQuery = "SELECT value FROM salesplanningcontentdata";
                                $dataQuery .= " WHERE salesplanningversion_id = '{$versionId}'";
                                $dataQuery .= " AND salesplanningcontentfield_id = '{$field->id}'";
                                $dataQuery .= " AND salesplanningnode_id = '{$nodeId}'";
                                $dataQuery .= " AND name = '" . ltrim($period, 'data_') . "'";
                                $dataQuery .= " AND deleted = 0";
                                $resultSetValues = $db->query($dataQuery);
                                // set the cell value
                                while($rowValues = $db->fetchByAssoc($resultSetValues))
                                    $dataValues[$period] = $rowValues['value'];
                            }
                        }
                        // if it is not node we sum up and calculate the average etc.
                        else {
                            // if the group action is not set get the default action from vardefs
                            if(!isset($app_list_strings['sales_planning_group_actions_dom'][$field->group_action])) {
                                $pcf = BeanFactory::getBean('SalesPlanningContentFields');
                                $field->group_action = $pcf->field_defs['group_action']['default'];
                            }

                            // calculate the by group action in the query
                            foreach($periods as $key => $period) {
                                $setValuesQuery = "SELECT " . strtoupper($field->group_action) . "(value) AS value FROM salesplanningcontentdata";
                                $setValuesQuery .= " WHERE salesplanningversion_id = '{$versionId}'";
                                $setValuesQuery .= " AND salesplanningcontentfield_id = '{$field->id}'";
                                $setValuesQuery .= " AND salesplanningnode_id IN ('" . implode("','", $nodes) . "')";
                                $setValuesQuery .= " AND name = '" . ltrim($period, 'data_') . "'";
                                $setValuesQuery .= " AND deleted = 0";

                                $resultSetValues = $db->query($setValuesQuery);
                                // set the cell value
                                while($rowValues = $db->fetchByAssoc($resultSetValues)) {
                                    $dataValues[$period] = $rowValues['value'];
                                }
                            }

                        }
                    }

                    // check for callback functions for column fields and value redetermination
                    foreach($periods as $key => $period) {
                        if(!empty($field->cbfunction) && (!isset($dataValues[$period]) || $field->redetermine_value)) {
                            /*$nodeParam = [];
                            foreach($pathArray as $pos => $posItem) {
                                if($characteristicSelectionList->data[$pos]->id !== null) {
                                    $resultSet = $db->query("SELECT cvkey FROM salesplanningcharacteristicvalues WHERE id = '" . $db->quote($posItem) . "'");

                                    if($row = $db->fetchByAssoc($resultSet)) {
                                        $nodeParam[$characteristicSelectionList->data[$pos]->id] = $row['cvkey'];

                                        if($row['cvkey'] == '*') {
                                            // we may only calculate "all the others" not listed as an own node
                                            $tmpPathArray = $pathArray;
                                            array_pop($tmpPathArray);
                                            $nodesOfThePath = SalesPlanningNode::returnNodesForPath($versionId, $tmpPathArray, $levels, false);
                                            if(count($nodesOfThePath) > 0) {
                                                $nodeParam[$characteristicSelectionList->data[$pos]->id . "_exclude"] = [];
                                                $sql = "SELECT DISTINCT cvkey FROM salesplanningnodes_salesplanningcharacteristicvalues AS pnv";
                                                $sql .= " JOIN salesplanningcharacteristicvalues AS cv ON cv.id = pnv.salesplanningcharacteristicvalue_id";
                                                $sql .= " AND cv.salesplanningcharacteristic_id = '" . $db->quote($characteristicSelectionList->data[$pos]->id) . "'";
                                                $sql .= " AND cv.deleted = 0";
                                                $sql .= " WHERE pnv.salesplanningnode_id IN ('" . implode("','", $nodesOfThePath) . "')";
                                                $resultSetExcludes = $db->query($sql);
                                                while($rowExcludes = $db->fetchByAssoc($resultSetExcludes)) {
                                                    $nodeParam[$characteristicSelectionList->data[$pos]->id . "_exclude"][] = $rowExcludes['cvkey'];
                                                }
                                            }
                                        }

                                    }
                                }
                            }*/

                            if(!empty($field->cbfunction)){
                                $filterMethodArray = explode('->', html_entity_decode($field->cbfunction));
                                $class = $filterMethodArray[0];
                                $method = $filterMethodArray[1];
                                if(class_exists($class)){
                                    $focus = new $class();
                                    if(method_exists($focus, $method)){
                                        // set the cell value from the callback function
                                        $dataValues[$period] = $focus->$method($pathArray, $periodsRawDate[$key], $pv->periode_unit);
                                       /* BEGIN PHP7 COMPAT
                                        The result of call_user_func() might be NAN: undefined or unrepresentable value in floating-point calculations
                                        http://php.net/manual/de/language.types.float.php#language.types.float.nan
                                        In that case json_encode will return false*/
                                        if(is_nan($dataValues[$period])) $dataValues[$period] = null;
                                        if(is_infinite($dataValues[$period])) $dataValues[$period] = null; //added maretval 2019-01-31 ticket KPP-23
                                    }
                                }
                            }
                        }
                        // if the callback function is not defined set the cell value to zero
                        if(!isset($dataValues[$period])) $dataValues[$period] = 0;
                    }
                    // callback function for rows total
                    if(!empty($field->cbfunction_sum)){
                        $filterMethodArray = explode('->', html_entity_decode($field->cbfunction_sum));
                        $class = $filterMethodArray[0];
                        $method = $filterMethodArray[1];
                        if(class_exists($class)){
                            $focus = new $class();
                            if(method_exists($focus, $method)){
                                $nodeParam['planning_node'] = $nodeId;
                                // set row sum from the callback function
                                $dataValues['sum'] = $focus->$method($pathArray, $dataValues, $periodsRawDate, $pv->periode_unit);
                                /* BEGIN PHP7 COMPAT
                                 The result of call_user_func() might be NAN: undefined or unrepresentable value in floating-point calculations
                                 http://php.net/manual/de/language.types.float.php#language.types.float.nan
                                 In that case json_encode will return false */
                                if(is_nan($dataValues[$period])) $dataValues[$period] = null;
                                if(is_infinite($dataValues[$period])) $dataValues[$period] = null; // added maretval 2019-01-31 ticket KPP-23
                                $dataValues['norender'] = true; // 2012-08-27 add norender field if custom suim function was used
                            }
                        }
                    }
                    // set the response data
                    $data[$field->id] = array_merge([
                        'field_id' => $field->id,
                        'description' => $field->name
                    ], $dataValues);
                }
            }

            return $res->withJson(["success" => true,"data" => $data]);
        }

        return withJson(["success" => false,"data" => $data]);
    }

    /**
     * update the node content values
     * @param $req
     * @param $res
     * @param $args
     * @return $result
     */
    public function update($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();
        // define necessary variables
        $periods = [];
        $params = $req->getParsedBody();
        $versionId = $db->quote($args['versionId']);
        $nodeId = $db->quote($args['nodeId']);
        $data = $params['data'];
        $pv = BeanFactory::getBean('SalesPlanningVersions');
        $pcf = BeanFactory::getBean('SalesPlanningContentFields');

        // check if version exists and define the periods
        if($pv->retrieve($versionId)) {
            $segments = SalesPlanningContent::getPeriods($pv->date_start, $pv->periode_unit, $pv->periode_segments);
            $i = 0;
            foreach($segments as $segment) {
                $i++;
                $periods[] = 'data_' . $pv->periode_unit . '_p' . $i;
            }
        }

        // update the value for each cell
        foreach($data as $record) {
            // retrieve the field
            if(!empty($record['field_id']))
                $field = $pcf->retrieve($record['field_id']);
            // update only if the field is storable
            if(isset($field) && $field->storable) {

                foreach($periods as $period) {
                    if(empty($record[$period])) continue;

                    $pcd = BeanFactory::getBean('SalesPlanningContentData');
                    $pcd->disable_num_format = true; // no number formatting!
                    // retrieve the cell
                    if($pcd->retrieve_by_string_fields([
                            "name" => ltrim($period, 'data_'),
                            "salesplanningversion_id" => $versionId,
                            'salesplanningnode_id' => $nodeId,
                            'salesplanningcontentfield_id' => $record['field_id']
                        ]) instanceof SalesPlanningContentData) {
                        // loaded the sugarbean or created a new one
                    }
                    // update the cell values
                    $pcd->name = ltrim($period, 'data_');
                    $pcd->salesplanningversion_id = $versionId;
                    $pcd->salesplanningcontentfield_id = $record['field_id'];
                    $pcd->salesplanningnode_id = $nodeId;
                    $pcd->value = (double)$record[$period];
                    if(empty($pcd->assigned_user_id)) {
                        $pcd->assigned_user_id = $current_user->id;
                    }
                    // save changes
                    $pcd->save();
                }

            }

        }

        $result = array("success" => true, "data" => $data);
        return $res->withJson($result);
    }
}
