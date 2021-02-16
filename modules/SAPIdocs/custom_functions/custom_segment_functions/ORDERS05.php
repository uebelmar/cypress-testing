<?php

use SpiceCRM\data\SugarBean;

function ORDERS05_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null) {

    global $timedate;

    $bean->salesdocdate = $timedate->nowDb();

    /*
    $territoryIDs = [];
    foreach($rawFields['E1KNVVM'] as $e1knvvm){
        $elementValues = ['vkorg' => $e1knvvm['VKORG'], 'vtweg' => $e1knvvm['VTWEG'], 'spart' => $e1knvvm['SPART']];
        $territory = \SpiceCRM\data\BeanFactory::getBean('SpiceACLTerritories');
        $territoryIDs[] = $territory->getTerritoryByValues('AccountCCDetails', $elementValues);
    }
    $bean->spiceacl_primary_territory = $territoryIDs[0];
    $bean->spiceacl_secondary_territories = $territoryIDs;
    */

    /*
    $territoryIDs = [];
    foreach($rawFields['E1KNVVM'] as $e1knvvm){
        $elementValues = ['vkorg' => $e1knvvm['VKORG'], 'vtweg' => $e1knvvm['VTWEG'], 'spart' => $e1knvvm['SPART']];
        $territory = \SpiceCRM\data\BeanFactory::getBean('SpiceACLTerritories');
        $territoryIDs[] = $territory->getTerritoryByValues('AccountCCDetails', $elementValues);
    }
    $bean->spiceacl_primary_territory = $territoryIDs[0];
    $bean->spiceacl_secondary_territories = $territoryIDs;
    */

    return true;

}
