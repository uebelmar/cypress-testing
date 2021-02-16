<?php


/**
 * translates the X from teh LOEVM to a boolean value 0 or 1
 *
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function saploevm_in(SAPIdoc $idoc, SugarBean &$seed, $field_defintion, &$rawFields = array())
{
    $seed->{$field_defintion['mapping_field']} = $rawFields[$field_defintion['sap_field']] == 'X' ? 1 : 0;
    return true;
}
