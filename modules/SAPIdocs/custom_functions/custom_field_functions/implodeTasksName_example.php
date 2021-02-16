<?php

use SpiceCRM\data\SugarBean;

/**
 * 'custom_field_function' => 'implodeTasksName_example', called after XML values have been populated wihtin the bean...
 * 
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function implodeTasksName_example(SAPIdoc $idoc, SugarBean $seed, $field_defintion, $rawFields = array()) {
    if (!empty($seed->name)) {
        if (is_array($seed->name)) {
            if ($field_defintion['value_conector']) {
                $seed->name = implode($field_defintion['value_conector'], $seed->name);
            }
        }
        return true;
    }
    return false;
}
