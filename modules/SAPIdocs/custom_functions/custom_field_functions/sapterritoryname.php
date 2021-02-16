<?php

use SpiceCRM\data\BeanFactory;

/**
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function sapterritoryname_in(SAPIdoc $idoc, SugarBean $seed, $field_defintion, &$rawFields = array())
{
    $elementValues = ['vkorg' => $rawFields['VKORG'], 'vtweg' => $rawFields['VTWEG'], 'spart' => $rawFields['SPART']];
    $territory = BeanFactory::getBean('SpiceACLTerritories');
    $territory->getTerritoryByValues('AccountCCDetails', $elementValues);
    $seed->{$field_defintion['mapping_field']} = $territory->name;

    return true;
}
