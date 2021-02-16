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

use SpiceCRM\modules\KReports\KReport;
use SpiceCRM\modules\KReports\KReportRenderer;

require_once('modules/KReports/KReport.php');

class khtmlreport {

   var $refObject = null;
   // in the iteration on building the header this is set so we know that there is a link and we should parse links
   // safes some processing time if we do not have to do it
   var $haslinks = false;

   public function __construct($refObject = null) {
      $this->refObject = $refObject;
   }

   public function renderReport($reportId, $start = 0, $entries = 10, $renderTarget = 'Dashlet', $addParams = array()) {
      $db = DBManagerFactory::getInstance();

      $reportHTML = '';

      if (isset($_REQUEST['report_start']))
         $start = $_REQUEST['report_start'];

      $thisReport = new KReport();
      $thisReport->retrieve($reportId);

      // set the report params
      $reportParams = array();
      $reportParams['start'] = $start;
      $reportParams['limit'] = $entries;
      $reportParams['noFormat'] = true;
      
      if (!empty($addParams['parentbean']))
         $reportParams['parentbean'] = $addParams['parentbean'];

      if (isset($_REQUEST['sort']) && isset($_REQUEST['dir'])) {
         $reportParams['sortseq'] = $_REQUEST['dir'];
         $reportParams['sortid'] = $_REQUEST['sort'];
      }

      $reportData = $thisReport->getSelectionResults($reportParams);

      $ss = new Sugar_Smarty();
      $ss->assign('navStrings', array('of' => 'of', 'start' => 'start', 'next' => 'next', 'previous' => 'previous', 'end' => 'end'));
      $ss->assign('prerow', false);

      // set the param for the target
      $ss->assign('renderTarget', $renderTarget);

      //get the total count
      $totalCount = 0;
      if ($thisReport->kQueryArray->countSelectString != '') {
         $queryResults = $db->fetchByAssoc($db->query($thisReport->kQueryArray->countSelectString));
         $totalCount = $queryResults ['totalCount'];
      } else {
         // bug #523 fix for reevaluating sql query and thus resetting the links
         $query = $thisReport->kQueryArray->selectString . ' ' . $thisReport->kQueryArray->fromString . ' ' . $thisReport->kQueryArray->whereString . ' ' . $thisReport->kQueryArray->groupbyString . ' ' . $thisReport->kQueryArray->havingString . ' ' . $thisReport->kQueryArray->orderbyString;
         $totalCount = $db->getRowCount($queryResults = $db->query($query));
      }

      // build the page data array the tpl expects
      $ss->assign('dashletId', $this->refObject->id);
      $pageData = array(
          'offsets' => array(
              'current' => $start,
              'lastOffsetOnPage' => ($start + $entries > $totalCount ? $totalCount : $start + $entries),
              'totalCounted' => true,
              'total' => $totalCount
          ),
          'urls' => array(
          //  'nextPage' => 'index.php?module=Home&action=DynamicAction&DynamicAction=displayDashlet&report_start=10&sugar_body_only=1&id=' . $this->refObject->id
          )
      );

      // build the urls for the Dashlet
      if ($renderTarget == 'Dashlet') {
         $addSortString = '';
         if (isset($_REQUEST['sort']) && isset($_REQUEST['dir']))
            $addSortString = '&sort=' . $_REQUEST['sort'] . '&dir=' . $_REQUEST['dir'];

         if ($start + $entries < $totalCount) {
            $pageData['urls']['nextPage'] = 'index.php?module=Home&action=DynamicAction&DynamicAction=displayDashlet&report_start=' . ($start + $entries) . '&sugar_body_only=1&id=' . $this->refObject->id . $addSortString;
            $pageData['urls']['endPage'] = 'index.php?module=Home&action=DynamicAction&DynamicAction=displayDashlet&report_start=' . (floor($totalCount / $entries) * $entries == $totalCount ? $totalCount - $entries : floor($totalCount / $entries) * $entries) . '&sugar_body_only=1&id=' . $this->refObject->id . $addSortString;
         }
         if ($start > 0) {
            $pageData['urls']['startPage'] = 'index.php?module=Home&action=DynamicAction&DynamicAction=displayDashlet&report_start=0&sugar_body_only=1&id=' . $this->refObject->id . $addSortString;
            $pageData['urls']['prevPage'] = 'index.php?module=Home&action=DynamicAction&DynamicAction=displayDashlet&report_start=' . ($start - $entries) . '&sugar_body_only=1&id=' . $this->refObject->id . $addSortString;
         }

         $pageData['urls']['orderBy'] = 'index.php?module=Home&action=DynamicAction&DynamicAction=displayDashlet&report_start=0&sugar_body_only=1&id=' . $this->refObject->id;
      }
      // build the urls for the Subpanel
      if ($renderTarget == 'SubPanel') {
         $pageData['subpanelname'] = $addParams['subpanelname'];
         $pageData['module'] = $addParams['module'];
         if ($start > 0) {
            $pageData['urls']['startPage'] = 'index.php?module=' . $addParams['module'] . '&action=DetailView&record=' . $addParams['recordid'] . '&' . $addParams['subpanelname'] . '_report_start=0&to_pdf=true&action=SubPanelViewer&subpanel=' . $addParams['subpanelname'] . '&layout_def_key=' . $addParams['module'];
            $pageData['urls']['prevPage'] = 'index.php?module=' . $addParams['module'] . '&action=DetailView&record=' . $addParams['recordid'] . '&' . $addParams['subpanelname'] . '_report_start=' . ($start - $entries) . '&to_pdf=true&action=SubPanelViewer&subpanel=' . $addParams['subpanelname'] . '&layout_def_key=' . $addParams['module'];
         }
         if ($start + $entries < $totalCount) {
            $pageData['urls']['nextPage'] = 'index.php?module=' . $addParams['module'] . '&action=DetailView&record=' . $addParams['recordid'] . '&' . $addParams['subpanelname'] . '_report_start=' . ($start + $entries) . '&to_pdf=true&action=SubPanelViewer&subpanel=' . $addParams['subpanelname'] . '&layout_def_key=' . $addParams['module'];
            $pageData['urls']['endPage'] = 'index.php?module=' . $addParams['module'] . '&action=DetailView&record=' . $addParams['recordid'] . '&' . $addParams['subpanelname'] . '_report_start=' . (floor($totalCount / $entries) * $entries == $totalCount ? $totalCount - $entries : floor($totalCount / $entries) * $entries) . '&to_pdf=true&action=SubPanelViewer&subpanel=' . $addParams['subpanelname'] . '&layout_def_key=' . $addParams['module'];
         }
      }

      // assign the page date array
      $ss->assign('pageData', $pageData);

      // get the header columns
      $columns = $this->parseReportListFields(json_decode(html_entity_decode($thisReport->listfields), true), $thisReport);
      $ss->assign('colCount', count($columns));
      $ss->assign('displayColumns', $columns);

      //2012-12-10 change in renderer
      $kreportRenderer = new KReportRenderer($thisReport);

      $reportRecords = array();
      foreach ($reportData as $thisReportRecord) {

         // 2013-02-23 changed so we process renderer and links on the full record to also have ther link info
         foreach ($thisReportRecord as $fieldId => $fieldDetails) {
            if ($columns[$fieldId]['renderer'] != '' && method_exists($kreportRenderer, $columns[$fieldId]['renderer']))
               $thisReportRecord[$fieldId] = $kreportRenderer->{$columns[$fieldId]['renderer']}($fieldId, $thisReportRecord);
            else
               $thisReportRecord[$fieldId] = $thisReportRecord[$fieldId];
         }

         // build links
         $thisReportRecord = $thisReport->buildLinks($thisReportRecord);

         // assign the built record to the array for display in the Dashlet
         $reportRecords[] = $thisReportRecord;
      }

      // assign data .. could be cleand if we want0
      $ss->assign('data', $reportRecords);

      // rendering Parametr
      $ss->assign('rowColor', Array('oddListRow', 'evenListRow'));

      // parse the TPL
      $reportHTML = $ss->fetch('modules/KReports/Plugins/Integration/kpublishing/tpls/khtmlpublish.tpl');

      return $reportHTML;
   }

