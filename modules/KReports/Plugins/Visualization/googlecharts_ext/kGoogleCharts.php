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
use SpiceCRM\modules\KReports\KReportRenderer;

require_once('modules/KReports/KReport.php');
require_once('modules/KReports/KReportChartData.php');
require_once('modules/KReports/Plugins/prototypes/kreportvisualizationplugin.php');

class kGoogleChart extends kreportvisualizationplugin {

    //2013-03-05 ... required for the rrenderer of numbers
    var $report = null;

    public function __construct() {
        
    }

    /**
     * (non-PHPdoc)
     * @deprecated
     * @see kreportvisualizationplugin::getHeader()
     */
    public function getHeader() {

        $coreString = "<script type='text/javascript' src='https://www.google.com/jsapi?autoload={\"modules\":[{\"name\":\"visualization\",\"version\":\"1\"}]}'></script>";
        $coreString .= "<script type='text/javascript' src='custom/modules/KReports/Plugins/Visualization/googlecharts/googlechartstools" . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . ".js'></script>";

        return $coreString;
    }

    /**
     * @deprecated
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
        $googleData = $this->getChartData($thisReport, $thisParams, $snapshotid, $addReportParams);
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
                'name' => $fields[$fieldMap[$thisDataSeriesData['fieldid']]]['name'],
                // 2013-03-19 handle Chart Function properly Bug #448
                // also added axis and renderer
                'axis' => $thisDataSeriesData['axis'],
                'chartfunction' => $thisDataSeriesData['chartfunction'],
                'renderer' => $thisDataSeriesData['renderer']
            );
        }

        // set Chart Params
        $chartParams = array();
        $chartParams['type'] = $thisParams['type']; //needed in KReportChartData for unset dimension1
        $chartParams['showEmptyValues'] = ($thisParams['options']['emptyvalues'] == 'on' ? true : false);
        if ($thisParams['context'] != '')
            $chartParams['context'] = $thisParams['context'];

        //get data
        $rawData = $chartDataObj->getChartData($thisReport, $snaphotid, $chartParams, $dimensions, $dataseries, $addReportParams);

        //convert for display
        $convertFunction = $this->getConvertFunctionName($thisParams['type']);
        return $this->$convertFunction($rawData['chartData'], $rawData['dimensions'], $rawData['dataseries']);
    }
    
    /**
     * retrieve name of function transforming data for chartWrapper
     * @param String $charttype
     * @return String
     */
    public function getConvertFunctionName($charttype){
        $fn = "convertRawToGoogleData";
        switch($charttype){
            case 'Scatter':
            case 'Bubble':
            case 'Sankey':
                $fn.= $charttype;
                break;
            default:
        }
        return $fn;
    }

    /*
     * helper function to mingle the data and prepare for a google represenatation
     */

    public function convertRawToGoogleData($chartData, $dimensions, $dataseries) {
        
        $googleData = array();
        $googleData['cols'] = array();
        $googleData['rows'] = array();

        foreach ($dimensions as $thisDimension) {
            $googleData['cols'][] = array('id' => $thisDimension['fieldid'], 'type' => 'string', 'label' => $thisDimension['fieldid']);
        }

        foreach ($dataseries as $thisDataseries) {
            $googleData['cols'][] = array('id' => $thisDataseries['fieldid'], 'type' => 'number', 'label' => ($thisDataseries['name'] != '' ? $thisDataseries['name'] : $thisDataseries['fieldid']));

            // add a row for the annotation
            $googleData['cols'][] = array('id' => $thisDataseries['fieldid'], 'type' => 'number', 'label' => ($thisDataseries['name'] != '' ? $thisDataseries['name'] : $thisDataseries['fieldid']), 'role' => 'annotation');

            // 2013-03-05 check if we have a renderer
            $dataseries[$thisDataseries['fieldid']]['renderer'] = $this->report->getXtypeRenderer($this->report->fieldNameMap[$thisDataseries['fieldid']]['type'], $thisDataseries['fieldid']);
        }

        //2013-03-05 instantiate a renderer
        $kreportRenderer = new KReportRenderer();

        
        foreach ($chartData as $thisDimensionId => $thisData) {
            $rowArray = array();
            
            $rowArray[] = array('v' => $dimensions[0]['values'][$thisDimensionId]);
            
            foreach ($dataseries as $thisDataseries) {
                //2013-03-05 check if we should render
                if (!empty($thisDataseries['renderer'])) {
                    $rowArray[] = array('x' => 'guidValue', 'v' => $thisData[$thisDataseries['fieldid']], 'f' => $kreportRenderer->{$thisDataseries['renderer']}($thisDataseries['fieldid'], $thisData));

                    // add a row for the annotaiton
                    $rowArray[] = array('x' => 'guidValue', 'v' => $thisData[$thisDataseries['fieldid']], 'f' => $kreportRenderer->{$thisDataseries['renderer']}($thisDataseries['fieldid'], $thisData));
                } else {
                    $rowArray[] = array('v' => $thisData[$thisDataseries['fieldid']]);

                    // add a row for the annotaiton
                    $rowArray[] = array('v' => $thisData[$thisDataseries['fieldid']]);
                }
            }
            $googleData['rows'][] = array('c' => $rowArray);
        }
        
        return $googleData;
    }
    
    
    /*
     * helper function to mingle the data and prepare for a google Scatter Chart represenatation
     * chartData is an Array looking like
     * Array (
    [9000] => Array
        (
            [7000] => 1
            [8000] => 0
            [8800.5] => 0

        )

    [10000] => Array
        (
            [8000] => 10
            [7000] => 0
            [8800.5] => 0

        )
      )
     *
     * We build pairs of first key + second where count is higher than 0
     */

