<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SpiceNumberRanges\SpiceNumberRanges;

/***** SPICE-HEADER-SPACEHOLDER *****/
class HooksForSAPIdoc
{

    public function processAccounts(&$bean, $event, $arguments)
    {
        if ($GLOBALS['idocprocessing']) return true;

        $db = DBManagerFactory::getInstance();

        if ($bean->trStopHookLogic)
            return;

        // not vendors
        if ((!empty($bean->account_type) && preg_match("/vendor/", $bean->account_type)) ||
            !empty($bean->tr_sap_creditorid)    ) {
            return;
        }

        // not dill customer
//exclusion of 1065 removed 2017-11-01
//        if (!empty($bean->dill_customerid))
//            return;

        // not leads
        if (!empty($bean->account_type) && strpos($bean->account_type, 'lead') !== false)
            return;

        //not when vkorg 1065
//exclusion of 1065 removed 2017-11-01
//        if(!class_exists('TRKOrgObjectsUtils', false)) require_once 'modules/KOrgObjects/TRKOrgObjectsUtils.php';
//        $orgunits = TRKOrgObjectsUtils::getInstance()->kppGetVkorgsForBean($bean->id, $bean->module_name);
//        $found = array_search('1065', array_column($orgunits, 'vkorg'));
//        if($found !== false){
//            return;
//        }

        $AdrMasFields = [
            'name',
            'k_name2',
            'k_name3',
            'k_name4',
            'billing_address_city',
            'billing_address_postalcode',
            'billing_address_country',
            'billing_address_state',
            'billing_address_street',
            'billing_address_hsnm',
            'billing_address_pobox_postalcode',
            'billing_address_pobox_number',
            'billing_address_pobox_city',
            'k_transportzone',
            'phone_office',
            'phone_fax',
            'email1',
            'website',
        ];

        if (empty($bean->k_sap_customerid)) {
            $bean->k_sap_customerid = str_pad(SpiceNumberRanges::getNextNumber('F0567673-87F8-444C-B8BA-C9C5176B26AA'), 10, '0', STR_PAD_LEFT);
            $bean->db->query("UPDATE accounts SET k_sap_customerid = '$bean->k_sap_customerid' WHERE id='$bean->id'");
            $GLOBALS['sapnewid'][] = $bean->id;
        }

        // send the DEBMAS
        $this->process($bean, "after", "new_update");


        $changedFields = [];
        $sendADRMAS = false;
        //maretval 2018-05-28: grab new property auditDataChanges
//        $auditDataChanges = array_keys($bean->auditDataChanges);
//
//        // get changed fields
//        foreach ($bean->field_name_map as $field) {
//            //BEGIN maretval 2018-05-28
////            if ($bean->{$field['name']} != $bean->fetched_row[$field['name']]) {
//            if ($bean->{$field['name']} != $bean->fetched_row[$field['name']] || in_array($field['name'], $auditDataChanges)) {
//                //END
//                $changedFields[] = $field['name'];
//                if (array_search($field['name'], $AdrMasFields))
//                    $sendADRMAS = true;
//            }
//        }

        //force ADRMAS
        $sendADRMAS = true;
        if ($sendADRMAS && !empty($bean->k_sap_customerid)) {
            $segmentRecord = $db->fetchByAssoc($db->query("SELECT id FROM sapidocsegmentrelations WHERE mestyp = 'ADRMAS' AND deleted = 0"));
            if ($segmentRecord) {

                $regioSplit = explode('_', $bean->billing_address_state);

                $phone_office = $this->extractPhoneDataForSap($bean->phone_office);
                $phone_mobile = $this->extractPhoneDataForSap($bean->phone_mobile);
                $phone_fax = $this->extractPhoneDataForSap($bean->phone_fax);

                $idoc = array(
                    'E1ADRMAS' => array(
                        '@attributes' => array('SEGMENT' => '1'),
                        'OBJ_TYPE' => 'KNA1',
                        'OBJ_ID' => $bean->k_sap_customerid,
                        'CONTEXT' => '0001',
                        'E1BPAD1VL' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'FROM_DATE' => '00010101',
                            'TO_DATE' => '99991231',
                            'TITLE' => '0003',
                            'NAME' => $bean->name,
                            'NAME_2' => $bean->k_name2,
                            'NAME_3' => $bean->k_name3,
                            'NAME_4' => $bean->k_name4,
                            'CITY' => $bean->billing_address_city,
                            'SORT1' => $bean->k_searchkey,
                            'LANGU' => $bean->k_language,
                            'POSTL_COD1' => $bean->billing_address_postalcode,
                            'TRANSPZONE' => $bean->k_transportzone,
                            'STREET' => $bean->billing_address_street,
                            'HOUSE_NO' => $bean->billing_address_hsnm,
                            'COUNTRY' => $bean->billing_address_country,
                            'COUNTRYISO' => $bean->billing_address_country,
                            'REGION' => $regioSplit[1],
                            'E1BPAD1VL1' => array(
                                '@attributes' => array('SEGMENT' => '1'),
                                'LANGU_CR' => 'E',
                                'LANGUCRISO' => 'EN',
                                'ADDR_GROUP' => 'BP'
                            )
                        ),
//BEGIN maretval 2018-07-06 fix ticket KPP-4
//                        'E1BPADTEL' => array(
//                            '@attributes' => array('SEGMENT' => '1'),
//                            'TELEPHONE' => $bean->phone_office, //modified TEL_NO to TELEPHONE 20180423
//                        ),
                        'E1BPADTEL' => array(
                            array(
                                '@attributes' => array('SEGMENT' => '1'),
                                'COUNTRY' => $bean->billing_address_country,
                                'TELEPHONE' => $phone_office['number'], //modified TEL_NO to TELEPHONE 20180423
                                'EXTENSION' => $phone_office['extension'],
                                'R_3_USER' => 1
                            ),
                            array(
                                '@attributes' => array('SEGMENT' => '1'),
                                'COUNTRY' => $bean->billing_address_country,
                                'TELEPHONE' =>  $phone_mobile['number'],
                                'EXTENSION' => $phone_mobile['extension'],
                                'R_3_USER' => 3
                            ),
                        ),
//END
                        'E1BPADFAX' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'FAX' => $phone_fax['number'], //modified TEL_NO to FAX 20180529
                            'EXTENSION' => $phone_fax['extension'],
                        ),
                        'E1BPADSMTP' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'E_MAIL' => $bean->email1,
                            'EMAIL_SRCH' => strtoupper($bean->email1)
                        ),
                        'E1BPADURI' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'URI_TYPE' => 'HPG',
                            'URI' => $bean->website
                        )
                    )
                );

                SAPIdoc::rawXMLExport($bean, $segmentRecord['id'], $idoc);
            }
        }

        return true;
    }

    /** OLD WAY  */
    public function processContacts(&$bean, $event, $arguments)
    {
        if ($GLOBALS['idocprocessing']) return true;

        //not when vkorg 1065
        if(!class_exists('TRKOrgObjectsUtils', false)) require_once 'modules/KOrgObjects/TRKOrgObjectsUtils.php';
        $orgunits = TRKOrgObjectsUtils::getInstance()->kppGetVkorgsForBean($bean->id, $bean->module_name);//todo-uebelmar class does not exist
        $found = array_search('1065', array_column($orgunits, 'vkorg'));
        if($found !== false){
            return;
        }

        $db = DBManagerFactory::getInstance();

        if(empty($bean->account_id)){
            return;
        }

        $account = BeanFactory::getBean('Accounts', $bean->account_id);
        if(empty($account->k_sap_customerid)){
            return;
        }
        $AdrMasFields = [
            'primary_address_postalcode',
            'primary_address_country',
            'primary_address_state',
            'primary_address_street',
            'primary_address_hsnm',
            'primary_address_pobox_postalcode',
            'primary_address_pobox_number',
            'primary_address_pobox_city',
            'phone_work',
            'phone_fax',
            'email1',
            'k_language'
        ];

        if (empty($bean->k_sap_parnerid)) {
            $bean->k_sap_parnerid = str_pad(SpiceNumberRanges::getNextNumber('0FD78201-9447-49CF-A04D-831C4E287DCF'), 10, '0', STR_PAD_LEFT);
            $bean->db->query("UPDATE contacts SET k_sap_parnerid = '$bean->k_sap_parnerid' WHERE id='$bean->id'");
            $GLOBALS['sapnewid'][] = $bean->id;
        }

        // send the DEBMAS
        $this->process($bean, "after", "new_update");

        $changedFields = [];
        $sendADRMAS = false;
        // get changed fields
        foreach ($bean->field_name_map as $field) {
            if ($bean->{$field['name']} != $bean->fetched_row[$field['name']]) {
                $changedFields[] = $field['name'];
                if (array_search($field['name'], $AdrMasFields))
                    $sendADRMAS = true;
            }
        }

        if ($sendADRMAS && !empty($bean->k_sap_parnerid)) {
            $segmentRecord = $db->fetchByAssoc($db->query("SELECT id FROM sapidocsegmentrelations WHERE mestyp = 'ADRMAS' AND deleted = 0"));
            if ($segmentRecord) {

                $regioSplit = explode($bean->primary_address_state);

                $idoc = array(
                    'E1ADRMAS' => array(
                        '@attributes' => array('SEGMENT' => '1'),
                        'OBJ_TYPE' => 'BUS1006',
                        'OBJ_ID' => $bean->k_sap_parnerid,
                        'CONTEXT' => '0001',
                        'E1BPAD1VL' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'FROM_DATE' => '00010101',
                            'TO_DATE' => '99991231',
                            'CITY' => $bean->primary_address_city,
                            'POSTL_COD1' => $bean->primary_address_postalcode,
                            'STREET' => $bean->primary_address_street,
                            'HOUSE_NO' => $bean->primary_address_hsnm,
                            'COUNTRY' => $bean->primary_address_country,
                            'COUNTRYISO' => $bean->primary_address_country,
                            'REGION' => $regioSplit[1],
                            'LANGU' => $bean->k_language,
                            'E1BPAD1VL1' => array(
                                '@attributes' => array('SEGMENT' => '1'),
                                'LANGU_CR' => 'E',
                                'LANGUCRISO' => 'EN',
                                'ADDR_GROUP' => 'BP'
                            )
                        ),
                        'E1BPADTEL' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'TEL_NO' => $bean->phone_work
                        ),
                        'E1BPADFAX' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'TEL_NO' => $bean->phone_fax
                        ),
                        'E1BPADSMTP' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'E_MAIL' => $bean->email1,
                            'EMAIL_SRCH' => strtoupper($bean->email1)
                        )
                    )
                );

                SAPIdoc::rawXMLExport($bean, $segmentRecord['id'], $idoc);
            }
        }


        return true;
    }

    /** NEW WAY NOT ACTIVE YET*/
    public function processContactsNEW(&$bean, $event, $arguments)
    {
        if ($GLOBALS['idocprocessing']) return true;

        //not when vkorg 1065
//        if(!class_exists('TRKOrgObjectsUtils', false)) require_once 'modules/KOrgObjects/TRKOrgObjectsUtils.php';
//        $orgunits = TRKOrgObjectsUtils::getInstance()->kppGetVkorgsForBean($bean->id, $bean->module_name);
//        $found = array_search('1065', array_column($orgunits, 'vkorg'));
//        if($found !== false){
//            return;
//        }

        $db = DBManagerFactory::getInstance();

        if(empty($bean->account_id)){
            return;
        }

        $account = BeanFactory::getBean('Accounts', $bean->account_id);
        if(empty($account->k_sap_customerid) && empty($account->tr_sap_creditorid)){
            return;
        }


//        $AdrMasFields = [
//            'primary_address_postalcode',
//            'primary_address_country',
//            'primary_address_state',
//            'primary_address_street',
//            'primary_address_hsnm',
//            'primary_address_pobox_postalcode',
//            'primary_address_pobox_number',
//            'primary_address_pobox_city',
//            'phone_work',
//            'phone_fax',
//            'email1',
//            'k_language',
//            'first_name',
//            'last_name',
//            'salutation'
//        ];

        if (empty($bean->k_sap_parnerid)) {
            $bean->k_sap_parnerid = str_pad(SpiceNumberRanges::getNextNumber('0FD78201-9447-49CF-A04D-831C4E287DCF'), 10, '0', STR_PAD_LEFT);
            $bean->db->query("UPDATE contacts SET k_sap_parnerid = '$bean->k_sap_parnerid' WHERE id='$bean->id'");
            $GLOBALS['sapnewid'][] = $bean->id;
        }

        // send the DEBMAS
        $this->process($bean, "after", "new_update");

//        $changedFields = [];
//        $sendADRMAS = false;
//        // get changed fields
//        foreach ($bean->field_name_map as $field) {
//            if ($bean->{$field['name']} != $bean->fetched_row[$field['name']]) {
//                $changedFields[] = $field['name'];
//                if (array_search($field['name'], $AdrMasFields)) {
//                    $sendADRMAS = true;
//                }
//            }
//        }

        //force ADR2MAS
        $sendADRMAS = true;
        if ($sendADRMAS && !empty($bean->k_sap_parnerid)) {
            $segmentRecord = $db->fetchByAssoc($db->query("SELECT id FROM sapidocsegmentrelations WHERE mestyp = 'ADR2MAS' AND deleted = 0"));
            if ($segmentRecord) {

                $regioSplit = explode('_', $bean->primary_address_state);

//                //common data segment contents
//                $E1BPADxVL = array(
//                    '@attributes' => array('SEGMENT' => '1'),
//                    'FROM_DATE' => '00010101',
//                    'TO_DATE' => '99991231',
//                    'CITY' => $bean->primary_address_city,
//                    'POSTL_COD1' => $bean->primary_address_postalcode,
//                    'STREET' => $bean->primary_address_street,
//                    'HOUSE_NO' => $bean->primary_address_hsnm,
//                    'LANGU_CR_P' => $bean->k_language,
//                    'FIRSTNAME' => $bean->first_name,
//                    'LASTNAME' => $bean->last_name,
//                    'TITLE_P' => $bean->salutation,
//
//                );
//                $E1BPADxVL1 = array(
//                    '@attributes' => array('SEGMENT' => '1'),
//                    'LANGU_CR' => 'E',
//                    'LANGUCRISO' => 'EN',
//                    'ADDR_GROUP' => 'BP',
//                    'COUNTRY' => $bean->primary_address_country,
//                    'COUNTRYISO' => $bean->primary_address_country,
//                    'REGION' => $regioSplit[1],
//                );
//
//                //common SEGMENT NAMES
//                $E1BPADTEL = array(
//                    '@attributes' => array('SEGMENT' => '1'),
//                    'TEL_NO' => $bean->phone_work
//                );
//                $E1BPADFAX = array(
//                    '@attributes' => array('SEGMENT' => '1'),
//                    'TEL_NO' => $bean->phone_fax
//                );
//                $E1BPADSMTP = array(
//                    '@attributes' => array('SEGMENT' => '1'),
//                    'E_MAIL' => $bean->email1,
//                    'EMAIL_SRCH' => strtoupper($bean->email1)
//                );



                //segment names depending on mestyp
//                switch($mestyp){
//                    case 'ADR3MAS':
//                        $idoc = array(
//                            'E1ADR3MAS' => array(
//                                '@attributes' => array('SEGMENT' => '1'),
//                                'OBJ_TYPE_P' => 'BUS1006001',
//                                'OBJ_ID_P' => $bean->k_sap_parnerid,
//                                'OBJ_TYPE_C' => 'BUS1006',
//                                'OBJ_ID_C' => (!empty($account->k_sap_customerid) ? $account->k_sap_customerid : $account->tr_sap_creditorid),
//                                'CONTEXT' => '0001', //0005?
//                            )
//                        );
//                        $idoc['E1ADR3MAS']['E1BPAD3VL'] = $E1BPADxVL;
//                        $idoc['E1ADR3MAS']['E1BPAD3VL']['E1BPAD3VL1'] = $E1BPADxVL1;
//                        $idoc['E1ADR3MAS']['E1BPADTEL'] = $E1BPADTEL;
//                        $idoc['E1ADR3MAS']['E1BPADFAX'] = $E1BPADFAX;
//                        $idoc['E1ADR3MAS']['E1BPADSMTP'] = $E1BPADSMTP;
//                        break;
//
//                    case 'ADR2MAS':
//                    default:
//                        $idoc = array(
//                            'E1ADR2MAS' => array(
//                                '@attributes' => array('SEGMENT' => '1'),
//                                'OBJ_TYPE' => 'BUS1006001',
//                                'OBJ_ID' => $bean->k_sap_parnerid,
//                                'CONTEXT' => '0001', //0004?
//                            )
//                        );
//                        $idoc['E1ADR2MAS']['E1BPAD2VL'] = $E1BPADxVL;
//                        $idoc['E1ADR2MAS']['E1BPAD2VL']['E1BPAD2VL1'] = $E1BPADxVL1;
//                        $idoc['E1ADR2MAS']['E1BPADTEL'] = $E1BPADTEL;
//                        $idoc['E1ADR2MAS']['E1BPADFAX'] = $E1BPADFAX;
//                        $idoc['E1ADR2MAS']['E1BPADSMTP'] = $E1BPADSMTP;
//                        break;
//
//                }

                //format phone number (remove coutry, extract extension
                $phone_work = $this->extractPhoneDataForSap($bean->phone_work);
                $phone_mobile = $this->extractPhoneDataForSap($bean->phone_mobile);
                $phone_fax = $this->extractPhoneDataForSap($bean->phone_fax, "FAX");

                $idoc = array(
                    'E1ADR2MAS' => array(
                        '@attributes' => array('SEGMENT' => '1'),
                        'OBJ_TYPE' => 'BUS1006001',
                        'OBJ_ID' => $bean->k_sap_parnerid,
                        'CONTEXT' => '0001',
                        'E1BPAD2VL' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'FROM_DATE' => '00010101',
                            'TO_DATE' => '99991231',
                            'CITY' => $bean->primary_address_city,
                            'POSTL_COD1' => $bean->primary_address_postalcode,
                            'STREET' => $bean->primary_address_street,
                            'HOUSE_NO' => $bean->primary_address_hsnm,

                            'REGION' => $regioSplit[1],
                            'LANGU_P' => $bean->k_language,
                            'FIRSTNAME' => $bean->first_name,
                            'LASTNAME' => $bean->last_name,
                            'TITLE_P' => $bean->salutation,
//                            'SEX' => intval($bean->salutation),
                            'E1BPAD2VL1' => array(
                                '@attributes' => array('SEGMENT' => '1'),
                                'LANGU_CR' => 'E',
                                'LANGUCRISO' => 'EN',
                                'ADDR_GROUP' => 'BP',
                                'COUNTRY' => $bean->primary_address_country,
                                'COUNTRYISO' => $bean->primary_address_country,
                                'REGION' => $regioSplit[1],
                            )
                        ),
                        'E1BPADTEL' => array(
                            array(
                                '@attributes' => array('SEGMENT' => '1'),
                                'COUNTRY' => $bean->primary_address_country,
                                'TELEPHONE' => $phone_work['number'],
                                'EXTENSION' => $phone_work['extension'],
                                'R_3_USER' => 1
                            ),
                            array(
                                '@attributes' => array('SEGMENT' => '1'),
                                'COUNTRY' => $bean->primary_address_country,
                                'TELEPHONE' => $phone_mobile['number'],
                                'EXTENSION' => $phone_mobile['extension'],
                                'R_3_USER' => 3
                            ),
                        ),
                        'E1BPADFAX' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'COUNTRY' => $bean->primary_address_country,
                            'FAX' => $phone_fax['number'],
                            'EXTENSION' => $phone_fax['extension'],
                        ),
                        'E1BPADSMTP' => array(
                            '@attributes' => array('SEGMENT' => '1'),
                            'E_MAIL' => $bean->email1,
                            'EMAIL_SRCH' => strtoupper($bean->email1)
                        )
                    )
                );

                SAPIdoc::rawXMLExport($bean, $segmentRecord['id'], $idoc);
            }
        }

        return true;
    }

    public function processKFProps(&$bean, $event, $arguments)
    {
        $db = DBManagerFactory::getInstance();

        $orgNumberRanges = array(
            '1000' => '6CE9C6ED-380C-4728-83DF-8884C578486D'
            /* '1010' => '8AD9E50B-318A-4875-BECC-ADC8065635CB',
             '1020' => 'AB5C0E28-03E0-47D2-9CD0-3F3E3A6C7B96',
             '1030' => 'AFC09D81-5F76-4281-A0F7-030467980060',
             '1040' => '818B12B7-73A5-464B-818A-4F8128DC3258',
             '1070' => '55C70E73-74C1-41E9-A3EC-FF01EA6793A0',
             '2000' => 'CD3271A9-08C2-4BE2-8746-3380E3082257',
             '2010' => '0C6D62EC-C674-4ECB-A71D-78C3A6035854',
             '2100' => '2D7C496E-868D-45E4-AA99-E6A3FE100635',
             '2200' => '28C86257-AB45-4439-923C-3B7F7FC3CA22',
             '3500' => 'B9934040-272A-4DD7-B143-A0DCBDCF60DF',
             '5500' => '57605E87-8771-4A06-9110-661B4BD2CDDA' */
        );

        if ($bean->status == 'ready2send') {

            if(empty($bean->krfprop_order_number)) {
                $vkOrgRow = $db->fetchByAssoc($db->query("SELECT elementvalue FROM korgobjects_korgobjectelementvalues, korgobjectelements WHERE korgobjects_korgobjectelementvalues.korgobjectelement_id = korgobjectelements.id AND korgobjectelements.name = 'vkorg' AND korgobjects_korgobjectelementvalues.korgobject_id = '$bean->korgobjectmain'"));
                $vkorg = $vkOrgRow['elementvalue'];

                LoggerManager::getLogger()->debug('getting number for proposal in VKORG ' . $vkorg);

                $newId = str_pad(SpiceNumberRanges::getNextNumber($orgNumberRanges[$vkorg] ?: $orgNumberRanges['1000']), 10, '0', STR_PAD_LEFT );
                $bean->db->query("UPDATE krfprops SET krfprop_order_number = '$newId' WHERE id='$bean->id'");
                $bean->krfprop_order_number = $newId;
            }

            $this->process($bean, "after", "new_update");

            // set the status
            $bean->db->query("UPDATE krfprops SET status = 'sent2sap' WHERE id='$bean->id'");
        }


        return true;
    }

    /**
     * process the whole sapidoc checks and triggers any action(s)
     *
     * @param unknown $bean
     * @param unknown $hook_event
     * @param unknown $object_event
     * @return boolean
     */
    private function process(&$bean, $hook_event, $object_event)
    {
        $db = DBManagerFactory::getInstance();
        // check, if there is an after_save coming by idoc import avoiding direct export by save events
        if (isset($_SESSION['incoming_idoc_beans'])) {
            foreach ($_SESSION['incoming_idoc_beans'][$bean->module_dir] as $id) {
                if ($bean->id == $id) {
                    return;
                }
            }
        }

        // available segments defined?
        $sql = "SELECT sapidocsegments.*, sysmodules.module FROM sapidocsegments "
            . "INNER JOIN sysmodules ON sysmodules.id = sapidocsegments.sysmodule_id "
            . "WHERE sysmodules.module = '" . $bean->module_dir . "' "
            . "AND sapidocsegments.active = 1";

        //BEGIN maretval 20180912: workaround to not generate E1LFA1M (CREMAS) when Account sent to SAP
        if($bean->module_dir == 'Accounts' && $hook_event == "after" && $object_event == "new_update")
            $sql.= " AND sap_segment <> 'E1LFA1M'";
        //END

        $result = $db->query($sql);
        while ($row = $db->fetchByAssoc($result)) {
            $segments = SAPIdoc::bubbleExportSegmentRecords($bean, $row);
            if (count($segments['all'])) {
                $sapidoc = BeanFactory::newBean("SAPIdocs");
                if ($sapidoc) {
                    $sapidoc->prepareOutbound($bean, $segments);
                }
            }
        }
    }

    /**
     * "normal" logic hook call called by custom_logic before retrieving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function before_retrieve_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "before", "retrieve");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic after retrieving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function after_retrieve_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "after", "retrieve");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic before saving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function before_new_update_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "before", "new_update");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic after saving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function after_new_update_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "after", "new_update");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic before saving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function before_new_record(&$bean, $event, $arguments)
    {
        $sql = "SELECT count(id) AS c FROM " . $bean->table_name . " 
	            WHERE id = '" . $bean->id . "' AND deleted = 0";
        $result = $bean->db->query($sql);
        $row = $bean->db->fetchByAssoc($result);
        if ($row ['c'] == 0 || $bean->new_with_id) {
            // this id dont exist, so its not new or the falg is set to new
            $bean->isNew = true;
            $this->process($bean, "before", "new");
        }
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic after saving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function after_new_record(&$bean, $event, $arguments)
    {
        if ($bean->isNew) {
            $this->process($bean, "after", "new");
        }
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic before saving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function before_update_record(&$bean, $event, $arguments)
    {
        $sql = "SELECT count(id) AS c FROM " . $bean->table_name . " 
	            WHERE id = '" . $bean->id . "' AND deleted = 0";
        $result = $bean->db->query($sql);
        $row = $bean->db->fetchByAssoc($result);
        if ($row ['c'] > 0) {
            // this id dont exist, so its not new or the falg is set to new
            $bean->isUpdate = true;
            $this->process($bean, "before", "update");
        }
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic after saving a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function after_update_record(&$bean, $event, $arguments)
    {
        if ($bean->isUpdate) {
            $this->process($bean, "after", "update");
        }
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic before deleting a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function before_delete_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "before", "delete");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic after deleting a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function after_delete_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "after", "delete");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic before restoring a record relationship
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function before_restore_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "before", "restore");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic after restoring a record relationship
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function after_restore_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "after", "restore");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic before setting a new relationship to a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function before_new_relationship_record(&$bean, $event, $arguments)
    {

        /**
         * arguments look like that:
         *
         * $custom_logic_arguments = array();
         * $custom_logic_arguments['id'] = $focus->id;
         * $custom_logic_arguments['related_id'] = $related->id;
         * $custom_logic_arguments['module'] = $focus->module_dir;
         * $custom_logic_arguments['related_module'] = $related->module_dir;
         * $custom_logic_arguments['related_bean'] = $related;
         * $custom_logic_arguments['link'] = $link_name;
         * $custom_logic_arguments['relationship'] = $this->name;
         */
        $this->process($bean, "before", "new_relationship");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic after setting a new relationship to a record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function after_new_relationship_record(&$bean, $event, $arguments)
    {

        /**
         * arguments look like that:
         *
         * $custom_logic_arguments = array();
         * $custom_logic_arguments['id'] = $focus->id;
         * $custom_logic_arguments['related_id'] = $related->id;
         * $custom_logic_arguments['module'] = $focus->module_dir;
         * $custom_logic_arguments['related_module'] = $related->module_dir;
         * $custom_logic_arguments['related_bean'] = $related;
         * $custom_logic_arguments['link'] = $link_name;
         * $custom_logic_arguments['relationship'] = $this->name;
         */
        $this->process($bean, "after", "new_relationship");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic processing any record
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function process_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "", "process");
        return null;
    }

    /**
     * "normal" logic hook call called by custom_logic when there was an exception by the SugarController at any bean
     *
     * @param unknown $bean
     * @param unknown $event
     * @param unknown $arguments
     * @return NULL
     */
    public function exception_record(&$bean, $event, $arguments)
    {
        $this->process($bean, "", "exception");
        return null;
    }


    public function extractPhoneDataForSap($phone_number)
    {
        $phone = array();
        if(substr($phone_number, 1, 1) != " ")
            $phone['country'] =  substr($phone_number, strpos($phone_number, "+") + 1, strpos($phone_number, " "));
        $phone['number'] = trim(substr($phone_number, strpos($phone_number, " ")));

        if(strpos($phone_number, "-") > 0){
            $phone['number'] = trim(substr($phone['number'], 0, strpos($phone['number'], "-")));
            if(substr($phone['number'], 0, 1) !="0")
                $phone['number'] = "0".$phone['number'];
        }
        $phone['extension'] = "";
        if(strpos($phone_number, "-") > 0)
            $phone['extension'] = trim(substr($phone_number, strpos($phone_number, "-") + 1, strlen($phone_number)));

        return $phone;
    }




}
