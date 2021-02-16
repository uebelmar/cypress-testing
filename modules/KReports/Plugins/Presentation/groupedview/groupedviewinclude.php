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


use SpiceCRM\modules\KReports\KReportRenderer;

require_once('modules/KReports/Plugins/prototypes/kreportpresentationplugin.php');

class kreportpresentationgrouped extends kreportpresentationplugin {

    public function getExportData($thisReport, $dynamicols = '', $renderFields = true, $parentBean = null, $pluginName = null) {

        // get the group criteria
        $listViewData = json_decode(html_entity_decode($thisReport->listtypeproperties));
        $groupCriteria = $listViewData->groupedViewProperties->groupById;

        // 2013-08-24 .. BUG#494 set the Report Paramaters up here .. sorting can be added
        $reportParams = array('toPDF' => true, 'noFormat' => true);

        // instance of the renderer
        $thisReportRenderer = new KReportRenderer($thisReport);

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
                if (!empty($thisOverrideEntry['sortState'])) {
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

                    // 2012-12-03 .. BUG#494 override the groupby if set
                    if ($dynamicolsOverride[$overrideMap[$fieldList[$i]['fieldid']]]['groupby'] == true)
                        $groupCriteria = $fieldList[$i]['fieldid'];
                }
            }

            // resort the array
            usort($fieldList, 'sortFieldArrayBySequence');
        } else {
            // determine the default group criteria
            $presentaitonParams = json_decode(html_entity_decode($thisReport->presentation_params), true);
            $groupCriteria = $presentaitonParams['pluginData']['groupedViewProperties']['groupById'];
        }

        // get the report results
        // 2013-08-25 moved since we need to get the sort order first 
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

        // return the fieldArray
        $exportData['fieldArray'] = $fieldArray;

        //run through the results
        $tmpSummaries = array();
        foreach ($reportResults as $resultRecord) {
            $recordArray = array();
            foreach ($fieldArray as $fieldId => $fieldData) {
                if ($fieldData['renderer'] != '' && $renderFields){                   
                    $rendererFn = $fieldData['renderer']; //PHP7 COMPAT
                    $recordArray[$fieldId] = $thisReportRenderer->$rendererFn($fieldId, $resultRecord);
                }
                else {
                    $recordArray[$fieldId] = $resultRecord[$fieldId];

                    //special treatment for currencies
                    if ($fieldData['renderer'] == 'kcurrencyRenderer' || $fieldData['renderer'] == 'kcurrencyintRenderer')
                        $recordArray[$fieldId . '_curid'] = $resultRecord[$fieldId . '_curid'];
                }

                if ($fieldData['summaryfunction'] != '') {
                    switch ($fieldData['summaryfunction']) {
                        case 'sum':
                        case 'avg':
                            $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId] += $resultRecord[$fieldId];
                            break;
                        case 'min':
                            if (!isset($tmpSummaries[$resultRecord[$groupCriteria]][$fieldId]) || $resultRecord[$fieldId] < $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId])
                                $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId] = $resultRecord[$fieldId];
                            break;
                        case 'max':
                            if (!isset($tmpSummaries[$resultRecord[$groupCriteria]][$fieldId]) || $resultRecord[$fieldId] > $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId])
                                $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId] = $resultRecord[$fieldId];
                            break;
                        case 'count':
                            if (empty($tmpSummaries[$resultRecord[$groupCriteria]][$fieldId]))
                                $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId] = 1;
                            // 2014-04-07 bug #518 number was wrong by one 
                            else
                                $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId] ++;
                            break;
                    }

                    // BEGIN CR1000402 add _curid field for summary
                    if(isset($resultRecord[$fieldId. '_curid'])){
                        $tmpSummaries[$resultRecord[$groupCriteria]][$fieldId . '_curid'] = $resultRecord[$fieldId. '_curid'];
                    }
                    // END
                }
            }
            $exportData['datasets'][$resultRecord[$groupCriteria]][] = $recordArray;

            if (!isset($exportData['datasettitles'][$resultRecord[$groupCriteria]]))
                $exportData['datasettitles'][$resultRecord[$groupCriteria]] = $resultRecord[$groupCriteria];
        }

        // sort the array so we have the same reuslt as in the grid
        ksort($exportData['datasets']);

        // process the summaries
        if (count($tmpSummaries) > 0) {
            foreach ($fieldArray as $fieldId => $fieldData) {
                foreach ($exportData['datasets'] as $datasetId => $datasetData) {
                    switch ($fieldData['summaryfunction']) {
                        case 'avg':
                            $tmpSummaries[$datasetId][$fieldId] = $tmpSummaries[$datasetId][$fieldId] / count($datasetData);
                            if ($fieldData['renderer'] != '' && $renderFields){
                                //PHP7 - 5.6 COMPAT
                                //ORIGINAL: $exportData['datasetsummaries'][$datasetId][$fieldId] = $thisReportRenderer->$fieldData['renderer']($fieldId, $tmpSummaries[$datasetId]);
                                $rendererFn = $fieldData['renderer'];
                                $exportData['datasetsummaries'][$datasetId][$fieldId] = $thisReportRenderer->$rendererFn($fieldId, $tmpSummaries[$datasetId]);
                                //END
                            }
                            else
                                $exportData['datasetsummaries'][$datasetId][$fieldId] = $tmpSummaries[$datasetId][$fieldId];
                            break;
                        default:
                            if ($fieldData['renderer'] != '' && $renderFields){
                                //PHP7 - 5.6 COMPAT
                                //ORIGINAL: $exportData['datasetsummaries'][$datasetId][$fieldId] = $thisReportRenderer->$fieldData['renderer']($fieldId, $tmpSummaries[$datasetId]);
                                $rendererFn = $fieldData['renderer'];
                                $exportData['datasetsummaries'][$datasetId][$fieldId] = $thisReportRenderer->$rendererFn($fieldId, $tmpSummaries[$datasetId]);
                                //END
                            }
                            else
                                $exportData['datasetsummaries'][$datasetId][$fieldId] = $tmpSummaries[$datasetId][$fieldId];
                            break;
                    }

                    // BEGIN CR1000402 use currency set in first dataset to render in summary
                    if($fieldData['renderer'] && preg_match("/^kcurrency/", $fieldData['renderer'])) {
                        $exportData['datasetsummaries'][$datasetId][$fieldId . '_curid'] = $exportData['datasets'][$datasetId][0][$fieldId.'_curid'];
                    }
                    // END
                }
            }
        }

        // return the 
        return $exportData;
    }

    public function buildColumnArray($thisReport) {
        //get the presentation view parameters
        $pParams = json_decode(html_entity_decode($thisReport->presentation_params));

        $arrayList = json_decode(html_entity_decode($thisReport->listfields, ENT_QUOTES), true);
        $columnArray = array();
        foreach ($arrayList as $thisList) {
            if ($thisList['fieldid'] != $pParams->pluginData->previewId && $thisList['display'] != 'hid') {
                $thisFieldType = ($thisList['overridetype'] == '' ? $thisReport->getFieldTypeById($thisList['fieldid']) : $thisList['overridetype']);
                $thisColumn = array(
                    //2013-03-05 html entities to support special chars in Text
                    //2013-04-19 added UTF-8 support
                    'text' => htmlentities($thisList['name'], ENT_QUOTES, 'UTF-8'),
                    // 'width' => ((isset($thisList['width']) && $thisList['width'] != '' && $thisList['width'] != '0') ? $thisList['width'] : '150'),
                    'sortable' => ($thisList['sort'] != '' && $thisList['sort'] != '-' ? 'true' : 'false'),
                    'dataIndex' => trim($thisList['fieldid'], ':'),
                    'hidden' => ($thisList['display'] != 'yes' ? true : false),
                    'align' => $thisReport->getXtypeAlignment($thisFieldType, $thisList['fieldid']),
                    'renderer' => 'renderField',
                );

                if (isset($thisList['width']) && $thisList['width'] != '' && $thisList['width'] != '0') {
                    $thisColumn['width'] = $thisList['width'];
                } else {
                    $thisColumn['flex'] = 1;
                }

                // see if we have renderer we need to process
                $renderer = $thisReport->getXtypeRenderer($thisReport->getFieldTypeById($thisList['fieldid']), $thisList['fieldid']);
                if ($renderer != '')
                    $thisColumn['fieldrenderer'] = $renderer;
                //2012-12-01 add default renderer for buuilding links
                else
                    $thisColumn['fieldrenderer'] = "fieldRenderer";

                // see if we have alignment we need to process
                $alignment = $thisReport->getXtypeAlignment($thisReport->getFieldTypeById($thisList['fieldid']), $thisList['fieldid']);
                if ($alignment != '')
                    $thisColumn['align'] = $alignment;

                // see if the summary is set
                if ($thisList['summaryfunction'] != '') {
                    $thisColumn['summaryType'] = $thisList['summaryfunction'];

                    // also set the renderer to the same as the column
                    if ($renderer != '')
                        $thisColumn['summaryFormatter'] = $renderer;
                    else
                        $thisColumn['summaryFormatter'] = 'fieldRenderer';

                    $hasSummary = true;
                }

                $columnArray[] = $thisColumn;
            }
        }
        return $columnArray;
    }

    public function getPresentationMetaData($thisReport) {
        return array(
            'gridColumns' => $this->buildColumnArray($thisReport)
        );
    }

}
