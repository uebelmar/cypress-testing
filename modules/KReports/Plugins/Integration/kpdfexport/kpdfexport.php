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


use SpiceCRM\modules\KReports\KReportPresentationManager;
use SpiceCRM\modules\KReports\KReportRenderer;

require_once 'modules/KReports/Plugins/prototypes/kreportintegrationplugin.php';
require_once 'modules/KReports/KReport.php';
require_once 'vendor/tcpdf6/tcpdf.php';

class kpdf extends TCPDF {

   public function Header() {
      if ($this->header_xobjid < 0 || $this->header_xobjid === false) {
         // start a new XObject Template
         $this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
         $headerfont = $this->getHeaderFont();
         $headerdata = $this->getHeaderData();
         $this->y = $this->header_margin;
         $this->x = $this->original_lMargin;

         $cell_height = round(($this->cell_height_ratio * $headerfont[2]) / $this->k, 2);

         $cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
         $this->SetTextColor(0, 0, 0);
         // header title
         $this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
         $this->SetX($this->original_lMargin);
         $this->Cell($cw, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);

         // print an ending header line
         $this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
         $this->SetY((2.835 / $this->k) + max($imgy, $this->y));
         if ($this->rtl) {
            $this->SetX($this->original_rMargin);
         } else {
            $this->SetX($this->original_lMargin);
         }
         $this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
         $this->endTemplate();
      }
      // print header template
      $x = 0;
      $dx = 0;
      if ($this->booklet AND ( ($this->page % 2) == 0)) {
         // adjust margins for booklet mode
         $dx = ($this->original_lMargin - $this->original_rMargin);
      }
      if ($this->rtl) {
         $x = $this->w + $dx;
      } else {
         $x = 0 + $dx;
      }
      $this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
   }

   public function Footer() {
      $cur_y = $this->y;
      $this->SetTextColor(0, 0, 0);
      //set style for cell border
      $line_width = 0.85 / $this->k;
      $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

      $this->Cell(0, 0, date($GLOBALS['timedate']->get_date_time_format()), 'T', 0, 'L');

      if (is_null($this->pagegroups) || empty($this->pagegroups)) { //check is_null for php < 5.5
         $pagenumtxt = $this->l['w_page'] . ' ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages();
      } else {
         $pagenumtxt = $this->l['w_page'] . ' ' . $this->getPageNumGroupAlias() . ' / ' . $this->getPageGroupAlias();
      }
      $this->SetY($cur_y);
      //Print page number
      if ($this->getRTL()) {
         $this->SetX($this->original_rMargin);
         $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
      } else {
         $this->SetX($this->original_lMargin);
         $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'R');
      }
   }

   public function getPageBreakTrigger() {
      return $this->PageBreakTrigger;
   }

}

class kpdfexport extends kreportintegrationplugin {

   var $currencySymbols = null;
   var $pdf = null;
   var $pdfConfig = array();
   var $pdfParams = null;
   var $cellWidth = array();
   var $cellWidthByIndex = array();
   var $headerData;
   var $headerDisplayData;
   var $headerFormat;

   public function __construct() {
      $this->pluginName = 'PDF';

      // load the pdf config
      require 'modules/KReports/Plugins/Integration/kpdfexport/config/KReportPDF.php';
      $this->pdfConfig = $kreportPDFconfig;
   }

   public function exportToPDF($thisReport, $dynamicols = '', $visData = '', $outputmethod = 'S') {

      // create new PDF document
      $this->pdf = $this->initializePdf($thisReport);

      // add a page
      $this->pdf->AddPage();

      // render Selection Criteria
      $this->renderSelection($thisReport, $dynamicols);

       // render the Visualization
       if($visData != '') {
           //Fix 2017-01-13 PHP7 Compat: check if any $visData->objects are available
           //something like {"measures":{"width":0,"height":0},"objects":{}} might be indeed in $visData
           //in that case $this->renderVisualization() crashes the PDF Layout under PHP7
           if($vis = json_decode(html_entity_decode($visData, ENT_QUOTES, 'UTF-8'))){
               if(!empty(get_object_vars($vis->objects))){
                   $this->renderVisualization($thisReport, $visData);
               }
           }
       }

      // render the Presentation
      $this->renderPresentation($thisReport, $dynamicols);

      // generate the PDF and export it
      return $this->pdf->Output($this->generatePDFName($thisReport), $outputmethod);
   }

