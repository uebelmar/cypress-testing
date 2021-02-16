<?php

require_once('modules/KReports/Plugins/prototypes/kreportvisualizationplugin.php');

use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\KReports\KReportRenderer;
use SpiceCRM\modules\KReports\KReportChartData;

class KHighChart extends kreportvisualizationplugin
{

    var $report = null;

    function __construct()
    {

    }

    /**
     * @deprecated
     * @return string
     */
    public function getHeader()
    {
        $coreString = "<script type='text/javascript' src='custom/k/HighCharts/js/highcharts.js'></script>";
        $coreString .= "<script type='text/javascript' src='custom/k/HighCharts/js/highcharts-more.js'></script>";
        $coreString .= "<script type='text/javascript' src='custom/k/HighCharts/js/modules/funnel.js'></script>";
        $coreString .= "<script type='text/javascript' src='custom/modules/KReports/Plugins/Visualization/highcharts/highchartstools" . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . ".js'></script>";
        return $coreString;
    }

    public function getItemUpdate($thisReport, $thisParams, $snaphotid = 0, $addReportParams = array())
    {
        $this->report = $thisReport;
        return $this->wrapHighChartData($this->getChartData($thisReport, $thisParams, 0, $addReportParams), $thisDivId, $thisParams);
    }

    public function getItem($thisDivId, $thisReport, $thisParams, $addReportParams = array(), $snapshotid = 0)
    {
        $this->report = $thisReport;

        $chartDataString = $this->wrapHighChartData($this->getChartData($thisReport, $thisParams, $snapshotid, $addReportParams), $thisDivId, $thisParams);


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
        $chartParams['showEmptyValues'] = ($thisParams['options']['emptyvalues'] == 'on' ? true : false);
        if ($thisParams['context'] != '')
            $chartParams['context'] = $thisParams['context'];

        $rawData = $chartDataObj->getChartData($thisReport, $snapshotid, $chartParams, $dimensions, $dataseries, $addReportParams);

        return $this->convertRawToHighChartData($rawData['chartData'], $rawData['dimensions'], $rawData['dataseries']);
    }

    public function convertRawToHighChartData($chartData, $dimensions, $dataseries)
    {
        $highChartData = array();

        $kreportRenderer = new KReportRenderer($this->report);
        foreach ($dataseries as $thisDataseries) {
            $dataseries[$thisDataseries['fieldid']]['renderer'] = $this->report->getXtypeRenderer($this->report->fieldNameMap[isset($thisDataseries['dimfieldid']) ? $thisDataseries['dimfieldid'] : $thisDataseries['fieldid']]['type'], isset($thisDataseries['dimfieldid']) ? $thisDataseries['dimfieldid'] : $thisDataseries['fieldid']);
        }

        $dataSetArray = array();
        foreach ($chartData as $thisDimensionId => $thisData) {
            $highChartData['xAxis']['categories'][] = $dimensions[0]['values'][$thisDimensionId];
            foreach ($dataseries as $thisSeriesID => $thisSeriesData) {
                if (!empty($thisSeriesData['renderer']))
                    $dataSetArray[$thisSeriesID][] = array(
                        'y' => $thisData[$thisSeriesID],
                        'dimensionname' => $thisDimensionId,
                        // ToDo add flexible Color based on entry data
                        // 'color' => rand ( 1 , 5 ) > 2 ? '#0404B4' : '#04B404',
                        'name' => $kreportRenderer->{$thisSeriesData['renderer']}($thisSeriesID, $thisData),
                    );
                else
                    $dataSetArray[$thisSeriesID][] = array(
                        'y' => $thisData[$thisSeriesID],
                        'dimensionname' => $thisDimensionId,
                        'name' => $thisData[$thisSeriesID]
                    );
            }
        }

        foreach ($dataseries as $thisSeriesID => $thisSeriesData) {
            $thisDataset = array(
                'name' => ($thisSeriesData['name'] != '' ? $thisSeriesData['name'] : $thisSeriesData['fieldid']),
                'data' => $dataSetArray[$thisSeriesID],
                // 'color' => rand ( 1 , 5 ) > 2 ? '#F781F3' : '#04B404'
            );

            if ($thisSeriesData['axis'] != '') {
                // set the data for the series arry we return to builkd the axis afterwards
                if (!isset($highChartData['yAxis'][$thisSeriesData['axis']])) {
                    $highChartData['yAxis'][$thisSeriesData['axis']] = array(
                        'counter' => count($highChartData['yAxis'] ?? []) + 1,
                        'displaytype' => $thisSeriesData['displaytype'],
                        'displaycolor' => $thisSeriesData['color'],
                        'title' => array(
                            'text' => $thisSeriesData['axis']
                        )
                    );
                }

                // set the axis for the series
                $thisDataset['yAxis'] = $highChartData['yAxis'][$thisSeriesData['axis']]['counter'];
                // the type renderer
                if (!empty($highChartData['yAxis'][$thisSeriesData['axis']]['displaytype'])) {
                    $thisDataset['type'] = $highChartData['yAxis'][$thisSeriesData['axis']]['displaytype'];
                }
                if (!empty($highChartData['yAxis'][$thisSeriesData['axis']]['displaycolor'])) {
                    $thisDataset['color'] = $highChartData['yAxis'][$thisSeriesData['axis']]['displaycolor'];
                }
            }
            if ($thisSeriesData['renderer'] != '')
                $thisDataset['renderAs'] = $thisSeriesData['renderer'];

            $highChartData['series'][] = $thisDataset;
        }


        $highChartData['xAxis']['type'] = 'category';

        // if all goes wrong we end up here ...
        return $highChartData;
    }

