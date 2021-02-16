<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;

function DEBMAS07_out(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, $parent = null)
{


    if(empty($rawFields['SORTL'])){
        $rawFields['SORTL'] = substr($bean->name, 0, 10);
    }

    $rawFields['SPRAS'] = 'D';

    return true;
}

function DEBMAS07_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, $parent = null){

    if(isset($rawFields['E1KNVVM']) && count($rawFields['E1KNVVM']) > 0) {
        $territoryIDs = [];
        foreach ($rawFields['E1KNVVM'] as $e1knvvm) {
            $elementValues = ['vkorg' => $e1knvvm['VKORG']];
            $territory = BeanFactory::getBean('SpiceACLTerritories');
            $territoryId = $territory->getTerritoryByValues('Accounts', $elementValues);
            if (array_search($territoryId, $territoryIDs) === false) {
                $territoryIDs[] = $territoryId;
            }
        }
        if(count($territoryIDs) > 0) {
            $bean->spiceacl_primary_territory = $territoryIDs[0];
            $bean->spiceacl_secondary_territories = $territoryIDs;
        }
    }
    return true;
}
