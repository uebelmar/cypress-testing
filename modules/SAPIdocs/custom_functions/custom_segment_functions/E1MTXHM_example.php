<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

/**
 *
 * @global type $db
 * @global type $timedate
 * @param SAPIdoc $idoc
 * @param array $segment_defintion
 * @param array $rawFields
 * @return boolean
 */
function E1MTXHM_example_in(SAPIdoc $idoc, array $segment_defintion, array $rawFields)
{
    global $timedate;
$db = DBManagerFactory::getInstance();

    $module = "Accounts";
    // example query, will only work, if $timedate uses cached date value for whole request!
    $sql = "SELECT bean_id FROM sapidocinboundrecords "
        . "WHERE bean_type = '" . $module . "' "
        . "AND sapidoc_id = '" . $idoc->id . "' "
        . "AND deleted = 0 "
        . "AND handled = '" . $timedate->nowDb() . "' ";
    $result = $db->query($sql);
    while ($row = $db->fetchByAssoc($result)) {
        $seed = BeanFactory::getBean($module, $row['bean_id']);
        if (!empty($seed)) {
            $value = "";
            // not mapped to any real bean
            foreach ($rawFields['E1MTXLM'] as $item) {
                $value .= $item['TDLINE'];
            }
            $seed->name = $value;
            $seed->save(false);
        }
    }

    /**
     * Array
     * (
     * [MSGFN] => 005
     * [TDOBJECT] => MATERIAL
     * [TDNAME] => 000000000000057799
     * [TDID] => BEST
     * [TDSPRAS] => D
     * [TDTEXTTYPE] => ASCII
     * [SPRAS_ISO] => DE
     * [E1MTXLM] => Array
     * (
     * [0] => Array
     * (
     * [MSGFN] => 005
     * [TDFORMAT] => *
     * [TDLINE] => Transportschutzhaube aus FleeceGuard G
     * )
     *
     * [1] => Array
     * (
     * [MSGFN] => 005
     * [TDFORMAT] => /
     * [TDLINE] => für Autoscheiben
     * )
     *
     * [2] => Array
     * (
     * [MSGFN] => 005
     * [TDFORMAT] => /
     * [TDLINE] => 1850 x 1100 mm
     * )
     *
     * )
     * )
     */
    return true;
}

function E1MTXHM_example_out(SAPIdoc $idoc, array $segment_defintion, array $rawFields)
{
    global $timedate;
$db = DBManagerFactory::getInstance();

    $module = "Accounts";
    return true;
}
