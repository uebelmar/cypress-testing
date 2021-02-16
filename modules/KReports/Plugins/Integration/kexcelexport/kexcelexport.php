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
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

use SpiceCRM\modules\KReports\KReport;
use SpiceCRM\modules\KReports\KReportPresentationManager;

require_once('modules/KReports/Plugins/prototypes/kreportintegrationplugin.php');
require_once('modules/KReports/KReport.php');

class kexcelexport extends kreportintegrationplugin {

   var $currencySymbols = null;
   var $objPHPExcel = null;
   var $userDateFormat = 'yyyy/mm/dd';
   var $userTimeDateFormat = 'yyyy/mm/dd hh:mm';
   var $userUTCOffset = 0;

   public function __construct() {

      global $timedate;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
      
      $this->pluginName = 'Excel';

       //check on $GLOBALS['disable_date_format']! Else getUserUTCOffset() might not calculate offset on the right timezone
       //now get offset between timezones
       $this->userUTCOffset = $timedate->getUserUTCOffset() * 60;
       //reset $GLOBALS['disable_date_format'] as it was
      
      $this->userDateFormat = preg_replace(array('/d/', '/m/', '/Y/'), array('dd', 'mm', 'yyyy'), $timedate->get_date_format($current_user));
      $this->userTimeDateFormat = preg_replace(array('/d/', '/m/', '/Y/', '/H/', '/i/', '/a/', '/A/'), array('dd', 'mm', 'yyyy', 'hh', 'mm', 'AM/PM', 'AM/PM'), $timedate->get_date_time_format($current_user));

       $this->userIntFormat = (isset(SpiceConfig::getInstance()->config['kreporter']['kintRenderer']['format']) ?: '#,###');
       $this->userFloatFormat = (isset(SpiceConfig::getInstance()->config['kreporter']['kfloatRenderer']['format']) ?: '#,###0.00');

   }

   protected function br2nl($string) {
      return strip_tags(str_replace('<br>', ' ', $string));
   }

   //2013-03-18 Bug #437
   public function getColumnName($colIndex) {
      if ($colIndex >= 26) {
          if($colIndex <52){ //AB, AC cols
              $retVal = chr(65) . chr(65 + ($colIndex % 26));
          }
          else{ //BA, BC cols
              $retVal = chr(64 + round($colIndex / 26, 0)) . chr(65 + ($colIndex % 26));
          }
          //end
      }
      else
         $retVal = chr(65 + $colIndex);

      return $retVal;
   }

   public function exportToExcel($reportId, $dynamicols = '', $exportto = 'xlsx', $parentbean) {

      $thisReport = new KReport();
      $thisReport->retrieve($reportId);

      // 2013-03-13 check for custom filtering 
      if (isset($_REQUEST['dynamicoptions'])) {
         $_REQUEST['whereConditions'] = $_REQUEST['dynamicoptions'];
         $thisReport->whereOverride = json_decode(html_entity_decode($_REQUEST['dynamicoptions']), true);
      }

      require_once('modules/KReports/KReportPresentationManager.php');
      $thisPresManager = new KReportPresentationManager();

      // see if the Presdentation Plugin provides a separate export, if not take the default
      $exportData = $thisPresManager->getPresentationExport($thisReport, $dynamicols, false, $parentbean, $this->pluginName);
      if (!$exportData)
         $exportData = $this->getDefaultPresentationExport($thisReport, $dynamicols, $parentbean);

      // some settings
      //date_default_timezone_set('Europe/London');
      date_default_timezone_set('UTC');
      define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
      require_once('vendor/phpexcel/PHPExcel.php');
      $this->objPHPExcel = new PHPExcel();
      $this->objPHPExcel->setActiveSheetIndex(0);

      // loop over the array
      // start at A1
      //2013-03-18 Bug #437
      $startingCell = 0;
      $startingRow = 1;

      // manage alignment
      $this->setAlignments($startingCell, $startingRow, $exportData['fieldArray']);

      // process Datasets
      foreach ($exportData['datasets'] as $datasetid => $datasetData) {
         $startingRow = $this->writeDataset($startingRow, $startingCell, $exportData['header'], $exportData['headerDisplay'], $exportData['width'], $datasetData, $exportData['datasettitles'][$datasetid], $exportData['datasetsummaries'][$datasetid], $exportData['fieldArray']);

         $startingRow++;
      }

      // send to the Output
      switch ($exportto) {
         case 'xlsx':
            $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
            ob_clean();
            ob_start();
            $objWriter->save('php://output');
            return ob_get_clean();
            break;
         //2013-04-16 support xls export Bug #467
         case 'xls':
            $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
            ob_clean();
            ob_start();
            $objWriter->save('php://output');
            return ob_get_clean();
            break;
         case 'csv':
            $filename = "kexcel.csv";
            header('Content-type: application/ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            $objWriter = new PHPExcel_Writer_CSV($this->objPHPExcel);
            $objWriter->save('php://output');
            break;
         case 'pdf':
            $filename = "kexcel.pdf";
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename=' . $filename);
            $rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
            $rendererLibraryPath = 'vendor/tcpdf6';
            PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath);
            $objWriter = new PHPExcel_Writer_PDF($this->objPHPExcel);
            $objWriter->save('php://output');
            break;
         case 'html':
            $objWriter = new PHPExcel_Writer_HTML($this->objPHPExcel);
            return $objWriter->generateSheetData();
            break;
      }
   }

