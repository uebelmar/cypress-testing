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

require_once('modules/KReports/Plugins/Presentation/pivot/kreportpivot.php');

class pluginpivotcontroller
{
    public function action_load_report($requestParams)
    {
        $db = DBManagerFactory::getInstance();

        $thisReport = BeanFactory::getBean('KReports', $requestParams['record']);

        // set the override Where if set in the request
        if (isset($requestParams['whereConditions'])) {
            $thisReport->whereOverride = json_decode(html_entity_decode($requestParams['whereConditions']));
        }

        //catch dynamic options sent by drilldown plugin at first load
        if (isset($requestParams['dynamicoptions']) && !empty($requestParams['dynamicoptions']) && !$requestParams['blockDynamicoptions']) {
            $dynamicoptions = json_decode(html_entity_decode($requestParams['dynamicoptions']), true);
            if(count($thisReport->whereOverride) <= 0)
                $thisReport->whereOverride = $dynamicoptions;
            else{
                foreach($thisReport->whereOverride as $idx => $whereOverride){
                    foreach($dynamicoptions as $idxdo => $dynamicoption){
                        if($dynamicoption['fieldid'] == $whereOverride['fieldid'] ||
                            (isset($whereOverride['reference']) && $dynamicoption['reference'] == $whereOverride['reference'])
                        ){
                            $thisReport->whereOverride[$idx] = $dynamicoption;
                        }
                    }
                }
            }
        }

        // if a filter is set evaluate it .. comes from the dashlet
        if (!empty($requestParams['filter'])) {
            $filter = $db->fetchByAssoc($db->query("SELECT selectedfilters FROM kreportsavedfilters WHERE id = '" . $requestParams['filter'] . "'"));
            $thisReport->whereOverride = json_decode(html_entity_decode($filter['selectedfilters']), true);
        }

        $thisPivot = new kreportpresentationpivot($requestParams);

        return $thisPivot->generatePivot($thisReport, isset($requestParams['snapshotid']) ? $requestParams['snapshotid'] : '0', isset($requestParams['panelwidth']) ? $requestParams['panelwidth'] : 100, true, isset($requestParams['sort']) ? $requestParams['sort'] : null);
    }
}

