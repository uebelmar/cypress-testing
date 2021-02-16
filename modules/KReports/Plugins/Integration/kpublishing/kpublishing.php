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

require_once('modules/KReports/Plugins/prototypes/kreportintegrationplugin.php');

class kpublishing extends kreportintegrationplugin {

    public function __construct() {
        $this->pluginName = 'Publish Report';
    }

    public function checkAccess($thisReport) {
        return true;
    }

    public function getMenuItem() {
        return '';
    }

    /*
     * Function that adds all related reports that are published as subpanels
     * for a specific module to the layoutdefs
     * 
     * gets called from include/SubPanel/SubPanelDefinition.php
     */

    static function addToLayoutDefs(&$layout_defs, $layout_def_key) {
        $db = DBManagerFactory::getInstance();

        //$queryRes = $db->query("SELECT id, name, publishoptions FROM kreports WHERE deleted='0' AND publishoptions LIKE '%\"publishSubpanelModule\":\"" . $layout_def_key . "\"%'");
        //$queryRes = $db->query("SELECT id, name, integration_params FROM kreports WHERE deleted='0' AND integration_params LIKE '%\"kpublishing\":\"1\"%' AND integration_params like '%\"subpanelModule\":\"" . $layout_def_key . "\"%'");
        $queryRes = $db->query("SELECT id, name, integration_params FROM kreports WHERE deleted='0' AND (integration_params LIKE '%\"kpublishing\":\"1\"%' OR integration_params LIKE '%\"kpublishing\":1%') AND integration_params like '%\"subpanelModule\":\"" . $layout_def_key . "\"%'");
        if ($db->getRowCount($queryRes) > 0) {
            while ($thisReportDetails = $db->fetchByAssoc($queryRes)) {
                $integration_params = json_decode(html_entity_decode($thisReportDetails ['integration_params'], ENT_QUOTES, 'UTF-8'));

                if ($integration_params->kpublishing->subpanelPresentation == 'on') {
                    $layout_defs ['subpanel_setup'] ['kreporterpres' . $thisReportDetails ['id']] = array('reportId' => $thisReportDetails ['id'], 'top_buttons' => array(), 'subpanel_name' => 'default', 'order' => $integration_params->kpublishing->subpanelSequence, 'module' => 'KReports', 'title_key' => $thisReportDetails ['name']);
                }

                if ($integration_params->kpublishing->subpanelVisualization == 'on') {
                    $layout_defs ['subpanel_setup'] ['kreportervisu' . $thisReportDetails ['id']] = array('reportId' => $thisReportDetails ['id'], 'top_buttons' => array(), 'subpanel_name' => 'default', 'order' => $integration_params->kpublishing->subpanelSequence, 'module' => 'KReports', 'title_key' => $thisReportDetails ['name']);
                }
            }
        }
    }

    /*
     * function that sorts subpanels into tabs
     * called from include/SubPanel/SubPanelTilesTabs.php
     */

    static function addToTabs(&$tabs, &$groups, &$found, &$tabStructure) {
        $db = DBManagerFactory::getInstance();

        foreach ($tabs as $tabId) {
            if (strstr($tabId, 'kreporterpres') !== false || strstr($tabId, 'kreportervisu') !== false) {
                $queryRes = $db->query("SELECT id, name, integration_params FROM kreports WHERE deleted='0' AND id = '" . substr($tabId, 13) . "'");
                if ($db->getRowCount($queryRes) > 0) {
                    $thisReportDetails = $db->fetchByAssoc($queryRes);
                    $integration_params = json_decode(html_entity_decode($thisReportDetails ['integration_params'], ENT_QUOTES, 'UTF-8'));
                    if ($integration_params->kpublishing->subpanelTab != '') {
                        $groups[translate($integration_params->kpublishing->subpanelTab)]['modules'][] = $tabId;
                        $found[$tabId] = true;
                    }
                }
            }
        }
    }