   private function setAlignments($startingCell, $thisRow, $fieldArray) {
      foreach ($fieldArray as $thisField) {
         // get the type for the cell and format alignment properly
         $this->setCellAlignment($this->objPHPExcel, $this->getColumnName($startingCell), $thisRow, $thisField['alignment']);
         $startingCell++;
      }
   }

   private function writeDataset($startingRow, $startingCell, $header, $headerDisplay, $width, $dataset, $datasetTitle, $datsetSummary, $fieldArray) {

      if ($datasetTitle != '') {
         $this->objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->getColumnName($startingCell) . $startingRow, $datasetTitle);

         // merge the cells
         $this->objPHPExcel->getActiveSheet()->mergeCells($this->getColumnName($startingCell) . $startingRow . ':' . $this->getColumnName($startingCell + count($header) - 1) . $startingRow);

         // apply Format
         $this->objPHPExcel->getActiveSheet()->getStyle($this->getColumnName($startingCell) . $startingRow)->applyFromArray(array('font' => array('bold' => true)));

         // next Row
         $startingRow++;
      }

      $columnArray = array();

      if (!is_array($headerDisplay)) {
         // save the startung cell ... will need it later again 
         $saveStartingCell = $startingCell;
         $this->setAlignments($startingCell, $startingRow, $fieldArray);
         foreach ($fieldArray as $thisFieldId => $thisField) {
            // 2013-03-05 Decode the Value
            $this->objPHPExcel->getActiveSheet()->setCellValue($this->getColumnName($startingCell) . $startingRow, html_entity_decode($header [$thisFieldId], ENT_QUOTES));
            $this->objPHPExcel->getActiveSheet()->getStyle($this->getColumnName($startingCell) . $startingRow)->applyFromArray(array('font' => array('italic' => true)));
            $startingCell++;
         }
         // set back to the starting cell
         $startingCell = $saveStartingCell;
      } else {
         $rowOffset = 1;
         $maxColIndex = 1;
         $maxRowIndex = 1;
         foreach ($headerDisplay as $thisHeaderDisplayCell) {
            $this->objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->getColumnName($startingCell + $thisHeaderDisplayCell['colIndex']) . ($startingRow + $thisHeaderDisplayCell['rowIndex']), $thisHeaderDisplayCell['text']);

            if ($thisHeaderDisplayCell['rowIndex'] > $rowOffset)
               $rowOffset = $thisHeaderDisplayCell['rowIndex'];
            // merge the cells
            if ($thisHeaderDisplayCell['rowSpan'] > 1 || $thisHeaderDisplayCell['colSpan'] > 1)
               $this->objPHPExcel->getActiveSheet()->mergeCells($this->getColumnName($startingCell + $thisHeaderDisplayCell['colIndex']) . ($startingRow + $thisHeaderDisplayCell['rowIndex']) . ':' . $this->getColumnName($startingCell + $thisHeaderDisplayCell['colIndex'] + $thisHeaderDisplayCell['colSpan'] - 1) . ($startingRow + $thisHeaderDisplayCell['rowIndex'] + $thisHeaderDisplayCell['rowSpan'] - 1));

            $this->objPHPExcel->getActiveSheet()->getStyle($this->getColumnName($startingCell + $thisHeaderDisplayCell['colIndex']) . ($startingRow + $thisHeaderDisplayCell['rowIndex']))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->objPHPExcel->getActiveSheet()->getStyle($this->getColumnName($startingCell + $thisHeaderDisplayCell['colIndex']) . ($startingRow + $thisHeaderDisplayCell['rowIndex']) . ':' . $this->getColumnName($startingCell + $thisHeaderDisplayCell['colIndex'] + $thisHeaderDisplayCell['colSpan'] - 1) . ($startingRow + $thisHeaderDisplayCell['rowIndex'] + $thisHeaderDisplayCell['rowSpan'] - 1))->applyFromArray(array('font' => array('italic' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'EEEEEE')), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CCCCCC')))));
         }

         $startingRow += $rowOffset;
      }
      $startingRow++;

