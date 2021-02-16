<?php
/* * *******************************************************************************
* This file is part of KReporter. KReporter is an enhancement developed
* by aac services k.s.. All rights are (c) 2016 by aac services k.s.
*
* This Version of the KReporter is licensed software and may only be used in
* alignment with the License Agreement received with this Software.
* This Software is copyrighted and may not be further distributed without
* witten consent of aac services k.s.
*
* You can contact us at info@kreporter.org
******************************************************************************* */

namespace SpiceCRM\modules\KReports\KREST\controllers;


use SpiceCRM\includes\database\DBManagerFactory;

use SpiceCRM\includes\authentication\AuthenticationController;

class KReportsPluginSavedFiltersController
{

    /**
     * save report filter to database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function saveFilter($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $params = $req->getParsedBody();
        $now = $db->now();
        $selectedFilters = htmlentities(json_encode($params['selectedfilters']), ENT_QUOTES, 'UTF-8');
        if (isset($params['isNew']) && $params['isNew']) {
            $record = array(
                'id' => "'{$args['savedFilterId']}'",
                'name' => "'" . $db->quote($params['name']) . "'",
                'date_entered' => $now,
                'date_modified' => $now,
                'modified_user_id' => "'{$current_user->id}'",
                'created_by' => "'{$current_user->id}'",
                'assigned_user_id' => "'{$current_user->id}'",
                'deleted' => 0,
                'kreport_id' => "'{$args['reportId']}'",
                'is_global' => ($params['is_global'] === true ? 1 : 0),
                'selectedfilters' => "'$selectedFilters'"
            );

            $query = $db->query("INSERT INTO kreportsavedfilters (" . implode(",", array_keys($record)) . ") VALUES(" . implode(",", array_values($record)) . ")");
        } else {
            $query = $db->query("UPDATE kreportsavedfilters SET selectedfilters = '$selectedFilters'");
        }

        return $res->withJson(["success" => boolval($query)]);
    }

    /**
     * set report filter deleted to true
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function deleteFilter($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();

        $query = "UPDATE kreportsavedfilters SET deleted=1 WHERE id='{$args['savedFilterId']}'";

        return $res->withJson(["success" => boolval($db->query($query))]);
    }

    /**
     * get saved filters  list
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getSavedFilters($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $params = $req->getQueryParams();
        $whereClause = "";

        if (!empty($params['assignedUserId'])) {
            if ($params['assignedUserId'] == 'own')
                $whereClause = " AND (ksf.assigned_user_id='" . $current_user->id . "' OR ksf.is_global > 0) AND deleted=0";
            else
                $whereClause = " AND (ksf.assigned_user_id='" . $params['assignedUserId'] . "' OR ksf.is_global > 0) AND deleted=0";
        }
        $results = array();
        $mod_strings = return_module_language($GLOBALS['current_language'], 'KReports');

        //set empty record for Viewer only
        if (isset($params['context']) && $params['context'] == "Viewer") {
            $results[] = array(
                'savedfilter_id' => 'none',
                'name' => '---',
                'kreport_id' => $params['reportid'],
                'assigned_user_id' => null,
                'assigned_user_name' => null,
                'is_global' => 1,
                'selectedfilters' => null
            );
        }

        //read passed whereConditions
        $whereconditions = array();
        if (isset($params['currentWhereConditions'])) {
            $whereconditions = json_decode(html_entity_decode($params['currentWhereConditions'], ENT_QUOTES), true);
        }

        //get data
        $records = $db->query("SELECT ksf.*, u.user_name FROM kreportsavedfilters ksf "
            . "INNER JOIN users u ON u.id = ksf.assigned_user_id "
            . "WHERE ksf.deleted= 0 AND ksf.kreport_id='" . $args['reportId'] . "' "
            . $whereClause
            . " ORDER BY ksf.name ASC");

        //prepare records
        while ($record = $db->fetchByAssoc($records)) {
            $selectedfilters = json_decode(html_entity_decode($record['selectedfilters'], ENT_QUOTES), true);
            $whereconditionsFieldids = array();
            foreach ($whereconditions as $idx => $condition) {
                $whereconditionsFieldids[] = $condition['fieldid'];
            }

            //loop selectedfilters over whereconditions and set status
            //if one of the filters IDs does not correspond to whereconditions, the whole savedfilter is status =0
            $status = 1;
            foreach ($selectedfilters as $idxfi => $filter) {
                if (!in_array($filter['fieldid'], $whereconditionsFieldids)) {
                    $status = 0;
                    break;
                }
            }

            //set entry values
            $results[] = array(
                'savedfilter_id' => $record['id'],
                'name' => ($record['is_global'] ? $mod_strings['LBL_KSAVEDFILTERS_IS_GLOBAL_MARK'] . ' ' : '') . $record['name'],
                'kreport_id' => $record['kreport_id'],
                'assigned_user_id' => $record['assigned_user_id'],
                'assigned_user_name' => $record['user_name'],
                'is_global' => $record['is_global'],
                'selectedfilters' => html_entity_decode($record['selectedfilters']),
                'status' => $status
            );
        }

        return $res->withJson($results);
    }

}
