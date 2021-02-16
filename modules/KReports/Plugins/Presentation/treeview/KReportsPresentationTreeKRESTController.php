<?php

namespace SpiceCRM\modules\KReports\Plugins\Presentation\treeview;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

class KReportsPresentationTreeKRESTController
{
    function buildColumnArray($req, $res, $args) {
        //get the presentation view parameters
        $thisReport = BeanFactory::getBean('KReports');
        $thisReport->retrieve($args['report']);

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
                    'fieldid' => trim($thisList['fieldid'], ':'),
                    'path' => $thisList['path'],
                    'link' => $thisList['link'],
                    'hidden' => ($thisList['display'] != 'yes' ? true : false),
                    'align' => $thisReport->getXtypeAlignment($thisFieldType, $thisList['fieldid']),
                    'renderer' => 'renderField',
                    'type' => $thisReport->getFieldTypeById($thisList['fieldid']),
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
        return $res->withJson($columnArray);
    }

    function getNode($req, $res, $args){
        global $app_list_strings;
        $app_list_strings = return_app_list_strings_language($GLOBALS['current_language']);

        // devoce the node
        $args['node'] = utf8_encode(base64_decode(urldecode($args['node'])));

        // get the body
        $requestParams = $req->getParsedBody();

        //BEGIN 2018-03-15 maretval: missing snapshots handling
        $snapshotid = (isset($requestParams['snapshotid']) ? $requestParams['snapshotid'] : '0');
        //END

        // processing
        $thisReport = BeanFactory::getBean('KReports', $args['report']);

        // set the override Where if set in the request
        if (isset($requestParams['whereConditions'])) {
            $thisReport->whereOverride = json_decode(html_entity_decode($requestParams['whereConditions']), true);
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
        if(!empty($requestParams['filter'])){
            $filter = DBManagerFactory::getInstance()->fetchByAssoc(DBManagerFactory::getInstance()->query("SELECT selectedfilters FROM kreportsavedfilters WHERE id = '".$requestParams['filter']."'"));
            $thisReport->whereOverride = json_decode(html_entity_decode($filter['selectedfilters']), true);
        }

        $currentGroupLevel = 1;
        $filterArray = [];

        //build the filter for the node ..
        if (isset($args['node']) && $args['node'] != 'root') {
            $tmp_filterArray = preg_split('/::/', $args['node']);
            foreach ($tmp_filterArray as $filterSeq => $filterDef) {
                $filterEntryArray = preg_split('/:/', $filterDef);
                $filterArray[$filterEntryArray[0]] = $filterEntryArray[1];
            }
            $currentGroupLevel = count($filterArray) + 1;
        }

        // get the results for the node
        //$maxGroupLevel = $thisReport->getMaxGroupLevel();
        // get the grouping fields
        $listTypeProperties = json_decode(html_entity_decode($thisReport->presentation_params));

        $arrayList = json_decode(html_entity_decode($thisReport->listfields, ENT_QUOTES), true);

        // asses the grouping depth .. index of the last node
        $groupdepth = 0;
        while($arrayList[$groupdepth]['fieldid'] != $listTypeProperties->pluginData->stopTreeAt &&  $groupdepth < 10)
            $groupdepth++;

        // increase by 1 since the last is the last level
        $groupdepth++;

        //reportParams
        $reportParams = array('noFormat' => true, 'toPDF' => true);

        //get parent bean
        if(isset($requestParams['parentbeanId']) && isset($requestParams['parentbeanModule'])){
            $parentbean = BeanFactory::getBean($requestParams['parentbeanModule'], $requestParams['parentbeanId']);
            if($parentbean->id)
                $reportParams['parentbean'] = $parentbean;
        }

        //if($currentGroupLevel > $maxGroupLevel)
        if ($currentGroupLevel < $groupdepth) {
            // since we are not at the end we change the field functions
            $listFields = json_decode(html_entity_decode($thisReport->listfields, ENT_QUOTES), true);
            foreach ($arrayList as $listFieldKey => $listFieldData) {
                if (isset($listFieldData['summaryfunction']) && $listFieldData['summaryfunction'] != '')
                    $listFields[$listFieldKey]['sqlfunction'] = $listFieldData['summaryfunction'];
            }
            $thisReport->listfields = json_encode($listFields);
            $reportParams['exclusiveGrouping'] = true;

            //BEGIN 2018-03-15 maretval: missing snapshots handling
            //ORIGINAL: $resultRecords = $thisReport->getSelectionResults($reportParams, '0', false, $filterArray, array($arrayList[$currentGroupLevel - 1]['fieldid']));
            $resultRecords = $thisReport->getSelectionResults($reportParams, $snapshotid, false, $filterArray, array($arrayList[$currentGroupLevel - 1]['fieldid']));
            //END
        }
        else {
            // 2011-03-25 no grouping on the lowest level excpet what the report groups anyway
            //BEGIN 2018-03-15 maretval: missing snapshots handling
            //ORIGINAL: $resultRecords = $thisReport->getSelectionResults($reportParams, '0', false, $filterArray, array() /* array($thisReportGroupings[count($thisReportGroupings) - 1]['fieldid']) */);
            $resultRecords = $thisReport->getSelectionResults($reportParams, $snapshotid, false, $filterArray, array() /* array($thisReportGroupings[count($thisReportGroupings) - 1]['fieldid']) */);
            //END
        }
        // now get the format ... first we did not format to keep original values for the later selection
        // need that for the ID
        // $formattedResultRecords =$thisReport->formatFields($resultRecords, false);
        //$levelFieldId = $thisReport->getGroupLevelId($currentGroupLevel);
        $levelFieldId = $arrayList[$currentGroupLevel - 1]['fieldid'];

        // get the list fields array since we need to check against that one
        $listFieldsAray = $thisReport->getListFieldsArray();

        foreach ($resultRecords as $thisRecordId => $thisRecordData) {
            $returnArray = array();
            // 2011-03-07 add the original value '_val' as id rather then the translated value
            // 2012-10-04 ... not said that the name is unique ... so at ethe end of the tree we return a GUID
            if ($currentGroupLevel < $groupdepth)
                $returnArray['id'] = (isset($args['node']) && $args['node'] != 'root' ? $args['node'] . '::' : '') . $levelFieldId . ':' . (isset($thisRecordData[$levelFieldId . '_val']) ? $thisRecordData[$levelFieldId . '_val'] : $thisRecordData[$levelFieldId]);
            else
                $returnArray['id'] = create_guid();

            $returnArray['leaf'] = $currentGroupLevel == $groupdepth /* $maxGroupLevel */ ? true : false;
            $returnArray['text'] = $thisRecordData[$arrayList[$currentGroupLevel - 1]['fieldid']];
            // process all the other entry fields
            foreach ($thisRecordData as $fieldId => $fieldValue) {
                $fieldIndex = array_search($fieldId,array_keys($listFieldsAray));

                if ($fieldIndex === false || $groupdepth  <= $currentGroupLevel || $fieldIndex < $groupdepth || $listFieldsAray[$fieldId]['sqlfunction'] != '-'){
                    $returnArray[$fieldId] = $thisRecordData[$fieldId];
                }else{
                    $returnArray[$fieldId] = '';
                }
            }



            // set the text if we still have a field
            /*
            if ($levelFieldId != '')
                $returnArray[$arrayList[count($thisReportGroupings) - 1]['fieldid']] = $thisRecordData[$arrayList[$currentGroupLevel - 1]['fieldid']];
            */
            //$returnArray['text'] = $thisFormattedRecordData[$levelFieldId];

            $return[] = $returnArray;
        }

        //json encode an return$thisReportGroupings$arrayList
        return $res->withJson($return);
    }
}
