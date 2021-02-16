<?php

use SpiceCRM\includes\database\DBManagerFactory;


/**
 * extracts the external id from the subsegment E1KONH
 *
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function sapkschl_in(SAPIdoc $idoc, SugarBean &$seed, $field_defintion, &$rawFields = array())
{
    $db = DBManagerFactory::getInstance();

    if (!empty($rawFields[$field_defintion['sap_field']])) {
        $type = $db->fetchByAssoc($db->query("SELECT id FROM syspriceconditiontypes WHERE ext_id='{$rawFields[$field_defintion['sap_field']]}'"));
        $seed->{$field_defintion['mapping_field']} = $type ? $type['id'] : $rawFields[$field_defintion['sap_field']];
    }

    return true;
}
