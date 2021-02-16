<?php

/**
 * process the PartnerRoles on the document header
 *
 * @param SAPIdoc $idoc
 * @param array $segment_defintion
 * @param array $rawFields
 * @param SugarBean $bean
 * @param null $parent
 * @return
 */
function E1KNA1H_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null)
{
    $bean->description = '';
    foreach($rawFields['E1KNA1L'] as $textline){
        $bean->description .= $textline['TDLINE']."\r\n";
    }

    return true;
}
