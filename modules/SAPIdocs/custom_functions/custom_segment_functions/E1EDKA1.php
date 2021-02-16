<?php

use SpiceCRM\data\BeanFactory;

/**
 * process the PartnerRoles on the document header
 *
 * @param SAPIdoc $idoc
 * @param array $segment_defintion
 * @param array $rawFields
 * @param SugarBean $bean
 * @param null $parent
 * @return
 */
function E1EDKA1_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null)
{
    $account = BeanFactory::getBean('Accounts');

    switch ($rawFields['PARVW']) {
        case 'AG':
            if ($account->retrieve_by_string_fields(['ext_id' => $rawFields['PARTN']])) {
                $bean->account_op_id = $account->id;
            }

            $bean->billing_address_name = trim($rawFields['NAME1'] . $rawFields['NAME2']);
            $bean->billing_address_phone = $rawFields['TELF1'];
            $bean->billing_address_street = $rawFields['STRAS'];
            $bean->billing_address_city = $rawFields['ORT01'];
            $bean->billing_address_postalcode = $rawFields['PSTLZ'];
            $bean->billing_address_country = $rawFields['LAND1'];
            $bean->billing_address_state = $rawFields['REGIO'];

            break;
        case 'WE':
            if ($account->retrieve_by_string_fields(['ext_id' => $rawFields['PARTN']])) {
                $bean->account_rp_id = $account->id;
            }

            $bean->shipping_address_name = trim($rawFields['NAME1'] . $rawFields['NAME2']);
            $bean->shipping_address_phone = $rawFields['TELF1'];
            $bean->shipping_address_street = $rawFields['STRAS'];
            $bean->shipping_address_city = $rawFields['ORT01'];
            $bean->shipping_address_postalcode = $rawFields['PSTLZ'];
            $bean->shipping_address_country = $rawFields['LAND1'];
            $bean->shipping_address_state = $rawFields['REGIO'];

            break;
        case 'RE':
            $bean->account_ir_id = $account->id;
            break;
        case 'RG':
            $bean->account_pp_id = $account->id;
            break;
    }

    return true;
}
