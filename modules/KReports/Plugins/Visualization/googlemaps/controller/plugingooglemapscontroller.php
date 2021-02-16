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
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\SpiceACL\SpiceACL;

require_once('modules/KReports/KReport.php');

class plugingooglemapscontroller
{

    function action_getCircleDesignerPeriphericLocations($requestparams)
    {
        //\SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal($requestparams);
        if (isset($requestparams['lat']) && isset($requestparams['lng']) && isset($requestparams['distance'])) {
            $lat = floatval($requestparams['lat']);
            $lat = str_replace(',', '.', $lat);
            $lng = floatval($requestparams['lng']);
            $lng = str_replace(',', '.', $lng);
            $distance = intval($requestparams['distance']);
            // 	$distance = $distance * 0.001;
            //	$distance = str_replace(',', '.', $distance);
        } else {
            return 'no data found';
        }

        $success = false;
        $msg = "";
        $locations = array();

        //required display
        $requestparams['name_field'] = preg_replace('/\s+/', '', $requestparams['name_field']);
        $labelFields = explode(",", $requestparams['name_field']);


        if (count($labelFields) > 1) {
            $addLabels = "CONCAT_WS(' / ', " . implode(",", $labelFields) . ") AS name,";
        } else {
            $addLabels = $labelFields[0] . " AS name,";
        }
        //additional display
        $addFields = "";
        if (!empty($requestparams['display_fields'])) {
            $addFields = "CONCAT_WS(' ', " . $requestparams['display_fields'] . ") AS address,";
        }

        //get Main Bean params
        $mainBean = BeanFactory::getBean($requestparams['module_name']);
        if (!$mainBean) {
            $return = array(
                'success' => false,
                'message' => "No module_name found. Please check settings in visualization",
                'data' => $locations,
            );
        }

        switch(SpiceConfig::getInstance()->config['KReports']['authCheck']){
            case 'KAuthAccess':
                //Check KAuthAccess:
                $doKAuthCheck = false;
                if (!empty($GLOBALS['KAuthAccessController'])) {
                    $doKAuthCheck = $GLOBALS['KAuthAccessController']->orgManaged($mainBean->object_name);
                    if (!class_exists('KAuthAccessController', false)) require_once 'modules/KAuthProfiles/KAuthAccess.php';
                    $kauth = new KAuthAccessController();
                }
                break;
            case 'SpiceACL':
                $doKAuthCheck = false;
                if (!empty(SpiceACL::getInstance())) {
                    if (method_exists(SpiceACL::getInstance(), 'orgManaged')){
                        $doKAuthCheck = SpiceACL::getInstance()->orgManaged($mainBean->module_name);
                        if (!class_exists('SpiceACLController', false)) require_once 'modules/SpiceACL/SpiceACL.php';
                        $kauth = new SpiceACLController();
                    }
                }
                break;
        }


        //build query
        $sql = 'SELECT ' . $requestparams['lat_field'] . ' AS lat, ' . $requestparams['lng_field'] . ' AS lng,
                           ' . $requestparams['id_field'] . ' AS id,
                           ' . $addLabels . '
                           ' . $addFields . '
                               
            			   ( 6371 * acos( cos( radians(' . $lat . ') ) * cos( radians(' . $requestparams['lat_field'] . ' ) ) * cos( radians(' . $requestparams['lng_field'] . ' ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') ) * sin( radians(' . $requestparams['lat_field'] . ' ) ) ) ) AS distance
            		  FROM ' . $mainBean->table_name . '
            		  WHERE ' . $mainBean->table_name . '.deleted=0
            		HAVING distance < ' . $distance . '
            	      ORDER BY distance ASC';

        //organize data
        if (!$results = DBManagerFactory::getInstance()->query($sql)) {
            $success = false;
            $msg = "DB error: " . DBManagerFactory::getInstance()->last_error;
        } else {
            $success = true;
            $msg = DBManagerFactory::getInstance()->getRowCount($results) . " entries found.";
            $loop = 0;

            while ($row = DBManagerFactory::getInstance()->fetchByAssoc($results)) {
                //     $locationdistance=round($row['distance']*1000); //already in meters at that point
                $locationdistance = round($row['distance']);
                $detailview = true;

                //begin KauthAccess
                if ($doKAuthCheck) {
                    $detailview = false;
                    //PHP7 - 5.6 COMPAT
                    //ORIGINAL: $mainBean->$requestparams['id_field'] = $row['id'];
                    $requestparam_id_field = $requestparams['id_field'];
                    $mainBean->$requestparam_id_field = $row['id'];
                    //END
                    if (!$kauth->checkACLAccess($mainBean, 'list')) {
                        continue;
                    }
                    if ($kauth->checkACLAccess($mainBean, 'detail')) {
                        $detailview = true;
                    }
                }
                //end

                //organize data
                $locations[$loop]['lat'] = $row['lat'];
                $locations[$loop]['lng'] = $row['lng'];
                $locations[$loop]['id'] = $row['id'];
                $locations[$loop]['name'] = $row['name'];
                $locations[$loop]['address'] = (is_null($row['address']) ? '' : $row['address']);
                $locations[$loop]['distance'] = $locationdistance . ' km';
                $locations[$loop]['detailview'] = $detailview;

                $loop++;
            }
        }

        unset($mainBean);

        $return = array(
            'success' => $success,
            'message' => $msg,
            'data' => $locations,
            //        'sql' => $sql
        );

        return $return;
    }


}