      // set the width and formatter
      foreach ($fieldArray as $thisFieldId => $thisField) {


         $this->objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnName($startingCell))->setWidth($width[$thisFieldId] / 7);


         $columnArray[$thisField['fieldid']] = array(
             'column' => $this->getColumnName($startingCell),
             'renderer' => $thisField['renderer'],
             'alignment' => $thisField['alignment']
         );
         $startingCell++;
      }

      // need this for the sums
      $startDataAt = $startingRow;

      // process the datasets
      if (count($dataset > 0)) {
         foreach ($dataset as $record) {

            foreach ($record as $key => $value) {
               if (isset($columnArray[$key])) {
                  // 2013-03-05 Decode the Value
                  $value = html_entity_decode($value, ENT_QUOTES);

                  $this->setCellFormat($this->objPHPExcel, $columnArray[$key]['column'] . $startingRow, $record, $key, $columnArray[$key]['renderer']);
                  switch ($columnArray[$key]['renderer']) {
                     case 'kcurrencyRenderer':
                     case 'kcurrencyintRenderer':
                     case 'knumberRenderer':
                     case 'kintRenderer':
                     case 'kdateRenderer':
                     case 'kdatetimeRenderer':
                     case 'kdatetutcRenderer':
                     case 'kpercentageRenderer':
                        $this->objPHPExcel->setActiveSheetIndex(0)->setCellValue($columnArray[$key]['column'] . $startingRow, $this->transformValue($value, $columnArray[$key]['renderer']));
                        break;
                     case 'ktextRenderer':
                        $this->objPHPExcel->setActiveSheetIndex(0)->getCell($columnArray[$key]['column'] . $startingRow)->setValueExplicit($this->transformValue($value, $columnArray[$key]['renderer']), PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->objPHPExcel->getActiveSheet()->getStyle($columnArray[$key]['column'] . $startingRow)->getAlignment()->setWrapText(true);
                        break;
                     default:
                        $this->objPHPExcel->setActiveSheetIndex(0)->getCell($columnArray[$key]['column'] . $startingRow)->setValueExplicit($this->transformValue($value, $columnArray[$key]['renderer']), PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                  }
               }
            }

            // set alignment 
            $this->setAlignments($startingCell, $startingRow, $fieldArray);

            $startingRow++;
         }
      }

      // see if we have a summary
      if ($datsetSummary != '') {
         foreach ($columnArray as $columnId => $columnData) {
            if (isset($datsetSummary[$columnId])) {
               $this->setCellFormat($this->objPHPExcel, $columnData['column'] . $startingRow, $datsetSummary, $columnId, $columnData['renderer']);

               // see if we can apply a formula
               $xlsFunction = $this->translateSummaryfunction($fieldArray[$columnId]['summaryfunction']);
               if ($xlsFunction != '')
                  $this->objPHPExcel->getActiveSheet()->setCellValue($columnData['column'] . $startingRow, '=' . $xlsFunction . '(' . $columnData['column'] . $startDataAt . ':' . $columnData['column'] . ($startingRow - 1) . ')');
               else
                  switch ($columnData['renderer']) {
                     case 'kcurrencyRenderer':
                     case 'kcurrencyintRenderer':
                     case 'knumberRenderer':
                     case 'kintRenderer':
                     case 'kdateRenderer':
                     case 'kdatetimeRenderer':
                     case 'kpercentageRenderer':
                        $this->objPHPExcel->setActiveSheetIndex(0)->setCellValue($columnData['column'] . $startingRow, $this->transformValue($datsetSummary[$columnId], $columnData['renderer']));
                        break;
                     case 'ktextRenderer':
                        $this->objPHPExcel->setActiveSheetIndex(0)->getCell($columnArray[$key]['column'] . $startingRow)->setValueExplicit($this->transformValue($datsetSummary[$columnId], $columnArray[$key]['renderer']), PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->objPHPExcel->getActiveSheet()->getStyle($columnArray[$key]['column'] . $startingRow)->getAlignment()->setWrapText(true);
                        break;
                     default:
                        $this->objPHPExcel->setActiveSheetIndex(0)->getCell($columnData['column'] . $startingRow)->setValueExplicit($this->transformValue($datsetSummary[$columnId], $columnData['renderer']), PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                  }

               // apply Format
               $this->objPHPExcel->getActiveSheet()->getStyle($columnData['column'] . $startingRow)->applyFromArray(array('font' => array('bold' => true, 'italic' => true)));
            }
         }
         $startingRow++;
      }

      return $startingRow;
   }

   private function translateSummaryfunction($function) {
      switch (strtolower($function)) {
         case 'avg':
            return 'AVERAGE';
            break;
         case 'sum':
            return 'SUM';
            break;
         case 'max':
            return 'MAX';
            break;
         case 'min':
            return 'MIN';
            break;
         default:
            return '';
            break;
      }
   }

   private function getDefaultPresentationExport($thisReport, $dynamicols = '', $parentbean = null) {
      
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

      if($parentbean) $reportParams['parentbean'] = $parentbean;

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
             if ($fieldArray[$fieldId]['renderer'] == 'kcurrencyRenderer' || $fieldArray[$fieldId]['renderer'] == 'kcurrencyintRenderer') {
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

   private function writeHeader($thisSheet, $thisReport) {
      $thisSheet->setActiveSheetIndex(0)->setCellValue('A1', 'Report');
      $thisSheet->setActiveSheetIndex(0)->setCellValue('B1', $thisReport->name);
   }

   // function to transfor a value depending on tyope. eg needed for date since this needs to be converted to a number
   private function transformValue($value, $type) {

      switch ($type) {
         case 'kdateRenderer':
            // cut off the time
            // 2013-03-13 check if value is empty
            return (empty($value) ? '' : PHPExcel_Shared_Date::PHPToExcel(strtotime(substr($this->br2nl($value), 0, 10))));
            break;
         case 'kdatetimeRenderer':
             // convert fully
            // 2013-03-13 check if value is empty
            return (empty($value) ? '' : PHPExcel_Shared_Date::PHPToExcel(strtotime($this->br2nl($value)) + $this->userUTCOffset));
            break;           
         case 'kdatetutcRenderer':
            // convert fully
            // 2013-03-13 check if value is empty
            return (empty($value) ? '' : PHPExcel_Shared_Date::PHPToExcel(strtotime($this->br2nl($value))));
            break;
         case 'kpercentageRenderer':
            return $value / 100;
            break;
         case 'ktextRenderer':
            return strip_tags(str_replace('<br>', "\n", $value));
            break;
         default;
            //2013-01-09 ... html decode .. Jasons feedback
            return html_entity_decode(strip_tags($value));
            break;
      }
   }

   // formatter .. currency needs a currency sign, date  date format etc. 
   private function setCellFormat($theSheet, $theCell, $record, $fieldid, $fieldtype) {
      global $timedate;
$current_user = AuthenticationController::getInstance()->getCurrentUser();

      switch ($fieldtype) {
          case 'kcurrencyintRenderer':
          case 'kcurrencyRenderer':
              if ($this->currencySymbols == null)
                  $this->loadCurrencySymbols();
              if (isset($record[$fieldid . '_curid']))
                  $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode((isset($record[$fieldid . '_curid']) && isset($this->currencySymbols[$record[$fieldid . '_curid']]) ? $this->currencySymbols[$record[$fieldid . '_curid']] : '*') . ' * #,##0'.($fieldtype=='kcurrencyRenderer'? '.00' : ''));
              else
                  $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode((isset($this->currencySymbols['-99']) ? $this->currencySymbols['-99'] : '*') . ' * #,##0'.($fieldtype=='kcurrencyRenderer'? '.00' : ''));
              break;
         case 'kdateRenderer':
            // 2013-03-13 set user date format 
            // $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode('dd.mm.yyyy');
            $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode($this->userDateFormat);
            break;
         case 'kdatetimeRenderer':
         case 'kdatetutcRenderer':
            // 2013-03-13 set user date format
            // $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode('dd.mm.yyyy hh:mm:ss');
            //2013-05-18 changed proper formatting in XLS
            $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode($this->userTimeDateFormat);
            break;
         case 'kpercentageRenderer':
            $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            break;
         case 'kintRenderer':
             $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode($this->userIntFormat);
             break;
         case 'knumberRenderer':
             $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode($this->userFloatFormat);
             break;
         default:
            // format as text
            $theSheet->getActiveSheet()->getStyle($theCell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            break;
      }
   }

   // is called to format the header cell in the excel sheet
   private function setCellAlignment($theSheet, $theCell, $theRow, $alignment) {
      switch ($alignment) {
         case 'right':
            $theSheet->getActiveSheet()->getStyle($theCell . $theRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            break;
         case 'center':
            $theSheet->getActiveSheet()->getStyle($theCell . $theRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            break;
      }
   }

   private function loadCurrencySymbols() {
      // get currencies
      $curResArray = DBManagerFactory::getInstance()->query('SELECT id, symbol FROM currencies WHERE deleted = \'0\'');

      $this->currencySymbols = array();
      $this->currencySymbols['-99'] = SpiceConfig::getInstance()->config['default_currency_symbol'];
      while ($thisCurEntry = DBManagerFactory::getInstance()->fetchByAssoc($curResArray)) {
         $this->currencySymbols[$thisCurEntry['id']] = $thisCurEntry['symbol'];
      }
   }

}