    public function wrapHighChartData($chartData, $divId, $thisParams)
    {
        $typeArray = explode('_', $thisParams['type']);

        $highChart = array(
            'chart' => array(
                'type' => $typeArray[0],
                'renderTo' => $divId
            ),
            'credits' => array(
                'enabled' => false
            ),
            'title' => array(
                'text' => null
            ),
            'legend' => array(
                'enabled' => false
            ),
            'yAxis' => array(
                array(
                    'type' => 'linear',
                    'title' => array()
                )
            ),
            'xAxis' => $chartData['xAxis'],
            'series' => $chartData['series'],
            'plotOptions' => array(
                $typeArray[0] => array(
                    'dataLabels' => array(
                        'enabled' => true,
                        'allowOverlap' => ($thisParams['options']['allowOverlap'] == "on" ? 1 : 0),
                        // 'inside' => true,
                        'format' => '{point.name}'
                    )
                )
            ),
            'tooltip' => array(
                'useHTML' => true,
                'headerFormat' => '<span style="color:{point.color};font-weight:bold">{point.x}</span><br/>',
                'pointFormat' => '<span style="color:{point.color}">&#9679;</span> {series.name}: <span style="font-weight:bold">{point.name}</span><br/>'
            )
        );
        // add Axis
        foreach ($chartData['yAxis'] as $thisLabel => $thisAxis) {
            $highChart['yAxis'][] = array(
                'type' => 'linear',
                'title' => $thisAxis['title'],
                'opposite' => true
            );
            if (!empty($thisAxis['displaytype']))
                $highChart['plotOptions'][$thisAxis['displaytype']] = array(
                    'dataLabels' => array(
                        'enabled' => true,
                        'inside' => true,
                        'format' => '{point.name}'
                    )
                );
        }

        switch ($typeArray[1]) {
            case 'donut':
                $highChart['plotOptions'][$typeArray[0]]['innerSize'] = '50%';
                break;
            case '180';
                $highChart['plotOptions'][$typeArray[0]]['startAngle'] = -90;
                $highChart['plotOptions'][$typeArray[0]]['endAngle'] = 90;
                $highChart['plotOptions'][$typeArray[0]]['center'] = array('50%', '75%');
                break;
            case 'donut180';
                $highChart['plotOptions'][$typeArray[0]]['innerSize'] = '50%';
                $highChart['plotOptions'][$typeArray[0]]['startAngle'] = -90;
                $highChart['plotOptions'][$typeArray[0]]['endAngle'] = 90;
                $highChart['plotOptions'][$typeArray[0]]['center'] = array('50%', '75%');
                break;
            case 'stacked':
                $highChart['plotOptions'][$typeArray[0]]['stacking'] = 'normal';
                break;
            case 'stckper':
                $highChart['plotOptions'][$typeArray[0]]['stacking'] = 'percent';
                break;
            case 'stckppl':
                $highChart['plotOptions'][$typeArray[0]]['stacking'] = 'percent';
                $highChart['chart']['polar'] = true;
                break;
            case 'stckpol':
                $highChart['plotOptions'][$typeArray[0]]['stacking'] = 'normal';
                $highChart['chart']['polar'] = true;
                break;
            case 'polr':
                $highChart['chart']['polar'] = true;
                break;
            case '2d':
                $seriesCount = count($highChart['series']);
                $serisCounter = 1;
                foreach ($highChart['series'] as &$thisSeries) {
                    $thisSeries['size'] = round(100 / $seriesCount * $serisCounter) . '%';
                    if ($serisCounter > 1) {
                        $thisSeries['innerSize'] = round(100 / $seriesCount * ($serisCounter - 1)) . '%';
                    }
                    $serisCounter++;
                }
                break;
            case 'trend':
                $highChart['series'][] = $this->get_regression_series(reset($highChart['series']));
                break;
        }


        // set the title
        if (!empty($thisParams['title'])) {
            $highChart['title'] = array(
                'text' => $thisParams['title']
            );
        }

        // handle the Legend
        switch ($thisParams['legend']) {
            case 'right':
                $highChart['legend'] = array(
                    'align' => 'right',
                    'verticalAlign' => 'middle',
                    'layout' => 'vertical'
                );
                break;
            case 'left':
                $highChart['legend'] = array(
                    'align' => 'left',
                    'verticalAlign' => 'middle',
                    'layout' => 'vertical'
                );
                break;
            case 'top':
                $highChart['legend'] = array(
                    'verticalAlign' => 'top',
                );
                break;
            case 'bottom':
                $highChart['legend'] = array(
                    'verticalAlign' => 'bottom',
                );
                break;
            default:
                break;
        }

        // special handling for display field
        switch ($typeArray[0]) {
            case 'pie':
            case 'pie180':
            case 'donut':
            case 'donut180':
            case 'funnel':
            case 'pyramid':
                $highChart['tooltip']['headerFormat'] = "";

                if ($thisParams['legend'] != 'none') {
                    $highChart['plotOptions'][$typeArray[0]]['showInLegend'] = true;
                    $highChart['legend']['labelFormat'] = '{dimensionname}';
                }
                $highChart['plotOptions'][$typeArray[0]]['dataLabels']['format'] = '{point.dimensionname}';
                        $highChart['plotOptions'][$typeArray[0]]['depth'] = 30;

                //display percentage
                switch($typeArray[0]){
                    case 'pie':
                    case 'pie180':
                    case 'donut':
                    case 'donut180':
                        $highChart['plotOptions'][$typeArray[0]]['dataLabels']['format'] = '{point.dimensionname} {point.percentage:.1f} %';
                        break;
                }
                break;
        }

        // handle Options
        foreach ($thisParams['options'] as $thisOption => $thisOptionCount) {
            switch ($thisOption) {
                case '3d':
                    //$highChart['chart']['margin'] = 75;
                    $highChart['chart']['options3d'] = array(
                        'enabled' => true,
                        'alpha' => 2,
                        'beta' => 1,
                        'depth' => 50,
                        'viewDistance' => 25
                    );
                    if($typeArray[0] == "pie"){
                        $highChart['plotOptions'][$typeArray[0]]['depth'] = 30;
                        $highChart['chart']['options3d']['alpha'] = 15;
                        $highChart['chart']['options3d']['beta'] = 15;
                    }
                    break;
                case 'logv':
                    foreach ($highChart['yAxis'] as &$thisAxis) {
                        $thisAxis['type'] = 'logarithmic';
                        $thisAxis['pointStart'] = 1;
                    }
                    break;
                case 'hideValues':
                    if($thisOptionCount == "on" )
                        $highChart['plotOptions'][$typeArray[0]]['dataLabels']['format'] = '{point.dimensionname}';
                    break;
                case 'rotateLabels':
                    $highChart['xAxis']['labels']['rotation'] = 270;
                    break;
                case 'hideLabels':
                    if($thisOptionCount == "on" )
                        $highChart['plotOptions'][$typeArray[0]]['dataLabels']['enabled'] = false;
//                    $highChart['xAxis']['labels']['enable'] = false;
//                    foreach ($highChart['yAxis'] as &$thisAxis) {
//                        $thisAxis['labels']['enable'] = false;
//                    }
                    break;
                case 'allowOverlap':
                    $highChart['plotOptions'][$typeArray[0]]['dataLabels']['allowOverlap'] = ($thisOptionCount == "on" ? 1 : 0);
                    break;
                case 'colorByPoint':
                    $highChart['plotOptions']['series']['colorByPoint'] = ($thisOptionCount == "on" ? 1 : 0);
                    break;
            }
        }

        // handle the colors
        include('modules/KReports/config/KReportColors.php');
        if ($thisParams['colors'] != '' && isset($kreportColors[$thisParams['colors']])) {
            $highChart['colors'] = $kreportColors[$thisParams['colors']]['colors'];
            // check if we have and if set the label color
            if (!empty($kreportColors[$thisParams['colors']]['labelColor'])) {
                foreach ($highChart['plotOptions'] as $thisType => &$thisOptions)
                    if (is_array($kreportColors[$thisParams['colors']]['labelColor'])) {
                        if (isset($kreportColors[$thisParams['colors']]['labelColor'][$thisType]))
                            $thisOptions['dataLabels']['color'] = $kreportColors[$thisParams['colors']]['labelColor'][$thisType];
                    } else
                        $thisOptions['dataLabels']['color'] = $kreportColors[$thisParams['colors']]['labelColor'];
            }

            // set additonal styles
            if (!empty($kreportColors[$thisParams['colors']]['addStyles'])) {
                foreach ($kreportColors[$thisParams['colors']]['addStyles'] as $thisType => $thisStyleDefs) {
                    if (isset($highChart['plotOptions'][$thisType]))
                        $highChart['plotOptions'][$thisType] = array_merge($highChart['plotOptions'][$thisType], $thisStyleDefs);
                    else
                        $highChart['plotOptions'][$thisType] = $thisStyleDefs;
                }
            }
        }
        return $highChart;
    }

