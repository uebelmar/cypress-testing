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





require_once('modules/KReports/KReport.php');
require_once('modules/KReports/KReportChartData.php');
require_once('modules/KReports/Plugins/prototypes/kreportvisualizationplugin.php');

class kGoogleMap extends kreportvisualizationplugin{

    function __construct() {
        
    }

    /**
     * @deprecated: not in use anymore
     * @see kreportvisualizationplugin::getHeader()
     */
    public function getHeader() {
        $coreString = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
       // $coreString = "<script type='text/javascript' src='https://www.google.com/jsapi?autoload={modules:[{name:\"maps\",version:3,other_params:\"sensor=false\"}]}'></script>";
       // $coreString = '<script type="text/javascript" src="custom/modules/KReports/Plugins/Visualization/googlemaps/markerclusterer.js"></script><script type="text/javascript" src="custom/modules/KReports/Plugins/Visualization/googlemaps/StyledMarker.js"></script>';
        
        return $coreString;
    }

    /*
     * get only the data component if the selction has changed
     */

    public function getItemUpdate($thisReport, $thisParams, $snaphotid = 0, $addReportParams = array()) {
        $this->report = $thisReport;
        return json_encode($this->getMapData($thisReport, $thisParams, $snaphotid, $addReportParams));
    }

    /*
     * get the Chart Object to render into the visualization
     */
    public function getItem($thisDivId, $thisReport, $thisParams, $addReportParams = array()) {
        $this->report = $thisReport;
        $googleData = $this->getMapData($thisReport, $thisParams, 0, $addReportParams);
        $mapData = $this->wrapGoogleData($googleData, $thisDivId, $thisParams);
        return $mapData;
    }


