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


use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\TimeDate;
use SpiceCRM\includes\authentication\AuthenticationController;

class pluginksavedfilterscontroller {

    public function action_save($params){
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
//        file_put_contents("sugarcrm.log", print_r($params, true)."\n", FILE_APPEND);
        $filters = array();
        $td = new TimeDate();
        $nowgmt = gmdate($td::DB_DATETIME_FORMAT, time());
//        file_put_contents("sugarcrm.log", print_r($nowgmt, true)."\n", FILE_APPEND);
        
        $record = array(
            'id' => "'".$params['savedfilter_id']."'",
            'name' => "'".$db->quote($params['name'])."'",
            'date_entered' => "'".$nowgmt."'",
            'date_modified' => "'".$nowgmt."'",
            'modified_user_id' => "'".$current_user->id."'",
            'created_by' => "'".$current_user->id."'",
            'assigned_user_id' => "'".$current_user->id."'",
            'deleted' => 0,
            'kreport_id' => "'".$params['kreport_id']."'",
            'is_global' => ($params['is_global']===true ? 1 : 0),
            'selectedfilters' => "'".htmlentities($params['selectedfilters'], ENT_QUOTES, 'UTF-8')."'"
        );
        
        $q = "INSERT INTO kreportsavedfilters (".implode(",", array_keys($record)). ") VALUES(".implode(",", array_values($record)).")";        
//        file_put_contents("sugarcrm.log", print_r($q, true)."\n", FILE_APPEND);

        if(!$db->query($q))
            return array("success" => false);

        return array("success" => true);
    }
    
    public function action_delete($params){
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
//        file_put_contents("sugarcrm.log", print_r($params, true)."\n", FILE_APPEND);
        
        $q = "UPDATE kreportsavedfilters SET deleted=1 WHERE id='".$params['savedfilter_id']."'";        
//        file_put_contents("sugarcrm.log", print_r($q, true)."\n", FILE_APPEND);

        if(!$db->query($q))
            return array("success" => false);

        return array("success" => true);
            
    }
}