   private function parseReportListFields($listFields, $thisReport) {
      $columnArray = array();
      // loop thorugh all fields
      $totalwidth = 0;
      foreach ($listFields as $thisIndex => $thisListFieldData) {
         //2012-12-10 check for fields that should no display
         if ($thisListFieldData['display'] == 'yes') {
            $columnArray[$thisListFieldData['fieldid']] = array(
                'label' => $thisListFieldData['name'],
                'sortable' => false,
                'align' => $thisReport->getXtypeAlignment($thisReport->getFieldTypeById($thisListFieldData['fieldid']), $thisListFieldData['fieldid']),
                'renderer' => $thisReport->getXtypeRenderer($thisReport->getFieldTypeById($thisListFieldData['fieldid']), $thisListFieldData['fieldid'])
            );
            $totalwidth = $totalwidth + $thisListFieldData['width'];

            // set Sortable
            if ($thisListFieldData['sort'] != '-') {

               $columnArray[$thisListFieldData['fieldid']]['sortable'] = true;

               if (isset($_REQUEST['sort']) && isset($_REQUEST['dir'])) {
                  if ($thisListFieldData['fieldid'] == $_REQUEST['sort'])
                     $columnArray[$thisListFieldData['fieldid']]['sortOrder'] = strtoupper($_REQUEST['dir']);
               }
               else {
                  // if we have a stor already .. do it 
                  if ($thisListFieldData['sort'] != 'sortable')
                     $columnArray[$thisListFieldData['fieldid']]['sortOrder'] = strtoupper($thisListFieldData['sort']);
               }
            }

            if ($thisListFieldData['link'] != 'no')
               $this->haslinks = true;
         }
      }

      // second run to set the width
      foreach ($listFields as $thisIndex => $thisListFieldData) {
         if (isset($columnArray[$thisListFieldData['fieldid']]))
            $columnArray[$thisListFieldData['fieldid']]['width'] = $thisListFieldData['width'] / $totalwidth * 100;
      }

      // returnn the array
      return $columnArray;
   }

}