   private function initializePdf($thisReport) {
      // get the PDF Parameter
      $this->pdfParams = json_decode(html_entity_decode($thisReport->integration_params));

      if ($this->pdfParams->kpdfexport->pdf_layout == '')
         $this->pdfParams->kpdfexport->pdf_layout = 'default';

      $pdf = new kpdf(
              ($this->pdfParams->kpdfexport->pdf_orientation != '' ? $this->pdfParams->kpdfexport->pdf_orientation : 'P'), 'mm', ($this->pdfParams->kpdfexport->pdf_format != '' ? $this->pdfParams->kpdfexport->pdf_format : 'A4')
      );

      // set default header data
      $pdf->setHeaderData('', '', $thisReport->name);
      // $pdf->header_title = $thisReport->name;
      // set header and footer fonts
      $pdf->setHeaderFont(Array(
          (!empty($this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['pdfFormat']['pdfHeader']['fontName']) ? $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['pdfFormat']['pdfHeader']['fontName'] : PDF_FONT_NAME_MAIN),
          (!empty($this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['pdfFormat']['pdfHeader']['fontStyle']) ? $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['pdfFormat']['pdfHeader']['fontStyle'] : 'B'),
          (!empty($this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['pdfFormat']['pdfHeader']['fontSize']) ? $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['pdfFormat']['pdfHeader']['fontSize'] : PDF_FONT_SIZE_MAIN)));

      $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

      // set default monospaced font
      $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

      //set margins
      // 2013-03-18 corrected Top Margin Bug #442
      $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP - 10, PDF_MARGIN_RIGHT);
      $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
      $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

      //set auto page breaks
      $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

      //set image scale factor
      //$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
      // set font
      //       $pdf->SetFont('helvetica', '', 10);