    public function convertRawToGoogleDataScatter($chartData, $dimensions, $dataseries) {
        //handle labels
        $fields = json_decode(html_entity_decode($this->report->listfields, ENT_QUOTES, 'UTF-8'), true);
        
        // check for all the fieldids we have
        $fieldMap = array();
        foreach ($fields as $thisFieldIndex => $thisFieldData) {
            $fieldMap[$thisFieldData['fieldid']] = $thisFieldIndex;
        }
               
        //manipulate data
        $googleData = array();
        $googleData['cols'] = array();
        $googleData['rows'] = array();

        foreach ($dimensions as $thisDimension) {
            $googleData['cols'][] = array('id' => $thisDimension['fieldid'], 'type' => 'number', 'label' => $fields[$fieldMap[$thisDimension['fieldid']]]['name']);
        }
                
        foreach ($chartData as $dim1Value => $dim2) {
            $rowArray = array();
            $rowArray[] = array('v' => $dim1Value);
            foreach($dim2 as $dim2Value => $counter){
                if($counter > 0){
                    $rowArray[] = array('v' => $dim2Value);
                }
            }
            
            $googleData['rows'][] = array('c' => $rowArray);
        }
        return $googleData;
    }

    
    public function convertRawToGoogleDataBubble($chartData, $dimensions, $dataseries) {
//         \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('------- chartdata ---------');
//         \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($chartData);

        //handle labels
        $fields = json_decode(html_entity_decode($this->report->listfields, ENT_QUOTES, 'UTF-8'), true);
        
        // check for all the fieldids we have
        $fieldMap = array();
        foreach ($fields as $thisFieldIndex => $thisFieldData) {
            $fieldMap[$thisFieldData['fieldid']] = $thisFieldIndex;
        }
        
        //get fieldid for size Value from dataseries. Only 1 serie possible in this chart type
        foreach($dataseries as $value => $data){
            $dataserieFieldId = $data['dimfieldid'];
            break;
        }
        
        //manipulate data for google Wrapper dataTable
        $googleData = array();
        $googleData['cols'] = array();
        $googleData['rows'] = array();

        //handle 3rd dimension when set
        $dimCount = count($dimensions);
        
        //cols definitions
        $googleData['cols'][] = array('id' => 'id', 'type' => 'string', 'label' => '');
        $dimCounter = 1;
        foreach ($dimensions as $thisDimension) {
            $googleData['cols'][] = array('id' => $thisDimension['fieldid'], 'type' => ($dimCounter == 3 ? 'string' : 'number'), 'label' => $fields[$fieldMap[$thisDimension['fieldid']]]['name']);
            $dimCounter++;
        }
        if($dimCount < 3)
            $googleData['cols'][] = array('id' => 'pack', 'type' => 'string', 'label' => '');
        $googleData['cols'][] = array('id' => 'size', 'type' => 'number', 'label' => $fields[$fieldMap[$dataserieFieldId]]['name']);
        
        //rows values
        switch($dimCount){
            case 2:
                foreach ($chartData as $dim1Value => $dim2) {
                    foreach($dim2 as $dim2Value => $serieValue){
                        $rowArray = array();
                        $rowArray[] = array('v' => ''); //id
                        $rowArray[] = array('v' => $dim1Value); //x-axis
                        $rowArray[] = array('v' => $dim2Value); //y-axis
                        $rowArray[] = array('v' => $fields[$fieldMap[$dataserieFieldId]]['name']); //pack
                        $rowArray[] = array('v' => $serieValue); //size
                        $googleData['rows'][] = array('c' => $rowArray);
                    }
                }
                break;
                
             case 3:
                foreach ($chartData as $dim1Value => $dim2) {
                    foreach($dim2 as $dim2Value => $dim3){
                        foreach($dim3 as $dim3Value => $serieValue){
                        $rowArray = array();
                        $rowArray[] = array('v' => ''); //id
                        $rowArray[] = array('v' => $dim1Value); //x-axis
                        $rowArray[] = array('v' => $dim2Value); //y-axis
                        $rowArray[] = array('v' => $dim3Value); //pack
                        $rowArray[] = array('v' => $serieValue); //size
                        $googleData['rows'][] = array('c' => $rowArray);
                    }
                }
                break;
                }
        }
        
        return $googleData;
    }
    
    
    
