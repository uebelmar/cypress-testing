<?php

namespace SpiceCRM\modules\SAPIdocs\KREST\controllers;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\authentication\AuthenticationController;

class SAPIdocsManagerController
{

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return $returnArray
     */
    public function getSegments($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        if (!$current_user->is_admin) {
            if (!$current_user->is_admin) throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');
        }

        return $res->withJson($this->buildSegment());
    }

    /**
     * build the segments
     *
     * @return array
     */
    private function buildSegment(){
        $db = DBManagerFactory::getInstance();

        $returnArray = [];

        $segmentsObject = $db->query("SELECT * FROM sapidocsegments WHERE deleted='0'");
        while ($segment = $db->fetchByAssoc($segmentsObject)) {
            $returnArray['segments'][] = $segment;
        }

        $segmentrelationsObject = $db->query("SELECT * FROM sapidocsegmentrelations WHERE deleted='0'");
        while ($segmentrelation = $db->fetchByAssoc($segmentrelationsObject)) {
            $returnArray['segmentrelations'][] = $segmentrelation;
        }

        $sfieldsObject = $db->query("SELECT * FROM sapidocfields WHERE deleted='0'");
        while ($field = $db->fetchByAssoc($sfieldsObject)) {
            $returnArray['fields'][] = $field;
        }
        return $returnArray;
    }

    /**
     * updates the segments on the database from the postbody
     *
     * @param $req
     * @param $res
     * @param $args
     * @throws Exception
     */
    public function setSegments($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        if (!$current_user->is_admin) {
            if (!$current_user->is_admin) throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');
        }

        $body = $req->getParsedBody();

        foreach ($body as $item) {
            $sql = '';
            switch ($item['type']) {
                case 'segments':
                    $sql = $this->buildSQL($item['action'], 'sapidocsegments', $item['data']);
                    break;
                case 'segmentrelations':
                    $sql = $this->buildSQL($item['action'], 'sapidocsegmentrelations', $item['data']);
                    break;
                case 'fields':
                    $sql = $this->buildSQL($item['action'], 'sapidocfields', $item['data']);
                    break;
            }
            $db->query($sql);
        }

        return $res->withJson($this->buildSegment());
    }

    /**
     * builds the SQL statement
     *
     * @param $table
     * @param $data
     */
    private function buildSQL($action, $table, $data)    {
        $sql = '';
        switch ($action) {
            case 'D':
                $sql = "DELETE from $table WHERE id='{$data['id']}'";
                break;
            case 'I':
                $fieldArray = [];
                $valueArray = [];
                foreach($data as $fieldName => $fieldValue){
                    $fieldArray[] = $fieldName;
                    $valueArray[] = "'{$fieldValue}'";
                }
                $sql = "INSERT INTO $table (". implode(',', $fieldArray).") VALUES (".implode(',', $valueArray).")";
                break;
            case 'U':
                $statementArray = [];
                foreach($data as $fieldName => $fieldValue){
                    if($fieldName == 'id') continue;
                    $statementArray[] = "$fieldName = ".($fieldValue || $fieldValue == '0' ? "'$fieldValue'": "NULL");
                }
                $sql = "UPDATE $table SET ".implode(',', $statementArray)." WHERE id='{$data['id']}'";
                break;
        }
        return $sql;
    }


}
