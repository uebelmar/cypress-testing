<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;


/**
 * replaces the currency with the internal id
 *
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */

function sapcurrency_in(SAPIdoc $idoc, SugarBean &$seed, $field_defintion, &$rawFields = array())
{

    if (!empty($rawFields[$field_defintion['sap_field']])) {
        $currency = BeanFactory::getBean('Currencies');

        $seed->{$field_defintion['mapping_field']} = $currency->retrieveIDByIso($rawFields[$field_defintion['sap_field']]);
    }

    return true;
}