    private function get_regression_series($series)
    {
        $seriesData = $series['data'];

        // get the avg
        $sumX = 0;
        $sumY = 0;
        for ($i = 0; $i < count($seriesData); $i++) {
            $thisPoint = $seriesData[$i];
            if (!empty($thisPoint['x']))
                $sumX += $thisPoint['x'];
            else
                $sumX += $i;

            $sumY += $thisPoint['y'];
        }
        $sumX = $sumX / count($seriesData);
        $sumY = $sumY / count($seriesData);

        // get the squareDiff
        $x1 = 0;
        $x2 = 0;
        for ($i = 0; $i < count($seriesData); $i++) {
            $thisPoint = $seriesData[$i];
            if (!empty($thisPoint['x'])) {
                $x1 += ($thisPoint['x'] - $sumX) * ($thisPoint['y'] - $sumY);
                $x2 += pow(($thisPoint['x'] - $sumX), 2);
            } else {
                $x1 += ($i - $sumX) * ($thisPoint['y'] - $sumY);
                $x2 += pow(($i - $sumX), 2);
            }
        }
        $f1 = $x1 / $x2;
        $f2 = $sumY - ($f1 * $sumX);

        $newSeries = array(
            'name' => $GLOBALS['mod_strings']['LBL_TRENDLINENAME'] . ' ' . $series['name'],
            'type' => 'line',
            'data' => array()
        );
        for ($i = 0; $i < count($seriesData); $i++) {
            $thisPoint = $seriesData[$i];
            if (!empty($thisPoint['x']))
                $newSeries['data'][] = array(
                    'y' => $f2 + ($thisPoint['x'] * $f1)
                );
            else
                $newSeries['data'][] = array(
                    'y' => $f2 + ($i * $f1)
                );
        }
        return $newSeries;
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
                case 'colorByPoint':
                    $dataSource['chart']['colorByPoint'] = 1;
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

}
