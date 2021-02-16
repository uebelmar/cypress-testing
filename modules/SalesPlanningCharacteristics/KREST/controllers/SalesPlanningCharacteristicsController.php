<?php

namespace SpiceCRM\modules\SalesPlanningCharacteristics\KREST\controllers;

use SpiceCRM\includes\database\DBManagerFactory;

class SalesPlanningCharacteristicsController {


    /**
     * @param $req
     * @param $res
     * @param $args
     * @return $returnArray
     */
    public function getCharacteristicValues($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $returnArray = array();

        $returnArray[] = array(
            'charvalid' => '',
            'charvalue' => ''
        );

        $characteristicId = $db->quote($args['characteristicId']);
        //get territory
        if ($characteristicId == 'territory') {
            $charValueObj = $db->query("SELECT id, name FROM salesplanningterritories WHERE deleted='0'");
            while ($charvalue_record = $db->fetchByAssoc($charValueObj)) {
                $returnArray[] = array(
                    'charvalid' => $charvalue_record['id'],
                    'charvalue' => $charvalue_record['name'],
                );
            }
        //get characteristic values
        } else {
            $charValueObj = $db->query("SELECT id, cvkey FROM salesplanningcharacteristicvalues WHERE deleted='0' AND salesplanningcharacteristic_id='" . $characteristicId . "'");
            while ($charvalue_record = $db->fetchByAssoc($charValueObj)) {
                $returnArray[] = array(
                    'charvalid' => $charvalue_record['id'],
                    'charvalue' => $charvalue_record['cvkey'],
                );
            }
        }
        return $res->withJson($returnArray);
    }
}