    /**
     * helper function to mingle the data and prepare for a google Sankey Chart represenatation
     *
     */

    public function convertRawToGoogleDataSankey($chartData, $dimensions, $dataseries) {
        //handle labels
        $fields = json_decode(html_entity_decode($this->report->listfields, ENT_QUOTES, 'UTF-8'), true);
        
        // check for all the fieldids we have
        $fieldMap = array();
        foreach ($fields as $thisFieldIndex => $thisFieldData) {
            $fieldMap[$thisFieldData['fieldid']] = $thisFieldIndex;
        }
               
        //manipulate data
        $googleData = array();
        $googleData['cols'] = array();
        $googleData['rows'] = array();

        //cols
        foreach ($dimensions as $thisDimension) {
            $googleData['cols'][] = array('id' => $thisDimension['fieldid'], 'type' => 'string', 'label' => $fields[$fieldMap[$thisDimension['fieldid']]]['name']);
        }
        //get label name for 3rd col
        foreach($dataseries as $dim1Value => $data){
            $fieldid = $data['dimfieldid'];
            break;
        }
        $googleData['cols'][] = array('id' => 'number', 'type' => 'number', 'label' => $fields[$fieldMap[$fieldid]]['name']);
            
        
        //grab fieldId name for 2nd Dim
        

        //rows
        foreach ($chartData as $dim1Value => $dim2) {
            foreach($dim2 as $dim2Value => $number){
                if($number > 0){
                    $rowArray = array();
                    $rowArray[] = array('v' => $dim1Value);
                    $rowArray[] = array('v' => $dim2Value);
                    $rowArray[] = array('v' => $number);
                    $googleData['rows'][] = array('c' => $rowArray);
                }
            }
            
        }
        return $googleData;
    }
    
    
    
    
    
    
    
    /*
     * function to wrap the code with the google visualization API options etc.
     */

