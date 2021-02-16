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
use SpiceCRM\includes\SugarObjects\SpiceConfig;

require_once('modules/KReports/KReport.php');
require_once('modules/KReports/Plugins/prototypes/kreportpresentationplugin.php');

class kreportpresentationpivot extends kreportpresentationplugin {

   var $defaultWidth = 100;
   var $totalColumnWidth = 80;
   var $nameWidth = 150;
   var $totalColumns = 0;
   var $minColumnWidth = 0;
   var $fieldArray = array();
   var $pParams = array();
   public $requestParams = array();
   // 2013-05-16 Bug #482 
   // memorize when we are exporting 
   var $exporting = false;

   public function __construct($requestParams = array()){
       $this->requestParams = $requestParams;
   }
   
   public function display(&$thisReport) {
      $viewJS = '<script type="text/javascript" src="custom/modules/KReports/Plugins/Presentation/pivot/js/viewpivot' . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . '.js"></script>';
      return $viewJS;
   }

   public function getExportData($thisReport, $dynamicols = '', $renderFields = false, $parentBean = null, $pluginName = null) {

      // 2013-05-16 Bug #482 
      // set the exporting flag
      $this->exporting = true; 
      
      // return false;
      if (isset($dynamicols) && $dynamicols != '') {
         $dynamicolsOverride = json_decode($dynamicols, true);
         $overrideMap = array();
         foreach ($dynamicolsOverride as $thisOverrideKey => $thisOverrideEntry) {
            if (!empty($thisOverrideEntry['dataIndex']))
               $overrideMap[$thisOverrideEntry['dataIndex']] = $thisOverrideEntry['width'];
         }
      }

      $exportData = array();

      $pivotData = $this->generatePivot($thisReport, 0, 1000, $renderFields);

      $exportData['datasets']['main'] = $pivotData['records'];
      if (!empty($pivotData['recordtotal']))
         $exportData['datasetsummaries']['main'] = $pivotData['recordtotal'];

      // build the ExportHeader
      //$exportData['headerDisplay']['maxDepth'] = count($this->pParams['pluginData']['columnData']) + 1 ;
      $headerDisplay = array();
      $fieldArrayNew = array();
      $currColumnIndex = 0;
      foreach ($pivotData['metaData']['gridcolumns'] as $thisColumn) {
         // 2013-05-16 ... bug #480 properly render fields in the Pivot Export
         // added this Report 
         $currColumnIndex += $this->addHeaderDisplay($headerDisplay, $fieldArrayNew, 0, $currColumnIndex, $thisColumn, $thisReport);
      }
      $exportData['headerDisplay'] = $headerDisplay;
      $exportData['fieldArray'] = $fieldArrayNew;
      foreach ($fieldArrayNew as $fieldId => $fieldData) {
         $exportData['width'][$fieldId] = (!empty($overrideMap[$fieldId]) && $overrideMap[$fieldId] > 0 ? $overrideMap[$fieldId] : 120);

         if (empty($thisReport->listFieldArrayById[$fieldId]))
            $exportData['alignment'][$fieldId] = 'C';
      }

      return $exportData;
   }