      return $pdf;
   }

   public function generatePDFName($thisReport) {
      return 'kreporter_' . date('Ymd_His') . '.pdf';
   }

   private function renderSelection($thisReport, $dynamicols = '') {
      global $current_language;
      if ($this->pdfParams->kpdfexport->pdf_exportwhere && count($thisReport->whereOverride) > 0) {
         $this->headerFormat = $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationHeader'];
         //
         $startPos = array(
             'x' => $this->pdf->GetX() + $this->cellWidth['leftOffset'],
             'y' => $this->pdf->GetY()
         );

         // $this->pdf->SetFillColor(255, 255, 255);
         $this->setFormatFromArray($this->headerFormat);

         $dynamicols = json_decode($dynamicols, true);
         $this->pdf->MultiCell(40, 5, 'Name', 1, 'L', 1, 0, '', '', true);
         $this->pdf->MultiCell(40, 5, 'Operator', 1, 'L', 1, 0, '', '', true);
         $this->pdf->MultiCell(40, 5, 'value from', 1, 'L', 1, 0, '', '', true);
         $this->pdf->MultiCell(40, 5, 'value to', 1, 'L', 1, 0, '', '', true);
         $this->pdf->Ln();

         $oddRow = true;

         $moduleLanguagae = return_module_language($current_language, 'KReports');

         $whereFields = json_decode(html_entity_decode($thisReport->whereconditions), true);

         foreach ($thisReport->whereOverride as $thisCol) {


            if ($thisCol['operator'] == 'ignore')
               continue;

            $this->setFormatFromArray($oddRow ? $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationDataOdd'] : $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationDataEven']);

            // get the fieldname
            $fieldName = '';
            forEach ($whereFields as $whereField) {
               if ($whereField['fieldid'] == $thisCol['fieldid']) {
                  $fieldName = $whereField['name'];
                  break;
               }
            }

             //calculate height for criteria cell depending on criteria length
             $valueToDisplay = $thisCol['value'];
             if(!empty($thisCol['valueinit']))
                 $valueToDisplay = $thisCol['valueinit'];
             //$cellContent = (is_array($thisCol['value']) ? implode(",", $thisCol['value']) : $thisCol['value']);
             $cellContent = (is_array($valueToDisplay) ? implode(",", $valueToDisplay) : $valueToDisplay);
             $cellContentLength = strlen($cellContent);
             $cellHeight = ($cellContentLength <=0 ? 5 : (5 * ceil($cellContentLength/40)));

            // Output the selection criteria
            $this->pdf->MultiCell(40, $cellHeight, $fieldName, 1, 'L', 1, 0, '', '', true);
            $this->pdf->MultiCell(40, $cellHeight, $moduleLanguagae['LBL_OP_' . strtoupper($thisCol['operator'])], 1, 'L', 1, 0, '', '', true);
            $this->pdf->MultiCell(40, $cellHeight, $cellContent, 1, 'L', 1, 0, '', '', true);
            $this->pdf->MultiCell(40, $cellHeight, $thisCol['valueto'], 1, 'L', 1, 0, '', '', true);
            $this->pdf->Ln();

            $oddRow = !$oddRow;
            // $this->pdf->SetX($startPos['x'] + $thisHeaderDisplayCell[])
         }

         // add a page after that
         $this->pdf->AddPage();
      }
   }

   private function renderPresentation($thisReport, $dynamicols = '') {
      $thisPresManager = new KReportPresentationManager();

      $pdfData = $thisPresManager->getPresentationExport($thisReport, $dynamicols, true);
      if (!$pdfData)
         $pdfData = $this->getDefaultPresentationExport($thisReport, $dynamicols);

      // calculate the width for all cells
      $this->calculateCellWith($pdfData['width'], $pdfData['alignment'], $thisReport);

      // render the data
      $this->headerData = $pdfData['header'];
      $this->headerDisplayData = $pdfData['headerDisplay'];
      $this->headerFormat = $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationHeader'];

      $this->renderDataset($pdfData['datasets'], $pdfData['datasettitles'], $pdfData['datasetsummaries'], json_decode(html_entity_decode($thisReport->listfields), true));
   }

   private function renderDataset($dataset, $datasetTitles = array(), $datasetSummaries = array(), $listfields = array()) {

      foreach ($dataset as $thisDataset => $datasetData) {

         if ($this->pdfParams->kpdfexport->pdf_newpagepergroup)
            $this->pdf->AddPage();

         // checkif we have a titles
         if (!empty($datasetTitles[$thisDataset]))
            $this->renderDatasetTitle($datasetTitles[$thisDataset]);

         // render the header
         // $this->renderRow($header, $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationHeader']);
         $this->renderHeaderRow();

         $oddRow = true;
         foreach ($datasetData as $datasetid => $thisDataEntry) {
            $this->renderRow($thisDataEntry, ($oddRow ? $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationDataOdd'] : $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationDataEven']), $listfields);
            $oddRow = !$oddRow;
         }

         // render the summary
         if ($datasetSummaries[$thisDataset] != '')
            $this->renderRow($datasetSummaries[$thisDataset], $this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationSummary'], $listfields);
      }
   }

   private function renderDatasetTitle($title) {
      // add a line before
      $this->pdf->Ln();

      // format presentationDataset
      $this->setFormatFromArray($this->pdfConfig[$this->pdfParams->kpdfexport->pdf_layout]['presentationDataset']);

      // output the Cell
      // html escape the title to allow specical chars e.g. apostophes BUG #500
      $this->pdf->Cell(0, 0, html_entity_decode($title, ENT_QUOTES));
      $this->pdf->Ln();
   }

   /*
    * calculate the width in px for each cell by id
    */

   private function calculateCellWith($cellArray, $alignmentArray, $thisReport) {

      // check if alignment of datatable is set and not L and do the pre math
      if ($this->pdfParams->kpdfexport->pdf_palignment != '' && $this->pdfParams->kpdfexport->pdf_palignment != 'L') {
         $totalWidth = 0;
         foreach ($cellArray as $fieldId => $cellWidth)
            $totalWidth += $cellWidth * $this->pdf->pixelsToUnits(100) / 100;

         // get the page width
         $margins = $this->pdf->getMargins();
         $pageWidth = $this->pdf->getPageWidth() - $margins['left'] - $margins['right'];

         switch ($this->pdfParams->kpdfexport->pdf_palignment) {
            case 'R':
               $this->cellWidth['leftOffset'] = $pageWidth - $totalWidth;
               break;
            case 'C':
               $this->cellWidth['leftOffset'] = ($pageWidth - $totalWidth) / 2;
               break;
         }
      }

      // calculate the width for each cell
      $i = 0;
      foreach ($cellArray as $fieldId => $cellWidth) {

         switch ($this->pdfParams->kpdfexport->pdf_palignment) {
            case 'S':
               $cellWidth = ($cellWidth * $this->pdf->pixelsToUnits(100) / 100) / $totalWidth * $pageWidth;
               break;
            default:
               $cellWidth = $cellWidth * $this->pdf->pixelsToUnits(100) / 100;
               break;
         }

         $this->cellWidth[$fieldId] = array(
             'width' => $cellWidth,
             'alignment' => (!empty($alignmentArray[$fieldId]) ? $alignmentArray[$fieldId] : strtoupper(substr($thisReport->getXtypeAlignment($thisReport->getFieldTypeById($fieldId), $fieldId), 0, 1)))
         );

         // required for complex header
         $this->cellWidthByIndex[$i] = $cellWidth;
         $i++;
      }
   }

   private function setFormatFromArray($cellFormat) {
      // see if we have an offet
      if (isset($this->cellWidth['leftOffset']) && $this->cellWidth['leftOffset'] != 0)
         $this->pdf->SetX($this->pdf->GetX() + $this->cellWidth['leftOffset']);

      // set the fill color
      if (is_array($cellFormat['colors']['fill']) && count($cellFormat['colors']['fill']) == 3)
         $this->pdf->setFillColor($cellFormat['colors']['fill'][0], $cellFormat['colors']['fill'][1], $cellFormat['colors']['fill'][2]);

      // set the text color
      if (is_array($cellFormat['colors']['text']) && count($cellFormat['colors']['text']) == 3)
         $this->pdf->setTextColor($cellFormat['colors']['text'][0], $cellFormat['colors']['text'][1], $cellFormat['colors']['text'][2]);

      // set the border color
      if (is_array($cellFormat['colors']['border']) && count($cellFormat['colors']['border']) == 3)
         $this->pdf->setDrawColor($cellFormat['colors']['border'][0], $cellFormat['colors']['border'][1], $cellFormat['colors']['border'][2]);

      // set the font properties
      $this->pdf->SetFont($cellFormat['font']['familiy'], $cellFormat['font']['style'], $cellFormat['font']['size']);
   }

   private function renderHeaderRow() {
      if (!is_array($this->headerDisplayData))
         $this->renderRow($this->headerData, $this->headerFormat);
      else {
         $startPos = array(
             'x' => $this->pdf->GetX() + $this->cellWidth['leftOffset'],
             'y' => $this->pdf->GetY()
         );

         $this->setFormatFromArray($this->headerFormat);
         $rowOffset = 1;
         $lineHeight = $this->pdf->pixelsToUnits($this->headerFormat['font']['size'] + (isset($this->headerFormat['linespacing']) ? $this->headerFormat['linespacing'] : 4));

         foreach ($this->headerDisplayData as $thisHeaderDisplayCell) {

            if ($thisHeaderDisplayCell['rowIndex'] + $thisHeaderDisplayCell['rowSpan'] > $rowOffset)
               $rowOffset = $thisHeaderDisplayCell['rowIndex'] + $thisHeaderDisplayCell['rowSpan'];

            // calculate y offset and width
            $i = 0;
            $thisX = $startPos['x'];
            while ($i < $thisHeaderDisplayCell['colIndex']) {
               $thisX += $this->cellWidthByIndex[$i];
               $i++;
            };
            $ie = $i + $thisHeaderDisplayCell['colSpan'];
            $thisWidth = 0;
            while ($i < $ie) {
               $thisWidth +=$this->cellWidthByIndex[$i];
               $i++;
            }

            // calculate x offset and height
            $thisY = $startPos['y'] + ($thisHeaderDisplayCell['rowIndex'] * $lineHeight);
            $thisHeight = $lineHeight * $thisHeaderDisplayCell['rowSpan'];


            // Output the cell
            $this->pdf->MultiCell($thisWidth, $thisHeight, html_entity_decode($thisHeaderDisplayCell['text'], ENT_QUOTES), $this->headerFormat['border'], 'C', $this->headerFormat['fill'], 0, $thisX, $thisY, true, 0, false, true, $thisHeight, 'M');

            // $this->pdf->SetX($startPos['x'] + $thisHeaderDisplayCell[])
         }

         // set coordinates to bottom of the footer
         $this->pdf->SetY($startPos['y'] + ($rowOffset * $lineHeight));
      }
   }

   private function renderRow($rowData, $cellFormat, $listfields = array()) {

      // if we have multicell set
      if ($this->pdfParams->kpdfexport->pdf_multicell) {

         $maxHeight = $this->pdf->pixelsToUnits($cellFormat['font']['size'] + (isset($cellFormat['linespacing']) ? $cellFormat['linespacing'] : 4));
         foreach ($rowData as $thisId => $thisData) {
            if (!empty($this->cellWidth[$thisId])) {
               $thisHeight = $this->pdf->getStringHeight($this->cellWidth[$thisId]['width'], $this->br2nl(html_entity_decode($thisData)));
               if ($thisHeight > $maxHeight)
                  $maxHeight = $thisHeight;
            }
            
            //2016-07-06: render numbers according to overridetype
            foreach($listfields as $field => $fieldconfig) {
               if ( $fieldconfig['fieldid'] === $thisId && !empty($fieldconfig['overridetype'])){
                   switch($fieldconfig['overridetype']){
                       case 'float':
                       case 'number':
                           $rowData[$thisId] = format_number($rowData[$thisId]);
                           break;
                       case 'int':
                           $rowData[$thisId] = format_number($rowData[$thisId], 0, 0, array('human' => true));
                           break;
                   }
               }
            }

         }

         // check page break
         $fm = $this->pdf->getMargins();
         if (($this->pdf->GetY() + $maxHeight) > ($this->pdf->getPageHeight() - $fm['bottom'] /* - $fm['footer'] */)) {
            $this->pdf->AddPage();
            if ($this->pdfParams->kpdfexport->pdf_headerperpage)
               $this->renderHeaderRow();
         }

         $this->setFormatFromArray($cellFormat);

         // render the row
         foreach ($this->cellWidth as $thisCellId => $thisCellData) {
            // 2013-03-05 added ENT_QUOTES to also handle Apostrophes properly
            $this->pdf->MultiCell($thisCellData['width'], $maxHeight, $this->br2nl(html_entity_decode($rowData[$thisCellId], ENT_QUOTES)), $cellFormat['border'], $thisCellData['alignment'], $cellFormat['fill'], 0);
         }
      } else {
         // check page break
         $fm = $this->pdf->getMargins();
         if (($this->pdf->GetY() + $this->pdf->pixelsToUnits($cellFormat['font']['size'] + (isset($cellFormat['linespacing']) ? $cellFormat['linespacing'] : 4))) > ($this->pdf->getPageHeight() - $fm['bottom'] - $fm['footer'])) {
            $this->pdf->AddPage();

            if ($this->pdfParams->kpdfexport->pdf_headerperpage)
               $this->renderHeaderRow();
         }

         $this->setFormatFromArray($cellFormat);

         // output the cells
         foreach ($this->cellWidth as $thisCellId => $thisCellData) {
            // since we also store other information like offset check if this is a cell
            if (is_array($thisCellData) && !empty($thisCellData['width']))
            // 2013-03-05 added ENT_QUOTES to also handle Apostrophes properly
               $this->pdf->Cell($thisCellData['width'], $this->pdf->pixelsToUnits($cellFormat['font']['size'] + (isset($cellFormat['linespacing']) ? $cellFormat['linespacing'] : 4)), $this->br2nl(html_entity_decode($rowData[$thisCellId], ENT_QUOTES)), $cellFormat['border'], 0, $thisCellData['alignment'], $cellFormat['fill']);
         }
      }
      // add a line
      $this->pdf->Ln();
   }

   protected function br2nl($string) {
      return strip_tags(str_replace('<br>', chr(13) . chr(10), $string));
   }

   private function getDefaultPresentationExport($thisReport, $dynamicols = '') {

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

      //run through the results
      foreach ($reportResults as $resultRecord) {
         $recordArray = array();
         foreach ($fieldArray as $fieldId => $fieldData) {
            if ($fieldData['renderer'] != ''){
               //PHP7 - 5.6 COMPAT
               //ORIGINAL: $recordArray[$fieldId] = $thisReportRenderer->$fieldData['renderer']($fieldId, $resultRecord);
               $rendererFn = $fieldData['renderer'];
               $recordArray[$fieldId] = $thisReportRenderer->$rendererFn($fieldId, $resultRecord);
               //END
            }
            else
               $recordArray[$fieldId] = $resultRecord[$fieldId];
         }
         $exportData['datasets']['main'][] = $recordArray;
      }

      // those we leave empty
      $exportData['datasettitles'] = array();
      $exportData['datasetsummaries'] = array();

      // return the
      return $exportData;
   }

   private function renderVisualization($thisReport, $visData) {

      // get an instance of the Visualizationmanager
      require_once 'modules/KReports/KReportVisualizationManager.php';
      $thisVisManager = new KReportVisualizationManager();

      $margins = $this->pdf->getMargins();

      if ($visData != '') {
         // get the data from the Post
         $visArray = json_decode(html_entity_decode($visData), true);

         // call the vis manager to get eth oibjects converted to proper formats
         $visObjects = $thisVisManager->getVisualizationExport(html_entity_decode($thisReport->visualization_params), $visArray['objects']);

         // get the measures
         $drawAreaMeasures = array(
             'left' => $margins['left'],
             'top' => 20,
             'width' => $this->pdf->getPageWidth() - $margins['left'] - $margins['right'],
             'height' => ($this->pdf->getPageWidth() - $margins['left'] - $margins['right']) / $this->pdf->pixelsToUnits($visArray['measures']['width']) * $this->pdf->pixelsToUnits($visArray['measures']['height'])
         );

         foreach ($visObjects as $count => $thisVisObject) {
            if ($thisVisObject['data'] != '') {
               switch ($thisVisObject['type']) {
                  case 'SVG':
                     // BUG #522 2014-12-03
                     $svgData = urldecode($thisVisObject['data']);
                     if (strpos($svgData, '<div') !== false)
                        $svgData = substr($svgData, 0, strpos($svgData, '<div'));

                     $this->pdf->ImageSVG(
                             $file = '@' . $svgData, $x = $drawAreaMeasures['left'] + ($drawAreaMeasures['width'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['left']) / 100), $y = $drawAreaMeasures['top'] + ($drawAreaMeasures['height'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['top']) / 100), $w = $drawAreaMeasures['width'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['width']) / 100, $h = $drawAreaMeasures['height'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['height']) / 100, $link = '', $align = '', $palign = '', $border = 0, $fitonpage = false);
                     break;
                  case 'PNG':
                     $this->pdf->Image(
                             $file = '@' . $thisVisObject['data'], $x = $drawAreaMeasures['left'] + ($drawAreaMeasures['width'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['left']) / 100), $y = $drawAreaMeasures['top'] + ($drawAreaMeasures['height'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['top']) / 100), $w = $drawAreaMeasures['width'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['width']) / 100, $h = $drawAreaMeasures['height'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['height']) / 100, $type = 'PNG', $link = '', $align = '', $palign = '', $border = 0, $fitonpage = false);
                     break;
               }
            } else
               $this->pdf->Rect(
                       $x = $drawAreaMeasures['left'] + ($drawAreaMeasures['width'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['left']) / 100), $y = $drawAreaMeasures['top'] + ($drawAreaMeasures['height'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['top']) / 100), $w = $drawAreaMeasures['width'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['width']) / 100, $h = $drawAreaMeasures['height'] * preg_replace('/\%/', '', $thisVisObject['layoutdata']['height']) / 100, '', array(), array('GREY')
               );
         }

         //2013-03-05 check if we should add a page after the visualization has been rendered
         if ($this->pdfParams->kpdfexport->pdf_chartpage && !$this->pdfParams->kpdfexport->pdf_newpagepergroup)
            $this->pdf->AddPage();
         else {
            // set the new Y adding the draw height
            $this->pdf->SetY($this->pdf->GetY() + $drawAreaMeasures['height'] + 10);
            //$this->pdf->Ln();
         }
      }
   }

}
