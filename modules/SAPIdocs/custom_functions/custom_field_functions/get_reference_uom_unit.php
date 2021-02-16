<?php
/**
 * Created by PhpStorm.
 * User: maretval
 * Date: 29.08.2019
 * Time: 21:16
 */

use SpiceCRM\data\SugarBean;

/**
 * @param SAPIdoc $idoc
 * @param SugarBean $seed
 * @param type $field_defintion the current record of the defined sapidocfield
 * @param type $rawFields the whole raw XML segment, if given
 * @return boolean
 */
function get_reference_uom_unit_in(SAPIdoc $idoc, SugarBean &$seed, $field_defintion, &$rawFields = array(), $parent)
{
    $seed->{$field_defintion['mapping_field']} = $parent['bean']->base_uom_id;

    return true;
}
