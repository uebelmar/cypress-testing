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


use SpiceCRM\modules\KReports\KReport;

use SpiceCRM\includes\SugarObjects\SpiceConfig;

require_once('modules/KReports/KReport.php');
require_once('modules/KReports/Plugins/Integration/kpdfexport/kpdfexport.php');

class pluginkpdfexportcontroller
{

    var $currencySymbols = null;

    public function action_export($requestParams)
    {
        

        ob_start();
        $exporter = new kpdfexport();

        // 2013-05-29 add config option for memory limit see if we should set the runtime and memory limit
        if (!empty(SpiceConfig::getInstance()->config['KReports']['pdfmemorylimit'])) ini_set('memory_limit', SpiceConfig::getInstance()->config['KReports']['pdfmemorylimit']);
        if (!empty(SpiceConfig::getInstance()->config['KReports']['pdfmaxruntime'])) ini_set('max_execution_time', SpiceConfig::getInstance()->config['KReports']['pdfmaxruntime']);

        $thisReport = new KReport();
        $thisReport->retrieve($requestParams['record']);

        // 2013-03-13 check for custom filtering
        $dynamicolsOverride = array();
        if (isset($requestParams['dynamicoptions'])) {
            $_REQUEST['whereConditions'] = $requestParams['dynamicoptions'];
            $thisReport->whereOverride = json_decode(html_entity_decode($requestParams['dynamicoptions']), true);
        }

        if (isset($requestParams['dynamicols']) && $requestParams['dynamicols'] != '')
            $dynamicolsOverride = html_entity_decode($requestParams['dynamicols'], ENT_QUOTES, 'UTF-8');

        ob_get_clean();
        ob_start();
        $exporter->exportToPDF($thisReport, $dynamicolsOverride, $requestParams['visData'], 'D');
        $output = ob_get_clean();

        if ($requestParams['rawResult'])
            return $output;
        else
            echo $output;
    }

    public function action_getPDFLayouts()
    {
        require('modules/KReports/Plugins/Integration/kpdfexport/config/KReportPDF.php');
        $layoutArray = array();
        // print_r($kreportPDFconfig);
        foreach ($kreportPDFconfig as $thisLayout => $thisLayoutData) {
            $layoutArray[] = array(
                'fieldvalueid' => $thisLayout,
                'fieldname' => (isset($thisLayoutData['displayName']) && $thisLayoutData['displayName'] != '' ? $thisLayoutData['displayName'] : $thisLayout)
            );
        }
        return $layoutArray;
    }

}