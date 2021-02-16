<?php

use SpiceCRM\data\SugarBean;

/**
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function sapdate_out(SAPIdoc $idoc, SugarBean $seed, $field_defintion, &$rawFields = array())
{
    if (!empty($seed->{$field_defintion['mapping_field']})) {
        $date = date_create_from_format($GLOBALS['timedate']->get_db_date_format(), $seed->{$field_defintion['mapping_field']});
        if ($date)
            $rawFields[$field_defintion['sap_field']] = $date->format('Ymd');
        else{
            $date = date_create_from_format($GLOBALS['timedate']->get_date_format(), $seed->{$field_defintion['mapping_field']});
            if ($date)
                $rawFields[$field_defintion['sap_field']] = $date->format('Ymd');
        }
    }
    return true;
}

function sapdate_in(SAPIdoc $idoc, SugarBean $seed, $field_defintion, &$rawFields = array())
{

    if (!empty($rawFields[$field_defintion['sap_field']])) {
        $date = date_create_from_format('Ymd', $rawFields[$field_defintion['sap_field']]);
        if ($date) {
            $seed->{$field_defintion['mapping_field']} = $date->format($GLOBALS['timedate']->get_db_date_format());
        }
    }

    return true;
}
