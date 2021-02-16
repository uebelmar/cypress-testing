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


use SpiceCRM\includes\SugarObjects\SpiceConfig;

require_once('modules/KReports/Plugins/prototypes/kreportintegrationplugin.php');

class kpdrilldown extends kreportintegrationplugin {

   var $thisReport;

   public function __construct() {
      $this->pluginName = 'Presentation Drilldown';
   }

   public function checkAccess($thisReport) {
      $this->thisReport = $thisReport;
      return true;
   }

   public function getMenuItem() {
      $drillDownArray = array(); 
      $integrationParams = json_decode(html_entity_decode($this->thisReport->integration_params));
      foreach($integrationParams->kpdrilldown as $thisDrilldown){
         $drillDownArray[] = array(
             'id' => $thisDrilldown->linkid, 
             'reportid' => $thisDrilldown->reportid, 
             'text' => (!empty($thisDrilldown->displayname) ? $thisDrilldown->displayname : $thisDrilldown->reportname), 
             'linktype' => $thisDrilldown->linktype,
             'icon' => 'modules/KReports/images/report'.strtolower($thisDrilldown->linktype).'.png'
         );
      }
      return array(
          'jsCode' => "K.kreports.pdrilldownitems = " . json_encode($drillDownArray), 
          'jsFile' => array(
              'custom/modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldownview' . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . '.js',
              'custom/modules/KReports/Plugins/Integration/kpdrilldown/base64.js'
              )
      );
   }
}

