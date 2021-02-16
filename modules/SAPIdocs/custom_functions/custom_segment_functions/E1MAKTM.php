<?php

use SpiceCRM\includes\authentication\AuthenticationController;

/**
 * process the Material Short Text segment for SAP
 *
 * @param SAPIdoc $idoc
 * @param array $segment_defintion
 * @param array $rawFields
 * @param SugarBean $bean
 * @param null $parent
 * @return bool
 */
function E1MAKTM_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){
    $current_user = AuthenticationController::getInstance()->getCurrentUser();

    // \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('MAKTN '  . print_r($rawFields, true) .  ' ' . print_r($parent, true));
    $sapLanguages = array(
        'D' => 'de_DE',
        'E' => 'en_us',
    );

    if (isset($sapLanguages[$rawFields['SPRAS']])) {
        $bean->language = $sapLanguages[$rawFields['SPRAS']];
        $bean->text_language = (empty($rawFields['SPRAS']) ? 'zz_ZZ' : $sapLanguages[$rawFields['SPRAS']]);
        $bean->parent_id = $parent['record']; //added maretval 2018-07-13
        $bean->parent_type = 'Products';
        $bean->text_id = "sap-producttext"; //value defined in syscustomtextids
        $bean->assigned_user_id = $current_user->id;

        // set the material short text for the bean
        if ($parent['bean'] && $rawFields['SPRAS'] == 'E') {
            $parent['bean']->name = $rawFields['MAKTX'];
            $parent['bean']->save();
        }

        return true;
    } else {
        return false;
    }
}