    static function renderSubpanel($subPanel, $parentBean = null) {

        // presentation view
        if (strpos($subPanel->subpanel_id, 'kreporterpres') !== false) {
            $reportId = str_replace('kreporterpres', '', $subPanel->subpanel_id);
            $tpl = 'modules/KReports/Plugins/Integration/kpublishing/tpls/kpresentationpublishsubpanel.tpl';
        }        

        // graphical view
        if (strpos($subPanel->subpanel_id, 'kreportervisu') !== false) {
            $reportId = str_replace('kreportervisu', '', $subPanel->subpanel_id);
            $tpl = 'modules/KReports/Plugins/Integration/kpublishing/tpls/kvisualizationpublishsubpanel.tpl';
        }
        
        //generate and return
        $sm = new Sugar_Smarty();
        $sm->assign('subpanelid',$subPanel->subpanel_id);
        $sm->assign('subpanelrecord',$reportId);
        $sm->assign('origin',$_REQUEST['action']); //tell if subpanel called manually => action = SubPanelViewer
        return $sm->fetch($tpl); 

    }

    public function getDefaultPresentationExport($thisReport, $dynamicols = '') {

        $reportParams = array('toCSV' => true, 'noFormat' => true);

        // get the fields
        $fieldList = json_decode(html_entity_decode($thisReport->listfields), true);

        //see if we have dynamic cols in the Request ...
        $dynamicolsOverride = array();
        if (isset($dynamicols) && $dynamicols != '') {
            $dynamicolsOverride = json_decode($dynamicols, true);
            $overrideMap = array();
            foreach ($dynamicolsOverride as $thisOverrideKey => $thisOverrideEntry) {
                $overrideMap[$thisOverrideEntry['dataIndex']] = $thisOverrideKey;

                // 2013-08-24 .. BUG#494 check sorting
                if(!empty($thisOverrideEntry['sortState'])){
                    $reportParams['sortseq'] = $thisOverrideEntry['sortState'];
                    $reportParams['sortid'] = $thisOverrideEntry['dataIndex'];
                }
            }

            //loop over the listfields
            for ($i = 0; $i < count($fieldList); $i++) {
                if (isset($overrideMap[$fieldList[$i]['fieldid']])) {
                    // set the display flag
                    if ($dynamicolsOverride[$overrideMap[$fieldList[$i]['fieldid']]]['isHidden'] == 'true')
                        $fieldList[$i]['display'] = 'no';
                    else
                        $fieldList[$i]['display'] = 'yes';

                    // set the width
                    $fieldList[$i]['width'] = $dynamicolsOverride[$overrideMap[$fieldList[$i]['fieldid']]]['width'];

                    // set the sequence
                    $fieldList[$i]['sequence'] = $dynamicolsOverride[$overrideMap[$fieldList[$i]['fieldid']]]['sequence'];
                }
            }

            // resort the array
            usort($fieldList, 'sortFieldArrayBySequence');
        }

        // get the report results
        $reportResults = $thisReport->getSelectionResults($reportParams);


        // to return we create an emtpy array
        $exportData = array();

        // process the header
        $totalWidth = 0;
        $fieldArray = array();
        foreach ($fieldList as $thisField) {
            if ($thisField['display'] == 'yes') {
                $exportData['header'][$thisField['fieldid']] = $thisField['name'];

                $exportData['width'][$thisField['fieldid']] = $thisField['width'];

                // separat small fieldarray
                $fieldArray[$thisField['fieldid']] = array(
                    'fieldid' => $thisField['fieldid'],
                    'summaryfunction' => $thisField['summaryfunction'],
                    'renderer' => $thisReport->getXtypeRenderer($thisReport->getFieldTypeById($thisField['fieldid']), $thisField['fieldid']),
                    'alignment' => $thisReport->getXtypeAlignment($thisReport->getFieldTypeById($thisField['fieldid']), $thisField['fieldid'])
                );
            }
        }

        // send back the fieldArray
        $exportData['fieldArray'] = $fieldArray;

        //run through the results
        foreach ($reportResults as $resultRecord) {
            $recordArray = array();
            foreach ($fieldArray as $fieldId => $fieldData) {
                $recordArray[$fieldId] = $resultRecord[$fieldId];

                //treat currency Fields seperately
                if ($fieldArray[$fieldId]['renderer'] == 'kcurrencyRenderer') {
                    $recordArray[$fieldId . '_curid'] = $resultRecord[$fieldId . '_curid'];
                }
            }
            $exportData['datasets']['main'][] = $recordArray;
        }

        // those we leave empty
        $exportData['datasettitles'] = array();
        $exportData['datasetsummaries'] = array();

        // return the
        return $exportData;
    }

}
