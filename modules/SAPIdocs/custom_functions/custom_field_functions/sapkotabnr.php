<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;


/**
 * replaces the kotabnr with the internal id
 *
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */

function sapkotabnr_in(SAPIdoc $idoc, SugarBean &$seed, $field_defintion, &$rawFields = array())
{
    $db = DBManagerFactory::getInstance();

    $determinationId = $db->fetchByAssoc($db->query("SELECT id FROM syspricedeterminations WHERE ext_id='{$rawFields['KOTABNR']}'"));
    if($determinationId){
        $seed->{$field_defintion['mapping_field']} = $determinationId['id'];

        // process element values
        $seed->deleteElementValues();
        $detDetails = $seed->getDeterminationElementsById($determinationId['id']);

        $offset = 0;
        foreach($detDetails['elements'] as $element){
            $priceconditonEv = BeanFactory::getBean('PriceConditionElementValues');
            $priceconditonEv->pricecondition_id = $seed->id;
            $priceconditonEv->element_id = $element['id'];

            // determine the value from the VAKEY as custom values are not populated to the IDOC
            $priceconditonEv->element_value = rtrim(substr($rawFields['VAKEY'], $offset, $element['element_length']));
            $offset += (int) $element['element_length'];

            $priceconditonEv->save();
        }
    } else {
        $seed->{$field_defintion['mapping_field']} = $rawFields['KOTABNR'];
    }

    return true;
}
