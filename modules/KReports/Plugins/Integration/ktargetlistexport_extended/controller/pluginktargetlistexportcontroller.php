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


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

require_once('modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlisthandler.php');

class pluginktargetlistexportcontroller
{

    function action_export_to_targetlist($requestParams)
    {
        $thisReport = BeanFactory::getBean('KReports', $requestParams['record']);

        // check if we have set dynamic Options
        if (isset($requestParams['whereConditions'])) {
            $thisReport->whereOverride = json_decode(html_entity_decode(base64_decode($requestParams['whereConditions'])), true);
        }

        // initiate the handler
        // $thisTargetListHandler = new KReportTargetListHandler($thisReport);
        $integrationsettings = json_decode(html_entity_decode($thisReport->integration_params));

        // initiate the handler
        $thisTargetListHandler = new KReportTargetListHandler($thisReport, ($integrationsettings->ktargetlistexport->targetlist_create_direct == true ? false : true));

        if ($requestParams['targetlist_action'] == 'new') {
            $thisTargetListHandler->createTargeList($requestParams['targetlist_name'], $requestParams['campaign_id'], $integrationsettings->ktargetlistexport->targetlist_create_direct);
        } else {
            $thisTargetListHandler->handle_update_request($requestParams['taregtlist_update_action'], $requestParams['targetlist_id'], $integrationsettings->ktargetlistexport->targetlist_create_direct);
        }
        return true;
    }

    function action_gettargetlists($requestParams)
    {
        $db = DBManagerFactory::getInstance();

        $returnArray = array();

        if ($requestParams['query'])
            $targetListObj = $db->query("SELECT id, name FROM prospect_lists WHERE name like '%" . $requestParams['query'] . "%' AND deleted='0'");
        else

            $targetListObj = $db->query("SELECT id, name FROM prospect_lists WHERE deleted='0'");
        while ($prospect_list_record = $db->fetchByAssoc($targetListObj)) {
            $returnArray[] = array(
                'id' => $prospect_list_record['id'],
                'name' => $prospect_list_record['name']
            );
        }

        return $returnArray;
    }

    function action_getcampaigns($requestParams)
    {
        $db = DBManagerFactory::getInstance();

        $returnArray = array();
        $returnArray[] = array(
            'id' => '',
            'name' => '-'
        );

        if ($requestParams['query'])
            $campaignsObj = $db->query("SELECT id, name FROM campaigns WHERE name like '%" . $requestParams['query'] . "%' AND deleted='0'");
        else
            $campaignsObj = $db->query("SELECT id, name FROM campaigns WHERE deleted='0'");

        while ($campaign_record = $db->fetchByAssoc($campaignsObj)) {
            $returnArray[] = array(
                'id' => $campaign_record['id'],
                'name' => $campaign_record['name']
            );
        }

        return $returnArray;
    }

}


