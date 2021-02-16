<?php

use SpiceCRM\data\BeanFactory;

/**
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function sapterritoryid_in(SAPIdoc $idoc, SugarBean &$seed, $field_defintion, &$rawFields = array())
{
    $elementValues = ['vkorg' => $rawFields['VKORG'], 'vtweg' => $rawFields['VTWEG'], 'spart' => $rawFields['SPART']];
    $territory = BeanFactory::getBean('SpiceACLTerritories');
    $seed->{$field_defintion['mapping_field']} = $territory->getTerritoryByValues('AccountCCDetails', $elementValues);

    return true;
}

/**
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function sapterritoryid_map($rawFields = array())
{
    $elementValues = ['vkorg' => $rawFields['VKORG'], 'vtweg' => $rawFields['VTWEG'], 'spart' => $rawFields['SPART']];
    $territory = BeanFactory::getBean('SpiceACLTerritories');
    return $territory->getTerritoryByValues('AccountCCDetails', $elementValues);
}
