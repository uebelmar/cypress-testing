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
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\modules\Campaigns\Campaign;
use SpiceCRM\modules\ProspectLists\ProspectList;

require_once 'modules/ProspectLists/ProspectList.php';

class KReportTargetListHandler {

    var $KReport;
    var $KReportResults;
    var $targetListID;
    var $targetList;

    function __construct(&$thisReport, $readResults = true) {
        $this->KReport = $thisReport;

        if ($readResults)
            $this->KReportResults = $this->KReport->getSelectionresults();

        $this->targetList = new ProspectList();
    }

    function createTargeList($listname, $campaign_id = '', $createDirect = false) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        if (count($this->KReportResults > 0) || $createDirect) {
            require_once 'modules/ProspectLists/ProspectList.php';
            $newProspectList = new ProspectList ();

            $newProspectList->name = $listname;
            $newProspectList->list_type = 'default';
            $newProspectList->assigned_user_id = $current_user->id;
            $newProspectList->assigned_user_name = $current_user->full_name;
            $newProspectList->save();

            // add to campaign
            if ($campaign_id != '') {
                require_once 'modules/Campaigns/Campaign.php';
                $thisCampaign = new Campaign();
                $thisCampaign->retrieve($campaign_id);
                $thisCampaign->load_relationships();
                $campaignLinkedFields = $thisCampaign->get_linked_fields();
                foreach ($campaignLinkedFields as $linkedField => $linkedFieldData) {
                    if ($thisCampaign->$linkedField->_relationship->rhs_module == 'ProspectList')
                        $thisCampaign->$linkedField->add($newProspectList->id);
                }
            }

            // fill with results: 
            $newProspectList->load_relationships();

            $linkedFields = $newProspectList->get_linked_fields();

            // change to allow union reports to be exportable as targetlists
            $modulesArray = array($this->KReport->report_module);

            // check that we have union modules and it is not an empty json
            if(!empty($this->KReport->union_modules) && $this->KReport->union_modules != '{}'){
               $unionModules = json_decode(html_entity_decode($this->KReport->union_modules));
               foreach($unionModules as $thisUnionModule){
                  if(!in_array($thisUnionModule->module, $modulesArray))
                          $modulesArray[] = $thisUnionModule->module;
               }
            }
            
            foreach ($linkedFields as $linkedField => $linkedFieldData) {
                //if ($newProspectList->$linkedField->_relationship->rhs_module == $this->KReport->report_module) {
                if (in_array($newProspectList->$linkedField->_relationship->rhs_module, $modulesArray)) {
                    // success   
                    if ($createDirect != true) {
                        foreach ($this->KReportResults as $thisRecord) {
                           // filter by module records since we allow unions
                           if($thisRecord['sugarRecordModule'] == $newProspectList->$linkedField->_relationship->rhs_module)
                              $newProspectList->$linkedField->add($thisRecord ['sugarRecordId']);
                        }
                    } else {
                        $sqlArray = $this->KReport->get_report_main_sql_query();
                        // filter by the union module
                        $createPLSQL = "INSERT INTO prospect_lists_prospects (id, prospect_list_id, related_id, related_type, date_modified, deleted)
						 	SELECT uuid(), '" . $newProspectList->id . "', reportselect.sugarRecordId, '" . $newProspectList->$linkedField->_relationship->rhs_module . "', UTC_TIMESTAMP(), '0' FROM
						 	(" . $sqlArray . ") as reportselect
						 	WHERE reportselect.sugarRecordModule = '".$newProspectList->$linkedField->_relationship->rhs_module."' AND NOT EXISTS(SELECT id FROM prospect_lists_prospects WHERE related_id = reportselect.sugarRecordId AND prospect_list_id='" . $newProspectList->id . "' AND deleted=0)";
                        $db->query($createPLSQL);
                    }
                } elseif ($newProspectList->$linkedField->_relationship->rhs_module == 'Campaigns' and $campaign_id != '') {
                    $newProspectList->$linkedField->add($campaign_id);
                }
            }
        }
    }

    function handle_update_request($thisAction, $thisTargetListID, $handleDirect = false) {
        // load the TargetList
        $this->targetList->retrieve($thisTargetListID);

        // swithc the action
        switch ($thisAction) {
            case 'add':
                $this->add_targets($handleDirect);
                break;
            case 'rep':
                $this->replace_targets($handleDirect);
                break;
            case 'sub':
                $this->remove_targets($handleDirect);
                break;
        }
    }

    function add_targets($handleDirect = false) {
        $db = DBManagerFactory::getInstance();
        
        $this->targetList->load_relationships();
        $linkedFields = $this->targetList->get_linked_fields();

        foreach ($linkedFields as $linkedField => $linkedFieldData) {
            if ($this->targetList->$linkedField->_relationship->rhs_module == $this->KReport->report_module) {
                // success
                if (!$handleDirect) {
                    foreach ($this->KReportResults as $thisRecord) {
                        $this->targetList->$linkedField->add($thisRecord ['sugarRecordId']);
                    }
                } else {
                    $sqlArray = $this->KReport->get_report_main_sql_query();
                    $addPLSQL = "INSERT INTO prospect_lists_prospects (id, prospect_list_id, related_id, related_type, date_modified, deleted)
                                                            SELECT uuid(), '" . $this->targetList->id . "', reportselect.sugarRecordId, '" . $this->KReport->report_module . "', UTC_TIMESTAMP(), '0' FROM
                                                            (" . $sqlArray . ") as reportselect
                                                            WHERE NOT EXISTS(SELECT id FROM prospect_lists_prospects WHERE related_id = reportselect.sugarRecordId AND prospect_list_id='" . $this->targetList->id . "' AND deleted=0)";
                    $db->query($addPLSQL);
                }
            }
        }
    }

    function remove_targets($handleDirect = false) {
        $db = DBManagerFactory::getInstance();
        
        $this->targetList->load_relationships();
        $linkedFields = $this->targetList->get_linked_fields();

        foreach ($linkedFields as $linkedField => $linkedFieldData) {
            if ($this->targetList->$linkedField->_relationship->rhs_module == $this->KReport->report_module) {
                // success
                if (!$handleDirect) {
                    foreach ($this->KReportResults as $thisRecord) {
                        $this->targetList->$linkedField->delete($this->targetList->id, $thisRecord ['sugarRecordId']);
                    }
                } else {
                    $db->query("UPDATE prospect_lists_prospects SET deleted='1' WHERE prospect_list_id='" . $this->targetList->id . "' AND related_type = '" . $this->KReport->report_module . "' AND deleted='0'");
                }
            }
        }
    }

    function replace_targets($handleDirect = false) {
        $db = DBManagerFactory::getInstance();

        // flag all records as deleted
        $db->query("UPDATE prospect_lists_prospects SET deleted='1' WHERE prospect_list_id='" . $this->targetList->id . "' AND related_type = '" . $this->KReport->report_module . "' AND deleted='0'");

        // add the records
        $this->add_targets($handleDirect);
    }

}


