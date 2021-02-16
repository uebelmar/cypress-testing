<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

function E1EDP01_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){
    $db = DBManagerFactory::getInstance();

    foreach($rawFields['E1EDP19'] as $e1edp19){
        switch($e1edp19['QUALF']){
            case '002':
                $product = BeanFactory::getBean('Products');
                $product->retrieve_by_string_fields(['ext_id' => $e1edp19['IDTNR']]);
                $bean->product_id = $product->id ?: $e1edp19['IDTNR'];
                $bean->name = $e1edp19['KTEXT'];
                break;
        }
    }

    // remove all conditions
    // $bean->load_relationship('salesdocitemconditions');
    // $bean->salesdocitemconditions->delete($bean->id);

    $db->query("DELETE FROM salesdocitemconditions WHERE salesdocitem_id = '{$bean->id}'");

    $i = 0;
    foreach($rawFields['E1EDP05'] as $e1edp05){
        $condition = BeanFactory::getBean('SalesDocItemConditions');
        $condition->salesdocitem_id = $bean->id;
        $condition->itemnr = $i;
        $condition->name = $e1edp05['KOTXT'];
        if(!empty($e1edp05['KSCHL'])) {
            $pconditiontype = $db->fetchByAssoc($db->query("SELECT id FROM syspriceconditiontypes WHERE ext_id='{$e1edp05['KSCHL']}'"));
            $condition->priceconditiontype_id = $pconditiontype['id'];
        }
        $condition->amount = str_replace('.', ',', $e1edp05['KRATE']);
        $condition->amount_total = str_replace('.', ',', $e1edp05['BETRG']);
        $condition->percentage = str_replace('.', ',', $e1edp05['KPERC']);
        $condition->save(false, false);
        $i++;
    }

    return true;
}
