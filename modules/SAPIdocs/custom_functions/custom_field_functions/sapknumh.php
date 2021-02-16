<?php


/**
 * extracts the external id from the subsegment E1KONH
 *
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function sapknumh_map($rawFields = array())
{
    return substr($rawFields['E1KONH'], 0, 10);
}
