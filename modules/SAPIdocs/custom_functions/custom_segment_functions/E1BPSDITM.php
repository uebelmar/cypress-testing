<?php

use SpiceCRM\data\BeanFactory;

function E1BPSDITM_out(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, $parent = null)
{

    if($bean->kproduct_id){
        $product = BeanFactory::getBean('KProducts', $bean->kproduct_id);
        $rawFields['MATERIAL'] = $product->sap_materialid;
        $rawFields['ITEM_CATEG'] = 'AFN';
    }

    return true;
}
