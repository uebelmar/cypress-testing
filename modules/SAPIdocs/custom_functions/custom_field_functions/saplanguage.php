<?php


function saplanguage_in(SAPIdoc $idoc, SugarBean $seed, $field_defintion, &$rawFields = array())
{

    if (!empty($rawFields[$field_defintion['sap_field']])) {
        switch($rawFields[$field_defintion['sap_field']]) {
            case 'DE':
                $seed->{$field_defintion['mapping_field']} = 'de_DE';
                break;
            case 'EN':
                $seed->{$field_defintion['mapping_field']} = 'en_us';
                break;
        }
    }

    return true;
}
