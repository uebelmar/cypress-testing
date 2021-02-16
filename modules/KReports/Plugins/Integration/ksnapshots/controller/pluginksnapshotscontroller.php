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

require_once('modules/KReports/KReport.php');

class pluginksnapshotscontroller {

    public function action_takeSnapshot($requestParams){

        $thisReport = BeanFactory::getBean('KReports', $requestParams['record']);
        // catch whereconditions to save in snapshot
        $whereconditions = json_decode($requestParams['whereconditions'], true);
        if(is_array($whereconditions)){
            $thisReport->whereOverride = $whereconditions;
        }
        $thisReport->takeSnapshot();
        return true;
    }

    function action_getFields($requestParams) {
        $report = BeanFactory::getBean('KReports', $requestParams['record']);
        $listfields = json_decode(html_entity_decode($report->listfields), true);
        $returnArray = array();

        foreach ($listfields as $thisListField) {
            if ($thisListField['display'] == 'yes')
                $returnArray[] = array (
                    'fieldid' => $thisListField['fieldid'],
                    'fieldname' => $thisListField['name']
                );
        }

        return $returnArray;
    }


    function action_getChart($requestParams) {

        require_once 'modules/KReports/Plugins/Integration/ksnapshots/ksnapshotanalyzer.php';

        $analyzer = new ksnapshotanalyzer();

        return $analyzer->analyzeSnapshots($requestParams['record'], $requestParams['charttype'], $requestParams['xaxisfield'], $requestParams['yaxisfield']);
    }
}
