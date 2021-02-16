<?php

use SpiceCRM\modules\KReports\KReportChartData;
use SpiceCRM\modules\KReports\KReportRenderer;

use SpiceCRM\includes\authentication\AuthenticationController;

require_once('modules/KReports/KReport.php');
require_once('modules/KReports/KReportChartData.php');
require_once('modules/KReports/Plugins/prototypes/kreportvisualizationplugin.php');

class KAmChart extends kreportvisualizationplugin
{

    var $report = null;

    function __construct()
    {

    }

//    public function getItemUpdate($thisReport, $thisParams, $snaphotid = 0, $addReportParams = array())
//    {
//        $this->report = $thisReport;
//        return $this->wrapAmChartData($this->getChartData($thisReport, $thisParams, 0, $addReportParams), $thisDivId, $thisParams);
//    }
//
    public function getItem($thisDivId, $thisReport, $thisParams, $addReportParams = array(), $snapshotid = 0)
    {
        $this->report = $thisReport;

        $chartDataString = $this->wrapAmChartData($this->getChartData($thisReport, $thisParams, $snapshotid, $addReportParams), $thisDivId, $thisParams);


        return $chartDataString;
    }

    public function getChartData($thisReport, $thisParams, $snapshotid = 0, $addReportParams = array())
    {
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
                'axis' => $thisDataSeriesData['axis'],
                // 2013-03-19 handle Chart Function properly Bug #448
                'chartfunction' => $thisDataSeriesData['chartfunction'],
                'displaytype' => $thisDataSeriesData['renderer'],
                'color' => $thisDataSeriesData['color']
            );
        }

        // set Chart Params
        $chartParams = array();
        foreach($thisParams['options'] as $opt => $optvalue){
            $chartParams['options'][$opt] = ($optvalue == "on" ? true : false);
        }
        if ($thisParams['context'] != '')
            $chartParams['context'] = $thisParams['context'];

        $chartParams['type'] = $this->convertTypeForKReportChartData($thisParams['type']);

        $rawData = $chartDataObj->getChartData($thisReport, $snapshotid, $chartParams, $dimensions, $dataseries, $addReportParams);

        //convert for display
        $convertFunction = $this->getConvertFunctionName($thisParams['type']);
        return $this->$convertFunction($rawData['chartData'], $rawData['dimensions'], $rawData['dataseries'], $rawData['valueAxes'], $chartParams);
    }

    /**
     * retrieve name of function transforming data for chartWrapper
     * @param String $charttype
     * @return String
     */
    public function getConvertFunctionName($charttype){
        $typeArray = explode('_', $charttype);

        $fn = "convertRawToAmChartData";
        switch($typeArray[1]){
            case 'scatter':
            case 'bubble':
                $fn.= ucfirst($typeArray[1]);
                break;
            default:
        }
        return $fn;
    }

    public function convertTypeForKReportChartData($type){

        switch($type){
            case 'xy_bubble':
                $type = 'Bubble';
                break;
        }
        return $type;
    }

    public function convertRawToAmChartData($chartData, $dimensions, $dataseries, $valueAxes, $chartParams = array())
    {
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('chartData');
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($chartData);
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('dimensions');
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($dimensions);
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('dataseries');
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($chartParams);

        $amChartData = array();
        if(!empty($valueAxes)){
            $amChartData['valueAxes'] = array_values($valueAxes);
        }
        $amChartData['graphs'] = array();

        $kreportRenderer = new KReportRenderer();
        foreach ($dataseries as $thisDataseries) {
            $dataseries[$thisDataseries['fieldid']]['renderer'] = $this->report->getXtypeRenderer($this->report->fieldNameMap[isset($thisDataseries['dimfieldid']) ? $thisDataseries['dimfieldid'] : $thisDataseries['fieldid']]['type'], isset($thisDataseries['dimfieldid']) ? $thisDataseries['dimfieldid'] : $thisDataseries['fieldid']);
            $amChartData['valueField'] = $thisDataseries['fieldid']; //for pie chart
        }

        $dataSetArray = array();
        $dataProviderIndex = 0;
        foreach ($chartData as $thisDimensionId => $thisData) {
            $amChartData['xAxis']['categories'][] = $dimensions[0]['values'][$thisDimensionId];

            foreach($thisData as $fieldid => $value){
                $amChartData['dataProvider'][$dataProviderIndex][$fieldid] = $value;
            }
            $amChartData['dataProvider'][$dataProviderIndex][$dimensions[0]['fieldid']] = $dimensions[0]['values'][$thisDimensionId];
            $dataProviderIndex++;
        }

        $graphIdx = 0;
        foreach ($dataseries as $thisSeriesID => $thisSeriesData) {
            $amChartData['graphs'][$graphIdx] = array(
                'title' => (!empty($thisSeriesData['name']) ? $thisSeriesData['name'] : $thisSeriesID), //on 2 dimensions, use $thisSeriesID
                'valueField' => $thisSeriesData['fieldid'],
            );

            if(!empty($valueAxes)){
                if(empty($thisSeriesData['axis'])){
                    $amChartData['graphs'][$graphIdx]['valueAxis'] = "va";
                }
                else{
                    foreach($valueAxes as $valueAxisId => $valueAxis){
                        if($thisSeriesData['axis'] == $valueAxisId){
                            $amChartData['graphs'][$graphIdx]['valueAxis'] = $valueAxisId;
                            continue;
                        }
                    }
                }
            }
            if(!empty($thisSeriesData['displaytype'])){
                $amChartData['graphs'][$graphIdx]['displaytype'] = $thisSeriesData['displaytype'];
            }
            if(!empty($thisSeriesData['color'])){
                $amChartData['graphs'][$graphIdx]['color'] = $thisSeriesData['color'];
            }
            if(!empty($thisSeriesData['renderer'])){
                $amChartData['graphs'][$graphIdx]['renderAs'] = $thisSeriesData['renderer'];
            }


            //add params to graphs for marimekko
            if(isset($chartParams['type']) && preg_match("/marimekko/", $chartParams['type'])) {
                $amChartData['graphs'][$graphIdx]['labelText'] = "[[value]]";
                $amChartData['graphs'][$graphIdx]['balloonText'] = "[[title]] [[percents]] %";
            }
            $graphIdx++;
        }


        $amChartData['xAxis']['type'] = 'category';
        $amChartData['categoryField'] = $dimensions[0]['fieldid'];
        // if all goes wrong we end up here ...

//\SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($amChartData);
        return $amChartData;
    }

    public function wrapAmChartData($chartData, $divId, $thisParams)
    {
//\SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($chartData);
        $typeArray = explode('_', $thisParams['type']);

        $amChart = array(
            'creditsPosition' => 'top-right',
            'type' => strtolower($typeArray[0]),
            'responsive' => array(
                'enabled' => true
            ),
            'legend' => array(
                'enabled' => false
            ),
            'synchronizeGrid' => true,
            'dataProvider' => $chartData['dataProvider'],
            'decimalSeparator' => AuthenticationController::getInstance()->getCurrentUser()->getPreference('decimal_seperator'),
            'thousandsSeparator' => AuthenticationController::getInstance()->getCurrentUser()->getPreference('number_grouping_seperator'),
        );

        if(!empty($chartData['valueAxes'])){
            $vaIds = array_keys($chartData['valueAxes']);
            $amChart['valueAxes'] = array_values($chartData['valueAxes']);
        }

        // set the title
        if (!empty($thisParams['title'])) {
            $amChart['titles'] = array(
                array('text' => $thisParams['title'], 'size' => 15)
            );
        }


        // handle the Legend
        switch ($thisParams['legend']) {
            case 'right':
                $amChart['legend'] = array(
                    'position' => 'right',
                    'autoMargins' => true
                );
                break;
            case 'left':
                $amChart['legend'] = array(
                    'position' => 'left',
                    'autoMargins' => true
                );
                break;
            case 'top':
                $amChart['legend'] = array(
                    'position' => 'top',
                    'autoMargins' => true
                );
                break;
            case 'bottom':
                $amChart['legend'] = array(
                    'position' => 'bottom',
                    'autoMargins' => true
                );
                break;
            default:
                break;
        }

        // special handling depending on chart type
        switch ($amChart['type']) {
            case 'pie':
                $amChart['valueField'] = $chartData['valueField'];
                $amChart['titleField'] = $chartData['categoryField'];

                //handle pie variations like donut
                if($typeArray[1] == "donut"){
                    $amChart['radius'] = "42%";
                    $amChart['innerRadius'] = "60%";
                }
                break;

            case 'serial':
                $amChart['categoryField'] = $chartData['categoryField'];

//                $graph['valueAxis'] = array(
//                    'gridColor' => '#666666',
//                    'gridAlpha' => 0.2,
//                    'dashLength' => 0,
//                );
                $amChart['gridAboveGraphs'] = true;

                $amChart['chartCursor'] = array(
                    'categoryBalloonEnabled' => false,
                    'cursorAlpha' => 0,
                    'zoomable' => false
                );

                $amChart['categoryAxis'] = array(
                    'gridPosition' => 'start',
                    'gridAlpha' => 0,
                    'tickPosition' => 'start',
                    'tickLength' => 20
                );


                //handle serial variations like column, bar
                if(!isset($graph)) $graph = array();
                $this->setSerialGraphPropertiesAccordingToType($graph, $typeArray[1], $amChart);
//                switch($typeArray[1]){
//                    case 'column':
//                        $graph['type'] = "column";
//                        $graph['fillAlphas'] = 1;
//                        break;
//                    case 'bar':
//                        $graph['type'] = "column";
//                        $graph['fillAlphas'] = 1;
//                        $amChart['rotate'] = true;
//                        break;
//                    case 'line':
//                        $graph['type'] = "line";
//                        $graph['bullet'] = "round";
//                        $graph['fillAlphas'] = 0;
//                        break;
//                    case 'area':
//                        $graph['type'] = "line";
//                        $graph['bullet'] = "round";
//                        $graph['fillAlphas'] = 0.5;
//                        break;
//                }


                //handle further serial variations like trendlines, stacked
                switch($typeArray[2]){
                    case 'trend':
//@to redo: chartData structure chamged... Adapt  this->get_trendlines()!
//                        $amChart['trendLines'] = $this->get_trendlines($dataProvider);
                        //$amChart['categoryAxis']['parseDates'] = true;
                        break;
                    case 'stacked':
                        $amChart['categoryAxis']['startOnAxis'] = true;
                        $amChart['valueAxes'][0]['stackType'] = "regular";
                        break;
                    case 'marimekko':
                        $amChart['categoryAxis'] = array(
                            'gridAlpha' => 0.8,
                            'axisAlpha' => 0,
                            'widthField' => 'total_'.$chartData['categoryField'],
                            'gridPosition' => 'start'
                        );
                        $amChart['valueAxes'][0] = array(
                            'stackType' => '100% stacked',
                            'gridAlpha' => 0,
                            'unit' => '%',
                            'axisAlpha' => 0
                        );
                        break;
                }


                break;

            case 'funnel':
                $amChart['valueField'] = $chartData['valueField'];
                $amChart['titleField'] = $chartData['categoryField'];
                $amChart['neckHeight'] = "30%";
                $amChart['neckWidth'] = "40%";
                $amChart['marginRight'] = 60;
                $amChart['marginLeft'] = 60;
                $amChart['funnelAlpha'] = 0.9;
                $amChart['startX'] = 0;
                $amChart['outlineThickness'] = 1;
                $flag = SORT_DESC;

                //handle funnel variations like pyramid
                if($typeArray[1] == "pyramid"){
                    $amChart['rotate'] = true;
                    $amChart['startX'] = -500;
                    $amChart['neckWidth'] = "0%";
                    $flag = SORT_DESC;
                }

                //sort values
                foreach ($amChart['dataProvider'] as $key => $row) {
                    $y[$key] = $row[$chartData['valueField']];
                    $dimensionname[$key] = $row[$chartData['categoryField']];
                    //$name[$key] = $row['name'];
                }
                array_multisort($y, $flag, $dimensionname, $flag, $amChart['dataProvider']);
                break;

            case 'xy':
                if(isset($amChartData['xAxis'])) unset($amChartData['xAxis']);
                if(isset($amChartData['categoryField'])) unset($amChartData['categoryField']);

                $amChart["startDuration"] = 1.5;

                $amChart['graphs'][] = array(
                    'balloonText' => 'x:<b>[[x]]</b> y:<b>[[y]]</b><br>value:<b>[[value]]</b>',
                    'bullet' => 'circle',
                    'bulletAlpha' => 0.8,
                    'lineAlpha' => 0,
                    'fillAlphas' => 0,
                    'valueField' => 'value',
                    'xField' => 'x',
                    'yField' => 'y',
                    'maxBulletSize' => 100
                );
                break;

            case 'radar':
                break;
        }

        //merge chart type graph settings with each graph
        foreach($chartData['graphs'] as $gidx => $g){
            $amChart['graphs'][$gidx] = array_merge($g, $graph);
            //override serial type if necessary
            if(!empty($amChart['graphs'][$gidx]['displaytype'])){
                $this->setSerialGraphPropertiesAccordingToType($amChart['graphs'][$gidx], $amChart['graphs'][$gidx]['displaytype'], $amChart);
//                switch($amChart['graphs'][$gidx]['displaytype']){
//                    case 'line':
//                        unset($amChart['graphs'][$gidx]['type']);
//                        unset($amChart['graphs'][$gidx]['displaytype']);
//                        $amChart['graphs'][$gidx]['bullet'] = "round";
//                        $amChart['graphs'][$gidx]['lineThickness'] = 3;
//                        $amChart['graphs'][$gidx]['bulletSize'] = 7;
//                        $amChart['graphs'][$gidx]['bulletBorderAlpha'] = 1;
//                        $amChart['graphs'][$gidx]['bulletColor'] = "#FFFFFF";
//                        $amChart['graphs'][$gidx]['useLineColorForBulletBorder'] = true;
//                        $amChart['graphs'][$gidx]['bulletBorderThickness'] = 3;
//                        $amChart['graphs'][$gidx]['fillAlphas'] = 0;
//                        $amChart['graphs'][$gidx]['lineAlpha'] = 1;
//                        break;
//                    
//                    //@todo: bar, column
//                }
            }
        }

        // handle Options
        if(!is_array($thisParams['options'])) $thisParams['options'] = array();
        foreach($thisParams['options'] as $opt => $optvalue){
            $thisParams['options'][$opt] = ($optvalue == "on" ? true : false);
        }
        //applicable to pie: 3d, hideLabels, hideValues, showEmptyValues
        //applicable to serial: 3d, hideValues
        foreach ($thisParams['options'] as $thisOption => $thisOptionCount) {
            if($thisOptionCount === true){
                switch ($thisOption) {
                    case '3d':
                        $amChart['depth3D'] = 10;
                        $amChart['angle'] = 15;

                        //overwrite option with other values for funnel
                        if($amChart['type'] == 'funnel'){
                            $amChart['startX'] = -500;
                            $amChart['depth3D'] =100;
                            $amChart['angle'] = 40;
                            $amChart['outlineAlpha'] = 1;
                            $amChart['outlineColor'] = "#FFFFFF";
                            $amChart['outlineThickness'] = 2;
                        }
                        break;
//                    case 'logv':                    
//                        break;
                    case 'hideLabels':
                        $amChart['labelsEnabled'] = false;
                        break;
                    case 'hideValues':
                        $amChart['balloon']['enabled'] = false;
                        break;
                    case 'showEmptyValues':
                        $amChart['showZeroSlices'] = 1; //labelsEnabled has to be 1 to show zero slices
                        break;
                }
            }
        }


        // handle the colors
        include('modules/KReports/config/KReportColors.php');
        if ($thisParams['colors'] != '' && isset($kreportColors[$thisParams['colors']])) {
            $amChart['colors'] = $kreportColors[$thisParams['colors']]['colors'];
        }


        //\SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($amChart);
        return $amChart;
    }

    public function convertRawToAmChartDataBubble($chartData, $dimensions, $dataseries) {
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

        //manipulate data for am Wrapper dataTable
        $amData = array();
        $amData['cols'] = array();
        $amData['rows'] = array();

        //handle 3rd dimension when set
        $dimCount = count($dimensions);

        //cols definitions
        $amData['cols'][] = array('id' => 'id', 'type' => 'string', 'label' => '');
        $dimCounter = 1;
        foreach ($dimensions as $thisDimension) {
            $amData['cols'][] = array('id' => $thisDimension['fieldid'], 'type' => ($dimCounter == 3 ? 'string' : 'number'), 'label' => $fields[$fieldMap[$thisDimension['fieldid']]]['name']);
            $dimCounter++;
        }
        if($dimCount < 3)
            $amData['cols'][] = array('id' => 'pack', 'type' => 'string', 'label' => '');
        $amData['cols'][] = array('id' => 'size', 'type' => 'number', 'label' => $fields[$fieldMap[$dataserieFieldId]]['name']);

        //rows values
        switch($dimCount){
//            case 2:
//                foreach ($chartData as $dim1Value => $dim2) {
//                    foreach($dim2 as $dim2Value => $serieValue){
//                        $rowArray = array(
//                            'y' => $dim2Value, //y-axis
//                            'x' => $dim1Value, //x-axis
////                        $rowArray[] = array('v' => $fields[$fieldMap[$dataserieFieldId]]['name']); //pack
//                            'value' => 20 //$serieValue //size
//                        ); 
//                        $amData['dataProvider'][] = $rowArray;
//                    }
//                }
//                break;

            case 3:
                foreach ($chartData as $dim1Value => $dim2) {
                    foreach($dim2 as $dim2Value => $dim3){
                        foreach($dim3 as $dim3Value => $serieValue){
                            $rowArray = array(
                                'y' => $dim2Value,  //y-axis
                                'x' => $dim1Value,  //x-axis
                                'value' => $serieValue //size
                            );
                            $amData['dataProvider'][] = $rowArray;
                        }
                        break;
                    }
                }
                break;
        }

        return $amData;
    }

    /**
     * Only 1 trendline for now
     * @param type $dataProvider
     * @return array
     */
    private function get_trendlines($dataProvider)
    {
        $trendlines = array();

        //sort values
        foreach ($dataProvider as $key => $row) {
            $y[$key] = $row['y'];
            $dimensionname[$key] = $row['dimensionname'];
            $name[$key] = $row['name'];
        }
        array_multisort($y, SORT_ASC, $name, $flag, $dimensionname, $flag, $dataProvider);

        $finalKey = count($y)-1;

        $trendlines[] = array(
            'finalDate' => $dimensionname[$finalKey],
            'finalValue' => $y[$finalKey],
            'initialDate' => $dimensionname[0],
            'initialValue' => $y[0],
            'lineColor' => '#CC0000'
        );
        return $trendlines;
    }

    public function createChartDataSource($fusionData, $thisParams)
    {
        $dataSource = array(
            'chart' => array(
                'showLegend' => 0,
                'formatNumberScale' => 0,
                'plotFillRatio' => 100
            )
        );

        foreach ($fusionData as $item => $data) {
            $dataSource[$item] = $data;
        }

        // handle the colors
        include('modules/KReports/config/KReportColors.php');
        if ($thisParams['colors'] != '' && isset($kreportColors[$thisParams['colors']])) {
            $dataSource['chart']['paletteColors'] = str_replace('#', '', implode(',', $kreportColors[$thisParams['colors']]['colors']));
        }

        // set the title if we have one
        if ($thisParams['title'] != '') {
            $dataSource['chart']['caption'] = $thisParams['title'];
        }

        // process the options
        foreach ($thisParams['options'] as $thisOption => $thisOptionCount) {
            switch ($thisOption) {
                case 'legend':
                    $dataSource['chart']['showLegend'] = 1;
                    break;
                case 'useRoundEdges':
                    $dataSource['chart']['useRoundEdges'] = 1;
                    break;
                case 'hideLabels':
                    $dataSource['chart']['showLabels'] = 0;
                    break;
                case 'hideValues':
                    $dataSource['chart']['showValues'] = 0;
                    break;
                case 'allowOverlap':
                    $dataSource['chart']['allowOverlap'] = 1;
                    break;
                case 'formatNumberScale':
                    $dataSource['chart']['formatNumberScale'] = 1;
                    break;
                case 'rotateValues':
                    $dataSource['chart']['rotateValues'] = 1;
                    break;
                case 'placeValuesInside':
                    $dataSource['chart']['placeValuesInside'] = 1;
                    break;
                case 'showShadow':
                    $dataSource['chart']['showShadow'] = 1;
                    break;
            }
        }

        // $dataSource['chart']['exportenabled'] = 1;
        // Primary Axis
        if ($thisParams['minmax']['vmin'] != '') {
            $dataSource['chart']['yAxisMinValue'] = $thisParams['minmax']['vmin'];
            $dataSource['chart']['PYAxisMinValue'] = $thisParams['minmax']['vmin'];
        }
        if ($thisParams['minmax']['vmax'] != '') {
            $dataSource['chart']['yAxisMaxValue'] = $thisParams['minmax']['vmax'];
            $dataSource['chart']['PYAxisMaxValue'] = $thisParams['minmax']['vmin'];
        }

        //Secondary Axis
        if ($thisParams['minmax']['hmin'] != '')
            $dataSource['chart']['SYAxisMinValue'] = $thisParams['minmax']['hmin'];
        if ($thisParams['minmax']['hmax'] != '')
            $dataSource['chart']['SYAxisMaxValue'] = $thisParams['minmax']['hmax'];

        // misc parameters
        $dataSource['chart']['showBorder'] = 0;
        $dataSource['chart']['bgAlpha'] = '0';

        return $dataSource;
    }

    function parseExportData($exportedData)
    {
        return array(
            'type' => 'SVG',
            'data' => urldecode(base64_decode($exportedData))
        );
    }

    public function setSerialGraphPropertiesAccordingToType(&$graph, &$type, &$amChart){
        switch($type){
            case 'column':
                $graph['type'] = "column";
                $graph['fillAlphas'] = 1;
                break;
            case 'bar':
                $graph['type'] = "column";
                $graph['fillAlphas'] = 1;
                $amChart['rotate'] = true;
                break;
            case 'line':
                $graph['type'] = "line";
                $graph['bullet'] = "round";
                $graph['fillAlphas'] = 0;
                $graph['lineColor'] = $graph['color'];
                break;
            case 'area':
                $graph['type'] = "line";
                $graph['bullet'] = "round";
                $graph['fillAlphas'] = 0.5;
                $graph['lineColor'] = $graph['color'];
                break;
            case 'spline':
                $graph['type'] = "smoothedLine";
                $graph['negativeLineColor'] = "#666666";
                $graph['lineThickness'] = 2;
                $graph['fillAlphas'] = 0;
                $graph['lineColor'] = $graph['color'];
                break;
        }
    }

}
