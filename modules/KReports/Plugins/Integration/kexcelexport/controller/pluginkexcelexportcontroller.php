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

require_once('modules/KReports/Plugins/Integration/kexcelexport/kexcelexport.php');

class pluginkexcelexportcontroller {

   var $currencySymbols = null;

   public function action_export($requestParams) {
      

      // 2013-05-29 add config option for memory limit see if we should set the runtime and memory limit
      if(!empty(SpiceConfig::getInstance()->config['KReports']['excelmemorylimit'])) ini_set('memory_limit', SpiceConfig::getInstance()->config['KReports']['excelmemorylimit']);
      if(!empty(SpiceConfig::getInstance()->config['KReports']['excelmaxruntime'])) ini_set('max_execution_time', SpiceConfig::getInstance()->config['KReports']['excelmaxruntime']);
      
      $exporter = new kexcelexport();

      // see if we have override layout from the grid
      $dynamicolsOverride = '';
      if (isset($requestParams['dynamicols']) && $requestParams['dynamicols'] != '')
         $dynamicolsOverride = html_entity_decode($requestParams['dynamicols'], ENT_QUOTES, 'UTF-8');

       //prepare file name
       $thisReport = BeanFactory::getBean("KReports", $requestParams['record']);
       $thisReport->name = str_replace("&#039;", "", $thisReport->name);
       $filename = preg_replace("/[^a-zA-Z0-9\-\_]/", "", $thisReport->name);
       $filename.= '_' . date('Y-m-d_H-i-s');
       unset($thisReport);
       //2013-04-16 support xls export Bug #467
       if (SpiceConfig::getInstance()->config['KReports']['excelversion'] == '2003')
           $filename.= ".xls";
       else
           $filename.= ".xlsx";

       //make sure all previously started output buffers are
       //under PHP7 we have to delete each started one
       $buffers = ob_get_level();
       if($buffers > 0){
           for($i=0; $i < $buffers; $i++)
               ob_end_clean();
       }

      header('Content-type: application/ms-excel');
      header('Content-Disposition: attachment; filename=' . $filename);
      header('Content-Transfer-Encoding: binary');

       //get parent bean
       if (isset($requestParams['parentbeanId']) && isset($requestParams['parentbeanModule'])) {
           $parentbean = BeanFactory::getBean($requestParams['parentbeanModule'], $requestParams['parentbeanId']);
       }

      //2013-04-16 support xls export Bug #467
      ob_start();
      if (SpiceConfig::getInstance()->config['KReports']['excelversion'] == '2003')
         echo $exporter->exportToExcel($requestParams['record'], $dynamicolsOverride, 'xls', $parentbean);
      else
         echo $exporter->exportToExcel($requestParams['record'], $dynamicolsOverride, 'xlsx', $parentbean);

      $output = ob_get_clean();

      if($requestParams['rawResult'])
         return $output;
      else
         echo $output; 
   }

}
