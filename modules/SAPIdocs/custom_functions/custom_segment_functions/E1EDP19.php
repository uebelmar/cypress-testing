<?php

use SpiceCRM\data\BeanFactory;

function E1EDP19_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){
    /**
    * 002 vendor material number
    */
    switch($rawFields['QUALF']){
        case '002':
            $product = BeanFactory::getBean('Products');
            $product->retrieve_by_string_fields(['ext_id' => $rawFields['IDTNR']]);
            $bean->product_id = $product->id ?: $rawFields['IDTNR'];
            $bean->name = $rawFields['KTEXT'];
            break;
    }
    return true;
}
