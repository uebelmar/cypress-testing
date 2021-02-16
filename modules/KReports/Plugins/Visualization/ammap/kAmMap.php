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


use SpiceCRM\modules\KReports\KReportChartData;

require_once('modules/KReports/KReport.php');
require_once('modules/KReports/KReportChartData.php');
require_once('modules/KReports/Plugins/prototypes/kreportvisualizationplugin.php');
require_once('modules/KReports/Plugins/Visualization/ammap/kAmMapUtil.php');

class kAmMap extends kreportvisualizationplugin {

    //2013-03-05 ... required for the rrenderer of numbers
    var $report = null;

    function __construct() {
        
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
        
        $amData = $this->getChartData($thisReport, $thisParams, 0 , $addReportParams);
        $chartData = $this->wrapAmData($amData, $thisDivId, $thisParams);
        
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

        return $this->convertRawToAmData($rawData['chartData'], $rawData['dimensions'], $rawData['dataseries']);
    }

    /*
     * helper function to mingle the data and prepare for a am represenatation
     */

    public function convertRawToAmData($chartData, $dimensions, $dataseries) {
        $amData = array();
        $values = array();
        
        //populate balloon - label
        foreach($dataseries as $dataserie => $data){
            $description = $data['name'];
            $fieldid = $data['fieldid'];
        }

        //populate areas (countries)
        foreach ($chartData as $id => $data) {
            $amData['dataProvider'][] = array(
                'id' => $id, 
                'description' => "<b>".$id."</b> - ".$description." ".$data[$fieldid],
                'value' => $data[$fieldid]
            );
            $values[] = $data[$fieldid]; 
        }

        //calculate min/max value
        $amData['minValue'] = min($values);
        $amData['maxValue'] = max($values);

        return $amData;
    }

    /*
     * function to wrap the code with the am visualization API options etc.
     */

    public function wrapAmData($amData, $divId, $thisParams) {

        $amChart = array(
            'type' => 'map',
            'dataProvider' => array(
                'map' => 'worldLow',
            ),
            'areasSettings' => array(
                'autoZoom' => true,                
                'balloonText' => "[[description]]",
            ),
            'valueLegend' => array(
                'right' => 10,
                'minValue' => "the less",
                'maxValue' => "the most"
            ),
            'title' => $thisParams['title']
        );

        // handle the colors
        include('modules/KReports/config/KReportColors.php');
        if ($thisParams['colors'] != '' && isset($kreportColors[$thisParams['colors']])) {
            $amChart['areasSettings']['color'] = $kreportColors[$thisParams['colors']]['colors'][0];
            $amChart['imageSettings']['color'] = $kreportColors[$thisParams['colors']]['colors'][0];
        }
        
        //handle map type
        switch($thisParams['type']){
            case 'area':
                $amChart['dataProvider']['areas'] = $amData['dataProvider'];
                //handle min -max value
                $amChart['valueLegend']['minValue'] = $amData['minValue'];
                $amChart['valueLegend']['maxValue'] = $amData['maxValue'];
                break;
            case 'bubbles':
                unset($amChart['valueLegend']);
                //build images
                foreach($amData['dataProvider'] as $amDataIdx => $data){
                    //calculate width/height. Get the min , the max calculate a percentage for size
                    $size =  round($data['value']*100/$amData['maxValue']);

                    $latlong = AmMapUtil::getLatLong($data['id']);
                    $amChart['dataProvider']['images'][] = array(
                        'type' => 'circle',
                        'width' => $size,
                        'height' => $size,
                        'value' => $data['value'],
                        'latitude' => $latlong['latitude'],
                        'longitude' => $latlong['longitude'],
                        'color' => $amChart['imageSettings']['color']
                    );
                }                
                break;
        }
        
        
        // handle options
        if(!empty($thisParams['colorSteps']))
            $amChart['colorSteps'] = $thisParams['colorSteps'];
        
        
        // send back the Chart as Array
        return $amChart;
    }

    /*
     * am chart provides proper svg code .. so nothing to do but to base64 decode
     */

    function parseExportData($exportedData) {
        return array(
            'type' => 'SVG',
            'data' => urldecode(base64_decode($exportedData))
        );
    }

}