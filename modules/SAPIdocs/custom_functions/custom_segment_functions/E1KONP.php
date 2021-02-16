<?php

use SpiceCRM\data\BeanFactory;

function E1KONP_in(SAPIdoc $idoc, array $segment_defintion, array $rawFields, SugarBean &$bean, &$parent = null){

    // load the scales relationship
    $bean->load_relationship('priceconditionscales');

    // remove all scales
    $bean->priceconditionscales->delete($bean->id);

    foreach($rawFields['E1KONM'] as $scale){
        $priceconditionScale = BeanFactory::getBean('PriceConditionScales');
        $priceconditionScale->pricecondition_id = $bean->id;
        $priceconditionScale->quantitiy_from = $scale['KSTBM'];
        if(substr($scale['KBETR'], -1) == '-'){
            $priceconditionScale->value = '-' . str_replace('-', '', $scale['KBETR']);
        } else {
            $priceconditionScale->value = $scale['KBETR'];
        }
        $priceconditionScale->save();
    }

    return true;
}
