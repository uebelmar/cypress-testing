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
use SpiceCRM\includes\SugarObjects\SpiceConfig;

require_once('modules/KReports/Plugins/prototypes/kreportpresentationplugin.php');

class kreportpresentationstandardwp extends kreportpresentationplugin {
    /**
     * @deprecated
     * @param $thisReport
     * @return string
     */
   public function display(&$thisReport) {

      //get the presentation view parameters
      $pParams = json_decode(html_entity_decode($thisReport->presentation_params));

      // $fieldArray = htmlentities($fieldArray, ENT_QUOTES, 'UTF-8');
      $viewJS = '<script type="text/javascript">FieldsArray = ' . htmlentities($this->buildFieldArray($thisReport)) . ';</script>';
      // $thisview->ss->assign('listfieldarray', $fieldArray);

      $viewJS .= '<script type="text/javascript">ColumnsArray = ' . str_replace('"', '', json_encode($this->buildColumnArray($thisReport))) . ';previewId=\'' . $pParams->pluginData->previewId . '\';</script>';
      $viewJS .= '<script type="text/javascript" src="custom/k/extjs4/ux/RowExpander.js"></script>';
      $viewJS .= '<script type="text/javascript" src="custom/modules/KReports/Plugins/Presentation/standardwpreview/js/viewstandardwpreview' . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . '.js"></script>';
      return $viewJS;
   }

   public function getExportData($thisReport, $dynamicols = '', $renderFields = true, $parentBean = null, $pluginName = null) {
      
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
      // 2013-08-25 .. BUG#494 moved since we need to get the sort order first 
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
                'renderer' => $thisReport->getXtypeRenderer($thisReport->getFieldTypeById($thisField['fieldid']), $thisField['fieldid'])
            );
         }
      }

      // send back the fieldArray ... 2013-05-08 ... Bug #476
      $exportData['fieldArray'] = $fieldArray;

      //run through the results
      foreach ($reportResults as $resultRecord) {
         $recordArray = array();
         foreach ($fieldArray as $fieldId => $fieldData) {
            if ($fieldData['renderer'] != ''){
               //PHP7 - 5.6 COMPAT
               //ORIGINAL: $recordArray[$fieldId] = $thisReportRenderer->$fieldData['renderer']($fieldId, $resultRecord);
               $rendererFn = $fieldData['renderer'];
                    //CR1000401 fix currency format
                    if($pluginName == 'Excel' && preg_match("/^kcurrency/", $rendererFn)){
                        $recordArray[$fieldId] = $resultRecord[$fieldId];
                        $recordArray[$fieldId.'_curid'] = $resultRecord[$fieldId.'_curid'];
                    } else {
                        $recordArray[$fieldId] = $thisReportRenderer->$rendererFn($fieldId, $resultRecord);
                    }
                    //END
               //END
            }
            else
               $recordArray[$fieldId] = $resultRecord[$fieldId];
         }
         $exportData['datasets']['main'][] = $recordArray;
      }

      // those we leave empty
      $exportData['datasettitles'] = array();

      // process the summary data
      $exportData['datasetsummaries'] = array();

      // return the 
      return $exportData;
   }

   public function buildFieldArray($thisReport) {
      // build the Field List for the JSON Reader
      $arrayList = json_decode(html_entity_decode($thisReport->listfields, ENT_QUOTES), true);
      $fieldArray = '[';

      foreach ($arrayList as $thisList) {
         if ($fieldArray != '[')
            $fieldArray .= ',';
         $fieldArray .= '{name : \'' . trim($thisList['fieldid'], ':') . '\'}';

         // see if we nee to add a field for the currency_id to the store 2010-12-25 
         // change to check if set to avoid php notice
         if ((isset($thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['type']) && $thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['type'] == 'currency') || (isset($thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['kreporttype']) && $thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['kreporttype'] == 'currency'))
            $fieldArray .= ',{name : \'' . trim($thisList['fieldid'], ':') . '_curid\'}';
      }

      //$fieldArray .= trim($fieldArray, ',');
      $fieldArray .= "]";

      return $fieldArray;
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
                'renderer' => 'renderField'
            );

            if(isset($thisList['width']) && $thisList['width'] != '' && $thisList['width'] != '0'){
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
