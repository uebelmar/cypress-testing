<?php

use SpiceCRM\data\BeanFactory;

/**
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function sapuom_in(SAPIdoc $idoc, SugarBean $seed, $field_defintion, &$rawFields = array())
{
    if(!empty($rawFields[$field_defintion['sap_field']])) {
        $uomunit = BeanFactory::getBean('UOMUnits');
        $uomunit->retrieve_by_string_fields(['iso' => $rawFields[$field_defintion['sap_field']]]);
        if ($uomunit) {
            $seed->{$field_defintion['mapping_field']} = $uomunit->id;
        }
    }

    return true;
}