    public function getMapData($thisReport, $thisParams, $snaphotid = 0, $addReportParams = array()) {
        
        
        $addReportParams['noFormat'] = true;
        $addReportParams['noEnumTranslation'] = false; //true; 2017-02-03 set false to have labels and values in legend with enum
        
        $reportResults = $thisReport->getSelectionResults($addReportParams, $snaphotid, false, '', array());
        
        //begin set color
        $thisParams['kreportgooglemapsstyledmarkers'] = false;
        if($thisParams['kreportgooglemapscolorcriteria'] != ''){
            $thisParams['kreportgooglemapsstyledmarkers'] = true;
            $storeColorCriteria = array();
            require_once 'modules/KReports/config/KReportColors.php';
        }
        //end
        
        //begin set legend
        $mapArray['legend']['display'] = $thisParams['kreportgooglemapslegend'];
        $mapArray['legend']['title'] = (empty($thisParams['kreportgooglemapslegendtitle']) ? 'Legend' : $thisParams['kreportgooglemapslegendtitle']);
        //end
        
        //begin set spiderfy
        $mapArray['spiderfy'] = $thisParams['kreportgooglemapsspiderfy'];
        //end
        
        //begin set route planner params
        $mapArray['routeplanner']['display'] = $thisParams['kreportgooglemapsrouteplanner'];
        $mapArray['routeplanner']['geocodeby'] = $thisParams['kreportgooglemapsrouteplannerwayptgcby'];
        //end

        //begin set circle designer params
        $mapArray['circledesigner']['display'] = $thisParams['kreportgooglemapscircledesigner'];
        $mapArray['circledesigner']['lat_field'] = $thisReport->fieldNameMap[$thisParams['kreportgooglemapstatitude']]['fieldname'];
        $mapArray['circledesigner']['lng_field'] = $thisReport->fieldNameMap[$thisParams['kreportgooglemapslongitude']]['fieldname'];
        $mapArray['circledesigner']['module'] = $thisParams['kreportgooglemapscircledesignermodule'];
        $mapArray['circledesigner']['displayfields'] = $thisParams['kreportgooglemapscircledesignerdisplayfields'];
        //end
        
        //check cluster param
        $mapArray['cluster'] = $thisParams['kreportgooglemapscluster'];
        
        
        $pinpointArray = array();
        foreach($reportResults as $thisRecord)
        {
            
            switch($thisParams['geocodeby']['gctype'])
            {
                case 'ADDRESS':
                    $thisPinPoint = array();
                    
                    if($thisParams['kreportgooglemapsaddressform']['kreportgooglemapsstreet'] != '' && $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapsstreet']] != '')
                        $thisPinPoint['street_address'] = $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapsstreet']];
                    
                    if($thisParams['kreportgooglemapsaddressform']['kreportgooglemapscity'] != '' && $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapscity']] != '')
                        $thisPinPoint['locality'] = $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapscity']];

                    if($thisParams['kreportgooglemapsaddressform']['kreportgooglemapspc'] != '' && $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapspc']] != '')
                        $thisPinPoint['postal_code'] = $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapspc']];
                    
                    if($thisParams['kreportgooglemapsaddressform']['kreportgooglemapscountry'] != '' && $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapscountry']] != '')
                        $thisPinPoint['country'] = $thisRecord[$thisParams['kreportgooglemapsaddressform']['kreportgooglemapscountry']];
                    
                    break;
                case 'LATLONG':
                     $thisPinPoint = array();
                     $thisPinPoint['longitude'] = $thisRecord[$thisParams['kreportgooglemapslongitude']];
                     $thisPinPoint['latitude'] = $thisRecord[$thisParams['kreportgooglemapstatitude']];
                     // store locations
                     if(!$storedLocations) $storedLocations = array();
                         $storedLocations[] = $thisPinPoint['longitude'].$thisPinPoint['latitude'];
                    break;
            }
            
            
            //begin info for route planner
            if($thisParams['kreportgooglemapsrouteplanner'] > 0){
                $thisPinPoint['routeLabel'] = $thisRecord[$thisParams['kreportgooglemapsrouteplannerwaypointlabel']];
                $thisPinPoint['routeAddress'] = $thisRecord[$thisParams['kreportgooglemapsrouteplannerwaypointaddress']];
            }
            //end
            
            if($thisParams['kreportgooglemapstitle'] != '')
                $thisPinPoint['title'] = $thisRecord[$thisParams['kreportgooglemapstitle']];
            
            //begin set color for $thisPinPoint
            $thisPinPoint['color'] = "F7819F";    //default color
            if($thisParams['kreportgooglemapscolorcriteria'] != ''){
                if(!in_array($thisRecord[$thisParams['kreportgooglemapscolorcriteria']], $storeColorCriteria)){
                    $storeColorCriteria[] = $thisRecord[$thisParams['kreportgooglemapscolorcriteria']];
                }
                if($thisParams['kreportgooglemapscolorset'] != ''){
                    $idx = array_search($thisRecord[$thisParams['kreportgooglemapscolorcriteria']], $storeColorCriteria);
//                     $thisPinPoint['color'] = str_replace('#', '', $kreportColors[$thisParams['kreportgooglemapscolorset']]['colors'][$idx]);

                    // check idx value: if higher than counter of color, run colors again and add char to pin text
                    $pinStyle = $this->getPinStyle($kreportColors[$thisParams['kreportgooglemapscolorset']]['colors'], $idx);
                    $thisPinPoint['color'] = str_replace('#', '', $kreportColors[$thisParams['kreportgooglemapscolorset']]['colors'][$pinStyle['colorIdx']]);
                    
                    //begin more options
                    $thisPinPoint['text'] = $pinStyle['text'];
                    $thisPinPoint['colorLabel'] = (empty($pinStyle['text']) ? $thisPinPoint['color'] : $thisPinPoint['color'].'_'.$pinStyle['text']);
                    //use starcolor to mark pins which have the same location (first pin found will not have the star because we are looping items)
                    if($thisParams['kreportgooglemapsspiderfy']){
                        $storedLocationsCount = array_count_values($storedLocations);
                        $thisPinPoint['starcolor'] = ( ($storedLocationsCount[$thisPinPoint['longitude'].$thisPinPoint['latitude']] > 1) ? "FFFFFF" : "");
                    }
                    //set legend item
                    $mapArray['legend']['items'][$thisPinPoint['colorLabel']]['color'] = $thisPinPoint['color'];
                    $mapArray['legend']['items'][$thisPinPoint['colorLabel']]['colorLabel'] = $pinStyle['text'];
                    $mapArray['legend']['items'][$thisPinPoint['colorLabel']]['text'] = $storeColorCriteria[$idx];
                    //end
                }
            }
            //get data for info dialog window
            $thisPinPoint['info'] = '';
            if($thisParams['kreportgooglemapsinfo'] != ''){
                $thisPinPoint['info'] = $thisRecord[$thisParams['kreportgooglemapsinfo']];
            }
            //end
            
            if(count($thisPinPoint) > 0)
                $pinpointArray[] = $thisPinPoint;
        }
        
        return array('mapaddins' => $mapArray, 'pinpoints' => $pinpointArray);
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
        }

        foreach ($chartData as $thisDimensionId => $thisData) {
            $rowArray = array();
            $rowArray[] = array('v' => $dimensions[0]['values'][$thisDimensionId]);
            foreach ($dataseries as $thisDataseries) {
                $rowArray[] = array('v' => $thisData[$thisDataseries['fieldid']]);
            }
            $googleData['rows'][] = array('c' => $rowArray);
        }

        return $googleData;
    }

    /*
     * function to wrap the code with the google visualization API options etc.
     */

    public function wrapGoogleData($googleData, $divId, $thisParams) {
        // else continue processing ..
        $googleMap = array(
            'containerId' => $divId,
            'options' => $thisParams,
            'data' => $googleData
        );

        // send back the Map as Array
        return $googleMap;
    }
    
    /**
     * set color and text for pin
     * if more colors needed that count of colors in color set, add a letter to pin strating with a
     * @param string $colorSet
     * @param string $idx
     * @param int $char (97 => a, 98 => b, ...)
     * @return multitype:string unknown
     */
    private function getPinStyle(&$colorSet, &$idx, &$char = 96){
        $privateIdx = $idx;
        $colorSetLength = count($colorSet);
        if($privateIdx >= $colorSetLength){
            $privateIdx-= $colorSetLength;
            $char++;
            return $this->getPinStyle($colorSet, $privateIdx, $char);
        }
        else{
            return array('colorIdx' => $privateIdx, 'text' => ( ($char==96) ? '' : chr($char)));
        }

    }

}


