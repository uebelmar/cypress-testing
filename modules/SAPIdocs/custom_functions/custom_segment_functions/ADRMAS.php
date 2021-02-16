<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\Logger\LoggerManager;

function ADRMAS_in(SAPIdoc &$idoc, array $segment_defintion, array $rawFields) {

    switch($rawFields['OBJ_TYPE']){
        case 'KNA1':
            $account = BeanFactory::getBean('Accounts');
            if($account->retrieve_by_string_fields(array('ext_id' => $rawFields['OBJ_ID']))){
                if(count($rawFields['E1BPAD1VL']) > 0){

                    $account->name = $rawFields['E1BPAD1VL'][0]['NAME'];
                    $account->sap_name2 = $rawFields['E1BPAD1VL'][0]['NAME_2'];
                    $account->sap_name3 = $rawFields['E1BPAD1VL'][0]['NAME_3'];
                    $account->sap_name4 = $rawFields['E1BPAD1VL'][0]['NAME_4'];

                    $account->sap_sort1 = $rawFields['E1BPAD1VL'][0]['SORT1'];
                    $account->sap_sort2 = $rawFields['E1BPAD1VL'][0]['SORT2'];

                    $account->billing_address_city = $rawFields['E1BPAD1VL'][0]['CITY'];
                    $account->billing_address_postalcode = $rawFields['E1BPAD1VL'][0]['POSTL_COD1'];
                    $account->billing_address_country = $rawFields['E1BPAD1VL'][0]['COUNTRYISO'];
                    $account->billing_address_state = $rawFields['E1BPAD1VL'][0]['REGION'];
                    $account->billing_address_street = $rawFields['E1BPAD1VL'][0]['STREET'];
                    $account->billing_address_street_number = $rawFields['E1BPAD1VL'][0]['HOUSE_NO'];
                    $account->billing_address_street_number_suffix = $rawFields['E1BPAD1VL'][0]['STR_SUPPL1'];
                    $account->billing_address_pobox_postalcode = $rawFields['E1BPAD1VL'][0]['POSTL_COD2'];
                    $account->billing_address_pobox_number = $rawFields['E1BPAD1VL'][0]['PO_BOX'];
                    $account->billing_address_pobox_city = $rawFields['E1BPAD1VL'][0]['CITY'];
                }

                if(count($rawFields['E1BPADTEL']) > 0){
                    $account->phone_office = $rawFields['E1BPADTEL'][0]['TELNO'];
                }

                if(count($rawFields['E1BPADFAX']) > 0){
                    $account->phone_fax = $rawFields['E1BPADFAX'][0]['FAXNO'];
                }

                if(count($rawFields['E1BPADSMTP']) > 0){
                    $account->email1 = $rawFields['E1BPADSMTP'][0]['E_MAIL'];
                }

                if(count($rawFields['E1BPADURI']) > 0){
                    $account->website = $rawFields['E1BPADURI'][0]['URI'];
                }
                $account->save();
            } else {
                $idoc->status = 'error';
                $idoc->log = 'Account ' . $rawFields['OBJ_ID'] . ' not found';
                LoggerManager::getLogger()->debug('ADRMAS Account ' . $rawFields['OBJ_ID'] . ' not found');
                return false;
            }

            break;
        case 'BUS1006001':
        case 'BUS1006':
            $contact = BeanFactory::getBean('Contacts');
            if($contact->retrieve_by_string_fields(array('k_sap_parnerid' => $rawFields['OBJ_ID']))){
                if(count($rawFields['E1BPAD1VL']) > 0){

                    $contact->primary_address_city = $rawFields['E1BPAD1VL'][0]['CITY'];
                    $contact->primary_address_postalcode = $rawFields['E1BPAD1VL'][0]['POSTL_COD1'];
                    $contact->primary_address_country = $rawFields['E1BPAD1VL'][0]['COUNTRYISO'];
                    $contact->primary_address_state = $rawFields['E1BPAD1VL'][0]['COUNTRYISO'] . '_' . $rawFields['E1BPAD1VL'][0]['REGION'];
                    $contact->primary_address_street = $rawFields['E1BPAD1VL'][0]['STREET'];
                    $contact->primary_address_hsnm = $rawFields['E1BPAD1VL'][0]['HOUSE_NO'];
                    $contact->primary_address_pobox_postalcode = $rawFields['E1BPAD1VL'][0]['POSTL_COD2'];
                    $contact->primary_address_pobox_number = $rawFields['E1BPAD1VL'][0]['PO_BOX'];
                    $contact->primary_address_pobox_city = $rawFields['E1BPAD1VL'][0]['CITY'];

                    $contact->k_language = $rawFields['E1BPAD1VL'][0]['LANGU'];
                }

                if(count($rawFields['E1BPADTEL']) > 0){
                    $contact->phone_work = $rawFields['E1BPADTEL'][0]['TELNO'];
                }

                if(count($rawFields['E1BPADFAX']) > 0){
                    $contact->phone_fax = $rawFields['E1BPADFAX'][0]['FAXNO'];
                }

                if(count($rawFields['E1BPADSMTP']) > 0){
                    $contact->email1 = $rawFields['E1BPADSMTP'][0]['E_MAIL'];
                }
                $contact->save();
            } else {
                $idoc->status = 'error';
                $idoc->log = 'Contact ' . $rawFields['OBJ_ID'] . ' not found';
                LoggerManager::getLogger()->debug('ADRMAS Contact ' . $rawFields['OBJ_ID'] . ' not found');

                return false;
            }
            break;
    }

    return true;
}
