<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

function E1KOMG_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, $bean, &$parent = null){
    $db = DBManagerFactory::getInstance();

    // determine qualifying knumh avodiing multiple entries
    $condRecord = null;
    foreach($rawFields['E1KONH'] as $tCondRecord){
        if(!$condRecord){
            $condRecord = $tCondRecord;
        } else if($tCondRecord['KNUMH'] > $condRecord['KNUMH']) {
            $condRecord = $tCondRecord;
        }
    }

    $condBean = BeanFactory::getBean('PriceConditions');
    if(!$condBean->retrieve_by_string_fields(['ext_id' => $condRecord['KNUMH']])) {
        // if we have a delete flag return and do not create the record
        if($condRecord['E1KONP'][0]['LOEVM_KO'] == 'X'){
            return;
        }

        $condBean->id = create_guid();
        $condBean->new_with_id = true;
        $condBean->ext_id = $condRecord['KNUMH'];
    } else if($condRecord['E1KONP'][0]['LOEVM_KO'] == 'X'){
        // delete the record
        $condBean->mark_deleted($condBean->id);
        return;
    }


    $condBean->pricecondition_key = $rawFields['VAKEY'];

    // determine the type
    $type = $db->fetchByAssoc($db->query("SELECT id FROM syspriceconditiontypes WHERE ext_id='{$rawFields['KSCHL']}'"));
    $condBean->priceconditiontype_id = $type ? $type['id'] : $rawFields['KSCHL'];

    // run the elements
    $determinationId = $db->fetchByAssoc($db->query("SELECT id FROM syspricedeterminations WHERE ext_id='{$rawFields['KOTABNR']}'"));
    if($determinationId){
        $condBean->priceconditiontypedetermination_id = $determinationId['id'];

        // process element values
        $condBean->deleteElementValues();
        $detDetails = $condBean->getDeterminationElementsById($determinationId['id']);

        $offset = 0;
        foreach($detDetails['elements'] as $element){
            $priceconditonEv = BeanFactory::getBean('PriceConditionElementValues');
            $priceconditonEv->pricecondition_id = $condBean->id;
            $priceconditonEv->element_id = $element['id'];

            // determine the value from the VAKEY as custom values are not populated to the IDOC
            $priceconditonEv->element_value = rtrim(substr($rawFields['VAKEY'], $offset, $element['element_length']));
            $offset += (int) $element['element_length'];

            $priceconditonEv->save();
        }
    } else {
        $condBean->priceconditiontypedetermination_id = $rawFields['KOTABNR'];
    }

    $valid_from = date_create_from_format('Ymd', $condRecord['DATAB']);
    if ($valid_from) {
        $condBean->valid_from = $valid_from->format($GLOBALS['timedate']->get_db_date_format());
    }
    $valid_to = date_create_from_format('Ymd', $condRecord['DATBI']);
    if ($valid_to) {
        $condBean->valid_to = $valid_to->format($GLOBALS['timedate']->get_db_date_format());
    }

    if(substr($condRecord['E1KONP'][0]['KBETR'], -1) == '-'){
        $condBean->amount = '-' . str_replace('-', '', $condRecord['E1KONP'][0]['KBETR']);
    } else {
        $condBean->amount = $condRecord['E1KONP'][0]['KBETR'];
    }

    $condBean->amount = str_replace('.', ',', $condBean->amount);

    $condBean->save();

    return true;
}