    public function wrapGoogleData($googleData, $divId, $thisParams) {
        //Build google visualization class name
        switch($thisParams['type']){
            case 'Gauge':
                $gvizclass = 'Gauge';
                break;
            case 'Sankey':
                $gvizclass = 'Sankey';
                break;
            default:
                $gvizclass = $thisParams['type'] . 'Chart';
        }
        
        
        // else continue processing ..
        $googleChart = array(
            'chartType' => $gvizclass,
            'containerId' => $divId,
            'options' => array(
                'legend' => 'none',
                'fontSize' => 11
            ),
            'dataTable' => $googleData
        );

        // switch for specific types
        switch($thisParams['type']){
            case 'Donut':
                $googleChart['chartType'] = 'PieChart';
                $googleChart['options']['pieHole'] =  '0.4';
                break;
            case 'Column':
                if($thisParams['options']['material'] == 'on')
                    $googleChart['chartType'] = 'Bar';
                break;
        }

        // handle options
        foreach ($thisParams['options'] as $thisOption => $thisOptionCount) {
            switch ($thisOption) {
                case 'is3D':
                    $googleChart['options']['is3D'] = true;
                    break;
                case 'legend':
                    $googleChart['options']['legend'] = array(
                        'position' => 'right'
                    );
                    break;
                case 'stacked':
                    $googleChart['options']['isStacked'] = true;
                    break;
                case 'reverse':
                    $googleChart['options']['reverse'] = true;
                    break;
                case 'curvetypefunction':
                    $googleChart['options']['curveType'] = 'function';
                    break;
                case 'points':
                    $googleChart['options']['pointSize'] = 2;
                    break;
                case 'novlabels':
                    $googleChart['options']['vAxis']['textPosition'] = 'none';
                    break;
                case 'nohlabels':
                    $googleChart['options']['hAxis']['textPosition'] = 'none';
                    break;
                case 'logv':
                    $googleChart['options']['vAxis']['logScale'] = true;
                    break;
                case 'logh':
                    $googleChart['options']['hAxis']['logScale'] = true;
                    break;
            }
        }

        // set the title if we have one
        if ($thisParams['title'] != '') {
            $googleChart['options']['title'] = $thisParams['title'];
            $googleChart['options']['titleTextStyle'] = array(
                'fontSize' => 14
            );
        }

        //set the legend
        if ($thisParams['legend'] != '' && $thisParams['legend'] != '') {
            $googleChart['options']['legend'] = array(
                'position' => $thisParams['legend']
            );
        }

        // set axis max/min values
        if ($thisParams['minmax']['vmin'] != '') {
            if ($thisParams['type'] != 'Gauge')
                $googleChart['options']['vAxis']['minValue'] = $thisParams['minmax']['vmin'];
            else
                $googleChart['options']['min'] = $thisParams['minmax']['vmin'];
        }
        if ($thisParams['minmax']['vmax'] != '') {
            if ($thisParams['type'] != 'Gauge')
                $googleChart['options']['vAxis']['maxValue'] = $thisParams['minmax']['vmax'];
            else
                $googleChart['options']['max'] = $thisParams['minmax']['vmax'];
        }
        if ($thisParams['minmax']['hmin'] != '')
            $googleChart['options']['hAxis']['minValue'] = $thisParams['minmax']['hmin'];
        if ($thisParams['minmax']['hmax'] != '')
            $googleChart['options']['hAxis']['maxValue'] = $thisParams['minmax']['hmax'];

        // specific for rht Gauage Charts
        if ($thisParams['type'] == 'Gauge') {
            if ($thisParams['minmax']['rto'] != '')
                $googleChart['options']['redTo'] = $thisParams['minmax']['rto'];
            if ($thisParams['minmax']['yto'] != '')
                $googleChart['options']['yellowTo'] = $thisParams['minmax']['yto'];
            if ($thisParams['minmax']['gto'] != '')
                $googleChart['options']['greenTo'] = $thisParams['minmax']['gto'];
            if ($thisParams['minmax']['rfrom'] != '')
                $googleChart['options']['redFrom'] = $thisParams['minmax']['rfrom'];
            if ($thisParams['minmax']['yfrom'] != '')
                $googleChart['options']['yellowFrom'] = $thisParams['minmax']['yfrom'];
            if ($thisParams['minmax']['gfrom'] != '')
                $googleChart['options']['greenFrom'] = $thisParams['minmax']['gfrom'];
        }
        
        // specific for the Scatter Chart
        if ($thisParams['type'] == 'Scatter') {
            $googleChart['options']['hAxis']['title'] = $googleData['cols'][0]['label'];
            $googleChart['options']['vAxis']['title'] = $googleData['cols'][1]['label'];
        }
        
         // specific for the Bubble Chart
        if ($thisParams['type'] == 'Bubble') {
            $googleChart['options']['hAxis']['title'] = $googleData['cols'][1]['label'];
            $googleChart['options']['vAxis']['title'] = $googleData['cols'][2]['label'];
            
            //add 10% height to y-axis (fat bubbles positioned chart edges are just half visible)
            //get values for y-axis
            foreach($googleData['rows'] as $rIdx => $col){
                $yValues[] = $col['c'][2]['v'];
            }
            $googleChart['options']['vAxis']['maxValue'] = round( (max($yValues) * 1.1) , 0);
        }
        

        // specific for the Combo Chart
        if ($thisParams['type'] == 'Combo') {
            $googleChart['options']['seriesType'] = 'bars';

            // loop over the series settings
            foreach ($thisParams['dataseries'] as $seriesId => $seriesData) {

                //if ($seriesData['renderer'] != '')
                $googleChart['options']['series'][$seriesId] = array(
                    'type' => (empty($seriesData['renderer']) ? 'bars' : $seriesData['renderer']),
                    'targetAxisIndex' => ( $seriesData['axis'] == 'S' ? 1 : 0)
                );
            }
        }

        // handle the colors
        include('modules/KReports/config/KReportColors.php');
        if ($thisParams['colors'] != '' && isset($kreportColors[$thisParams['colors']])) {
            $googleChart['options']['colors'] = $kreportColors[$thisParams['colors']]['colors'];
        }

        // see if we have a special color for a series
        foreach ($thisParams['dataseries'] as $seriesId => $seriesData) {
            if ($seriesData['color'] != '')
                $googleChart['options']['colors'][$seriesId] = '#' . $seriesData['color'];
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