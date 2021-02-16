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

use SpiceCRM\modules\KReports\KReportChartData;

require_once('modules/KReports/KReport.php');
require_once('modules/KReports/KReportChartData.php');
require_once('modules/KReports/Plugins/prototypes/kreportvisualizationplugin.php');

class kGoogleGeo extends kreportvisualizationplugin {

    //2013-03-05 ... required for the rrenderer of numbers
    var $report = null;

    function __construct() {
        
    }

    public function getHeader() {

        $coreString = "<script type='text/javascript' src='https://www.google.com/jsapi?autoload={\"modules\":[{\"name\":\"visualization\",\"version\":\"1\"}]}'></script>";
        $coreString .= "<script type='text/javascript' src='custom/modules/KReports/Plugins/Visualization/googlegeo/googlegeotools" . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . ".js'></script>";
        
        return $coreString;
    }

    /*
     * get only the data component if the selction has changed
     */

    public function getItemUpdate($thisReport, $thisParams, $snaphotid = 0, $addReportParams = array()) {
        // 2013-05-16 set the report on the object
        $this->report = $thisReport;

        return json_encode($this->getChartData($thisReport, $thisParams, $snaphotid, $addReportParams));
    }

    /*
     * get the Chart Object to render into the visualization
     */

    public function getItem($thisDivId, $thisReport, $thisParams, $addReportParams = array(), $snapshotid = 0) {
        $this->report = $thisReport;
        
        $googleData = $this->getChartData($thisReport, $thisParams, 0 , $addReportParams);
        $chartData = $this->wrapGoogleData($googleData, $thisDivId, $thisParams);
        
        return $chartData;
    }

    public function getChartData($thisReport, $thisParams, $snaphotid = 0, $addReportParams = array()) {
        $chartDataObj = new KReportChartData();
        $fields = json_decode(html_entity_decode($thisReport->listfields, ENT_QUOTES, 'UTF-8'), true);

        // check for all the fieldids we have
        $fieldMap = array();
        foreach ($fields as $thisFieldIndex => $thisFieldData) {
            $fieldMap[$thisFieldData['fieldid']] = $thisFieldIndex;
        }

        //$dimensions = array(array('fieldid' => $fields[0]['fieldid']));
        $dimensions = array();
        foreach ($thisParams['dimensions'] as $thisDimension => $thisDimensionData) {
            if ($thisDimensionData != null)
                $dimensions[] = array('fieldid' => $thisDimensionData);
        }

        //$dataseries = array($fields[1]['fieldid'], $fields[2]['fieldid']);
        $dataseries = array();
        foreach ($thisParams['dataseries'] as $thisDataSeries => $thisDataSeriesData) {
            $dataseries[$thisDataSeriesData['fieldid']] = array(
                'fieldid' => $thisDataSeriesData['fieldid'],
                'name' => $fields[$fieldMap[$thisDataSeriesData['fieldid']]]['name']
            );
        }

        // set Chart Params
        $chartParams = array();
        $chartParams['showEmptyValues'] = ($thisParams['options']['emptyvalues'] == 'on' ? true : false);
        if ($thisParams['context'] != '')
            $chartParams['context'] = $thisParams['context'];

        $rawData = $chartDataObj->getChartData($thisReport, $snaphotid, $chartParams, $dimensions, $dataseries, $addReportParams);

        return $this->convertRawToGoogleData($rawData['chartData'], $rawData['dimensions'], $rawData['dataseries']);
    }

    /*
     * helper function to mingle the data and prepare for a google represenatation
     */

    public function convertRawToGoogleData($chartData, $dimensions, $dataseries) {
    $googleData = array();


        foreach ($dataseries as $thisDataseries) {
            $dataseriesEntry = $thisDataseries;
        }
        
        $googleData[] = array($dimensions[0]['fieldid'], $dataseriesEntry['name']);

        /*
        foreach ($dimensions as $thisDimension) {
            $googleData['cols'][] = array('id' => $thisDimension['fieldid'], 'type' => 'string', 'label' => $thisDimension['fieldid']);
        }

        foreach ($dataseries as $thisDataseries) {
            $googleData['cols'][] = array('id' => $thisDataseries['fieldid'], 'type' => 'number', 'label' => ($thisDataseries['name'] != '' ? $thisDataseries['name'] : $thisDataseries['fieldid']));
        }
*/
        foreach ($chartData as $thisDimensionId => $thisData) {
            $googleData[] = array($thisDimensionId, $thisData[$dataseriesEntry['fieldid']]);
            /*
            $rowArray = array();
            $rowArray[] = array('v' => $dimensions[0]['values'][$thisDimensionId]);
            foreach ($dataseries as $thisDataseries) {
                $rowArray[] = array('v' => $thisData[$thisDataseries['fieldid']]);
            }
            $googleData['rows'][] = array('c' => $rowArray);
             */
        }
        

        return $googleData;
    }

    /*
     * function to wrap the code with the google visualization API options etc.
     */

    public function wrapGoogleData($googleData, $divId, $thisParams) {
        // else continue processing ..
        $googleChart = array(
            'chartType' => 'GeoChart',
            'containerId' => $divId,
            'options' => array(
                'displayMode'=> 'regions'
            ),
            'dataTable' => $googleData
        );
        
        if(!empty($thisParams['region']))
            $googleChart['options']['region'] = $thisParams['region'];
        
        if(!empty($thisParams['resolution']))
            $googleChart['options']['resolution'] = $thisParams['resolution'];
        
        // handle the colors

        include('modules/KReports/config/KReportColors.php');
        if ($thisParams['colors'] != '' && isset($kreportColors[$thisParams['colors']])) {
            $googleChart['options']['colorAxis']['colors'] = $kreportColors[$thisParams['colors']]['colors'];
        }

        // send back the Chart as Array
        return $googleChart;
    }

    /*
     * google chart provides proper svg code .. so nothing to do but to base64 decode
     */

    function parseExportData($exportedData) {
        return array(
            'type' => 'SVG',
            'data' => urldecode(base64_decode($exportedData))
        );
    }

}