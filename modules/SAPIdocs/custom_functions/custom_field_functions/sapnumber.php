<?php


/**
 * replaces the kotabnr with the internal id
 *
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */

function sapnumber_in(SAPIdoc $idoc, SugarBean &$seed, $field_defintion, &$rawFields = array())
{

    if (!empty($rawFields[$field_defintion['sap_field']])) {
        if(substr($rawFields[$field_defintion['sap_field']], -1) == '-'){
            $seed->{$field_defintion['mapping_field']} = '-' . str_replace('-', '', $rawFields[$field_defintion['sap_field']]);
        } else {
            $seed->{$field_defintion['mapping_field']} = $rawFields[$field_defintion['sap_field']];
        }

        $seed->{$field_defintion['mapping_field']} = str_replace('.', ',', $seed->{$field_defintion['mapping_field']});
    }

    return true;
}
