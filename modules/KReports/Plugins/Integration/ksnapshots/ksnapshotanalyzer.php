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

require_once('modules/KReports/Plugins/prototypes/kreportintegrationplugin.php');

class ksnapshotanalyzer extends kreportintegrationplugin {

   public function __construct() {
      $this->pluginName = 'Snapshot Analyzer';
   }

   public function analyzeSnapshots($reportid, $chartType, $valueField, $addAxisField = '') {
      $db = DBManagerFactory::getInstance();

      $thisReport = BeanFactory::getBean('KReports', $reportid);

      $analysisResults = array();

      $snapshotsList = $db->query("SELECT * FROM kreportsnapshots WHERE report_id ='" . $reportid . "' order by snapshotdate asc");
      while ($thisSnapshot = $db->fetchByAssoc($snapshotsList)) {
         $snapshotValues = $thisReport->getSelectionResults(array('noFormat' => true), $thisSnapshot['id']);
         $analysisResults[$thisSnapshot['snapshotdate']] = $this->generateResultArray($snapshotValues, $valueField, $addAxisField);
      }

      // get Actuals
      $actualValues = $thisReport->getSelectionResults(array('noFormat' => true));
      $analysisResults['current'] = $this->generateResultArray($actualValues, $valueField, $addAxisField);

      return $this->createChartData($analysisResults, $chartType);
   }

   private function generateResultArray($resultArray, $valueField, $addAxisField = '') {
      if (empty($addAxisField))
         $retValue = 0;
      else
         $retValue = array();

      foreach ($resultArray as $thisRecord) {
         if (empty($addAxisField))
            $retValue += $thisRecord[$valueField];
         else {
            if (empty($retValue[$thisRecord[$addAxisField]]))
               $retValue[$thisRecord[$addAxisField]] = 0;
            $retValue[$thisRecord[$addAxisField]] += $thisRecord[$valueField];
         }
      }
      return $retValue;
   }

   private function createChartData($reportData, $chartType) {
      
      $chartTypeArray = explode('_', $chartType);
      if(is_array($chartTypeArray))
         $thisChartType = $chartTypeArray[0];
      else
         $thisChartType = $chartType;
      
      $chartData = array(
          'chart' => array(
              'type' => $thisChartType,
              'renderTo' => 'thisAnaylzerChartId'
          ),
          'credits' => array(
              'enabled' => false
          ),
          'title' => array(),
          'xAxis' => array(
              'type' => 'datetime',
              'dateTimeLabelFormats' => array(
                  'month' => '%e. %b',
                  'year' => '%b'
              )
          ),
          'yAxis' => array(
              'min' => 0,
              'title' => array()
          )
      );
      
      // check if we need to stack 
      if(is_array($chartTypeArray) && count($chartTypeArray) > 1){
         $chartData['plotOptions'][$thisChartType] = array(
             'stacking' => $chartTypeArray[1]
         );
      }

      $series = array();
      foreach ($reportData as $thisSnapshotId => $thisSnaphotData) {
         $timestamp = time() * 1000;
         if ($thisSnapshotId != 'current')
            $timestamp = strtotime($thisSnapshotId) * 1000;

         if (is_array($thisSnaphotData)) {
            foreach ($thisSnaphotData as $seriesName => $serisValue)
               $series[$seriesName][] = array(
                   $timestamp,
                   $serisValue
               );
         } else {
            $series[0][] = array(
                $timestamp,
                $thisSnaphotData
            );
         }
      }

      foreach ($series as $thisSeriesName => $thisSeriesData) {
         $chartData['series'][] = array(
             'name' => $thisSeriesName,
             'data' => $thisSeriesData
         );
      }

      return $chartData;
   }

}