   private function addHeaderDisplay(&$headerDisplay, &$fieldArray, $rowIndex, $colIndex, $headerItem, $thisReport = null) {
      if (empty($headerItem['columns'])) {
         // we have a simple cell 
         $headerDisplay[] = array(
             'text' => $headerItem['text'],
             'rowIndex' => $rowIndex,
             'colIndex' => $colIndex,
             'rowSpan' => count($this->pParams['pluginData']['columnData']) + 1 - $rowIndex,
             'colSpan' => 1
         );
         if (!empty($headerItem['dataIndex']))

         // 2013-05-16 ... bug #480 properly render fields in the Pivot Export
         // check the renderer ... 
            $renderer = $thisReport->getXtypeRenderer($thisReport->getFieldTypeById($headerItem['dataIndex']), $headerItem['dataIndex']);
         if ($renderer === false)
            $renderer = 'k' . $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] . 'Renderer';

         $fieldArray[$headerItem['dataIndex']] = array(
             'fieldid' => $headerItem['dataIndex'],
             // 'renderer' => 'k' . $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] . 'Renderer',
             'renderer' => $renderer,
             'summaryRenderer' => $renderer,
             'alignment' => 'center'
         );
         return 1;
      }
      else {
         $thisColspan = 0;
         foreach ($headerItem['columns'] as $thisColumnItem) {
            $thisColspan += $this->addHeaderDisplay($headerDisplay, $fieldArray, $rowIndex + 1, $colIndex + $thisColspan, $thisColumnItem, $thisReport);
         }
         $headerDisplay[] = array(
             'text' => $headerItem['text'],
             'rowIndex' => $rowIndex,
             'colIndex' => $colIndex,
             'rowSpan' => 1,
             'colSpan' => $thisColspan
         );
         if (!empty($headerItem['dataIndex']))
            $fieldArray[$headerItem['dataIndex']] = array(
                'fieldid' => $headerItem['dataIndex'],
                'renderer' => '',
                'alignment' => 'center'
            );
         return $thisColspan;
      }
   }

   public function generatePivot($thisReport, $thisSnapshot = 0, $thisPanelWidth = 1000, $renderFields = true, $sort = null) {

      global $mod_strings;
      
      // set request Paramaters
      $reportParams = array('noFormat' => true, 'start' => 0, 'limit' => 0);
      if($sort !== null){
            $sortParams = json_decode(html_entity_decode($sort));
            $reportParams['sortid'] = $sortParams[0]->property;
            $reportParams['sortseq'] = $sortParams[0]->direction;
      }
      
        //get parent bean 
        if(isset($this->requestParams['parentbeanId']) && isset($this->requestParams['parentbeanModule'])){
            $parentbean = BeanFactory::getBean($this->requestParams['parentbeanModule'], $this->requestParams['parentbeanId']);
            if($parentbean->id)
                $reportParams['parentbean'] = $parentbean;
        }

      $reportRecords = $thisReport->getSelectionResults($reportParams, $thisSnapshot, false);
      // get the presentation params
      $this->pParams = json_decode(html_entity_decode($thisReport->presentation_params), true);

      // see if we have individual width settings
      if ($this->pParams['pluginData']['advancedOptions']['nameWidth'] > 0)
         $this->nameWidth = $this->pParams['pluginData']['advancedOptions']['nameWidth'];

      if ($this->pParams['pluginData']['advancedOptions']['minWidth'] > 0)
         $this->minColumnWidth = $this->pParams['pluginData']['advancedOptions']['minWidth'];

      $pivotData = array();
      $fieldData = array();
      $columnLevels = array();
      $addRowDataArray = array();
      foreach ($reportRecords as $thisRecord) {
         $fieldIndex = '';
         $fieldIndexDetails = array();
         foreach ($this->pParams['pluginData']['columnData'] as $columnIndex => $columnData) {
            // concatenate the values so we get a fieldID we aggregate on
            $fieldIndex .= $thisRecord[$columnData['fieldid']];
            $fieldIndexDetails[$columnIndex] = $thisRecord[$columnData['fieldid']];

            // memorize the values for the decoding in the Drilldown .. 
            // check for _val since that holds the orginial enum value as 
            // selected from the DB before interpretation through app_list_strings
            $fieldIndexDrillDownValues[$columnData['fieldid']] = !empty($thisRecord[$columnData['fieldid'].'_val']) ? $thisRecord[$columnData['fieldid'].'_val'] : $thisRecord[$columnData['fieldid']];
            
            $columnLevels[$columnIndex][$thisRecord[$columnData['fieldid']]] = $thisRecord[$columnData['fieldid']];
         }

         // normalize the fieldindex with an MD5 Hash
         $fieldIndex = md5($fieldIndex);
         
         // keep the values for the drilldown report selection... 
         $fieldIndexDrillDownArray[$fieldIndex] = $fieldIndexDrillDownValues;

         // see if we know the field already
         if (!isset($fieldData[$fieldIndex])) {
            $fieldData[$fieldIndex] = $fieldIndexDetails;
         }

         // also normalize the Rowid with an MD5 Hash
         $rowId = md5($thisRecord[$this->pParams['pluginData']['rowData']]);

         // see if we have a record for the row
         if (!isset($pivotData[$rowId])) {
            $pivotData[$rowId] = array(
                'id' => $rowId,
                $this->pParams['pluginData']['rowData'] => $thisRecord[$this->pParams['pluginData']['rowData']]
            );

            //2013-01-22 add add row fields
            foreach ($this->pParams['pluginData']['addRowData'] as $addRowIndex => $addRowData) {
               $addRowDataArray[$addRowData['fieldid']] = $addRowData['name'];
               $pivotData[$rowId][$addRowData['fieldid']] = $thisRecord[$addRowData['fieldid']];
            }
         }

         // process the value for the row
         if (count($this->pParams['pluginData']['valueData']) > 1000) {
            foreach ($this->pParams['pluginData']['valueData'] as $valueIndex => $valueData) {
               $pivotData[$rowId][$fieldIndex][$valueIndex] += $thisRecord[$valueData['fieldid']];
            }
         } else {
            $pivotData[$rowId][$fieldIndex] += $thisRecord[$this->pParams['pluginData']['valueData'][0]['fieldid']];
         }
      }

      // build the fields aray for the store and the column array for the grid
      $fieldArr = array();
      $fieldArr[] = array('name' => 'id');
      $fieldArr[] = array('name' => $this->pParams['pluginData']['rowData']);

      //2013-01-22 add add Rows
      foreach ($this->pParams['pluginData']['addRowData'] as $addRowIndex => $addRowData) {
         $fieldArr[] = array('name' => $addRowData['fieldid']);
      }

      // see if we have to calc the width
      $pivotDataWidth = 0;
      if ($this->pParams['pluginData']['advancedOptions']['adjustwidth'] == 1 && $thisPanelWidth > 0) {
         // -4 for frames, -14 for scrollbars
         $gridWith = $thisPanelWidth - $this->nameWidth - 4 - (count($this->pParams['pluginData']['addRowData']) * $this->nameWidth) - 14;
         $pivotDataWidth = $gridWith - ($this->pParams['pluginData']['advancedOptions']['showTotal'] == 1 ? $this->totalColumnWidth : 0);
      }

      // add all fields to the Fieldarr for the config of the Store
      foreach ($fieldData as $fieldId => $thisfieldData) {
         $fieldArr[] = array('name' => $fieldId);
      }

      // build the col Array for the pivot data
      // get the fomatter
      $this->renderer = $thisReport->getXtypeRenderer($thisReport->getFieldTypeById($this->pParams['pluginData']['valueData'][0]['fieldid']), $this->pParams['pluginData']['valueData'][0]['fieldid']);

      $columnsArray = $this->buildPivotColumns($fieldData, $this->pParams['pluginData']['columnData'], $columnLevels, $pivotDataWidth, $this->pParams['pluginData']['advancedOptions']['sortValues'], $this->pParams['pluginData']['advancedOptions']['showEmpty']);
      // finalize the column Array
      $columnArr = array(
          array('text' => $thisReport->listFieldArrayById[$this->pParams['pluginData']['rowData']]['name'], 'locked' => false, 'dataIndex' => $this->pParams['pluginData']['rowData'], 'flex' => 1),
      );

      //2013-01-22 add add Rows
      foreach ($this->pParams['pluginData']['addRowData'] as $addRowIndex => $addRowData)
         $columnArr[] = array('text' => $addRowData['name'], 'locked' => false, 'dataIndex' => $addRowData['fieldid'], 'width' => $this->nameWidth);

      $columnArr[] = array('text' => $mod_strings['LBL_PIVOT_LBLPIVOTDATA'], 'columns' => $columnsArray);

      // build the recordArray
      $finalArray = array();

      // get a renderer Object
      $thisRenderer = new KReportRenderer();

      foreach ($pivotData as $recordData) {
         // an empty array for the new record
         $newRecord = array();

         // build the total
         $totalSum = 0;

         // loop over all fields making sure the record fort the store is complete
         foreach ($fieldArr as $fieldData) {

            if (count($this->pParams['pluginData']['valueData']) > 1000) {
               // TODO ...
            } else {
               if ($renderFields && $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] != '' && empty($addRowDataArray[$fieldData['name']]) && $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] != '-' && $fieldData['name'] != 'id' && $fieldData['name'] != $this->pParams['pluginData']['rowData']) {
                  $renderer = 'k' . $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] . 'Renderer';
                  //catch duplicate setting to currency (once in "available fields", once in "values")
                  if($renderer == 'kcurrencyRenderer' && $this->renderer == 'kcurrencyRenderer') $renderer = 'knumberRenderer';
                  
                  $newRecord[$fieldData['name']] = $thisRenderer->$renderer($fieldData['name'], $recordData);
               }
               else
                  $newRecord[$fieldData['name']] = $recordData[$fieldData['name']];

               // build the total
               if ($fieldData['name'] != 'id' && $fieldData['name'] != $this->pParams['pluginData']['rowData'] && empty($addRowDataArray[$fieldData['name']]) && $this->pParams['pluginData']['advancedOptions']['showTotal'] == 1)
                  $totalSum += $recordData[$fieldData['name']];

               // build the sum
               if ($fieldData['name'] != 'id' && $fieldData['name'] != $this->pParams['pluginData']['rowData'] && empty($addRowDataArray[$fieldData['name']]) && $this->pParams['pluginData']['advancedOptions']['showSum'] == 1) {
                  $totalArray['recordtotal'][$fieldData['name']] += $recordData[$fieldData['name']];

                  if ($this->pParams['pluginData']['advancedOptions']['showTotal'] == 1)
                     $totalArray['recordtotal']['total'] += $recordData[$fieldData['name']];
               }
            }
         }

         // add the total
         if ($this->pParams['pluginData']['advancedOptions']['showTotal'] == 1) {
            if (count($this->pParams['pluginData']['valueData']) > 1000) {
               // TODO ... 
            } else {
               $newRecord['total'] = $totalSum;

               if ($renderFields && $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] != '' && $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] != '-') {
                  $renderer = 'k' . $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] . 'Renderer';
                  $newRecord['total'] = $thisRenderer->$renderer('total', $newRecord);
               }
               else
                  $newRecord['total'] = $totalSum;
            }
         }        
         // append the record
         $finalArray[] = $newRecord;
      }

      // add the sum to the arrays
      if ($this->pParams['pluginData']['advancedOptions']['showTotal'] == 1) {
         $fieldArr[] = array('name' => 'total');
         $columnArr[] = array('text' => 'total', 'dataIndex' => 'total', 'align' => 'center', 'width' => $this->totalColumnWidth);
      }

      if ($this->pParams['pluginData']['advancedOptions']['showSum'] == 1) {
         foreach ($totalArray['recordtotal'] as $fieldId => $fieldTotal) {
            // 2013-05-29 bug when no renderer is set than this runs into an error since the method is not defined 
            if ($renderFields 
                    && $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] != '' 
                    && $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] != '-'
                    && $fieldId === 'total'){
                $renderer = 'k' . $this->pParams['pluginData']['valueData'][0]['pivotrenderer'] . 'Renderer';
                $totalArray['recordtotal'][$fieldId] = $thisRenderer->$renderer($fieldId, $totalArray['recordtotal']);
            }
            else
               $totalArray['recordtotal'][$fieldId] = $totalArray['recordtotal'][$fieldId];
         }
         $totalArray['recordtotal']['id'] = 'grid-row-summary';
         $totalArray['recordtotal'][$this->pParams['pluginData']['rowData']] = 'total';
      }

      // build the return Array
      $totalArray['records'] = $finalArray;
      $totalArray['metaData'] = array(
          'totalProperty' => 'count',
          'root' => 'records',
          'fields' => $fieldArr,
          'count' => count($finalArray),
          'showSum' => ($this->pParams['pluginData']['advancedOptions']['showSum'] == 1 ? true : false),
          'gridcolumns' => $columnArr, 
          'drilldowndata' => $fieldIndexDrillDownArray
      );

      // done!!!
      return $totalArray;
   }

   // build Column Header
   private function buildPivotColumns($fields, $columns, $columnValues, $pivotDataWidth, $sortValues = false, $showEmptyValues = true, $flat = false) {
      $tmpColArray = array();
      $columnDepth = count($columns);
      if ($showEmptyValues) {
         $tmpColArray = $this->buildFullTmpColArray(1, $columnDepth, $columnValues);
      } else {
         $tmpColArray = $this->buildFieldTmpColArray($columnDepth, $fields);
      }

      // calculate the width of each column
      if ($pivotDataWidth != 0 && $this->totalColumns != 0)
         $columnWidth = $pivotDataWidth / $this->totalColumns;
      else
         $columnWidth = $this->defaultWidth;

      if (!$flat) {
         $columnArray = $this->buildPivotColumnsFromArray($tmpColArray, '', ($columnWidth > $this->minColumnWidth ? $columnWidth : $this->minColumnWidth), $sortValues);
         return $columnArray;
      }
      else
         return $tmpColArray;
   }

   private function buildFieldTmpColArray($maxDepth, $fields) {
      $tmpColArray = array();
      $evalString = '$tmpColArray';
      for ($i = 0; $i < $maxDepth; $i++) {
         $evalString .= '[$fieldData[' . $i . ']]';
      };
      $evalString .= ' = $fieldId;';
      foreach ($fields as $fieldId => $fieldData) {
         eval($evalString);

         // add to the total columns Counter
         $this->totalColumns++;
      }
      return $tmpColArray;
   }

   /*
    * function that calls itself recursive to build an n layer deep array
    */

   private function buildFullTmpColArray($thisDepth, $maxDepth, $columnValues) {
      $tmpColArray = array();

      if ($thisDepth == $maxDepth) {
         $tmpColArray = $columnValues[$thisDepth - 1];
         // add to the total columns Counter
         $this->totalColumns += count($columnValues[$thisDepth - 1]);
      } else {
         foreach ($columnValues[$thisDepth - 1] as $thisColumnValue) {
            $tmpColArray[$thisColumnValue] = $this->buildFullTmpColArray($thisDepth + 1, $maxDepth, $columnValues);
         }
      }

      return $tmpColArray;
   }

   /*
    * function that calls itself recursively to transform the field array in an Sencha Column Array
    */

   private function buildPivotColumnsFromArray($columnArray, $curPathString, $columnWidth, $sortValues) {
      // check if we should sort .. then sort this level 
      if ($sortValues)
         ksort($columnArray);

      // build the array recursive
      $thisColumnArray = array();
      foreach ($columnArray as $thisColumnValue => $thisSubColumns) {
         if (is_array($thisSubColumns)) {
            $thisColumnArray[] = array(
                'text' => $thisColumnValue,
                'columns' => $this->buildPivotColumnsFromArray($thisSubColumns, $curPathString . $thisColumnValue, $columnWidth, $sortValues)
            );
         } else {
            $thisColumnArray[] = array(
                // 2013-05-18 Bug #482
                // if we are exporting do not wrap headers !!!
                'text' => ($this->pParams['pluginData']['advancedOptions']['rotateHeaders'] && !$this->exporting ? '<div style="-webkit-transform: rotate(-90deg);-moz-transform: rotate(-90deg); -o-transform: rotate(-90deg);filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);">' : '') . $thisColumnValue . ($this->pParams['pluginData']['advancedOptions']['rotateHeaders'] && !$this->exporting ? '</div>' : ''),
                'dataIndex' => md5($curPathString . $thisColumnValue),
                'align' => 'center',
                'minWidth' => $columnWidth,
                'flex' => '1',
                'renderer' => 'renderField',
                'fieldrenderer' => $this->renderer,
                'height' => ($this->pParams['pluginData']['advancedOptions']['rotateHeaders'] ? strlen($thisColumnValue) * 10 : 'auto')
            );

            // keep track of the fields for the Object (for the data Export)
            $this->fieldArray[] = array(
                'text' => $thisColumnValue,
                'id' => md5($curPathString . $thisColumnValue),
                'width' => $columnWidth
            );
         }
      }
      // off we go
      return $thisColumnArray;
   }

}


