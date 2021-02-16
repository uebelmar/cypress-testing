<?php

use SpiceCRM\includes\database\DBManagerFactory;

/**
 * 
 * @global type $db
 * @global type $timedate
 * @param SAPIdoc $idoc
 * @param array $segment_defintion
 * @param array $rawFields
 * @return boolean
 */
function E1MTXHM_in(SAPIdoc $idoc, array $segment_defintion, array $rawFields) {
    global $timedate;
$db = DBManagerFactory::getInstance();

    $sapLanguages = array(
        'D' => 'de_DE',
        'E' => 'en_us',
        'R' => 'ru_RU'
    );

    /*
    if($rawFields['TDOBJECT'] == 'MVKE'){
        // exlode object
        $matNr = substr($rawFields['TDNAME'], 0, 18);
        $vkOrg = substr($rawFields['TDNAME'], 18, 4);
        $vtWeg = substr($rawFields['TDNAME'], 22, 2);

        $product = \SpiceCRM\data\BeanFactory::getBean('KProducts');
        $product->retrieve_by_string_fields(array('sap_materialid' => $matNr));

        if(!empty($product->id)){
            // get the text
            $matTxt = '';
            foreach ($rawFields['E1MTXLM'] as $textSegment){
                if($matTxt != '') $matTxt .= '\n';
                $matTxt .= $textSegment['TDLINE'];
            }

            // find a vtext
            $vtext = $db->fetchByAssoc($db->query("SELECT id FROM kproductsdescriptions_vtexts WHERE kproduct_id='$product->id' AND vkorg = '$vkOrg' AND vtweg='$vtWeg' AND language='{$sapLanguages[$rawFields['TDSPRAS']]}' AND deleted = 0"));
            if($vtext && !empty($vtext['id'])){
                $db->query("UPDATE kproductsdescriptions_vtexts SET description = '$matTxt', date_modified = '".$timedate->nowDb()."'  WHERE id = '{$vtext['id']}'");
            } else {
                $guid = create_guid();
                $db->query("INSERT INTO kproductsdescriptions_vtexts (id, vkorg, vtweg, kproduct_id, language, description, date_modified, date_entered, deleted) VALUES('$guid', '$vkOrg', '$vtWeg', '$product->id', '{$sapLanguages[$rawFields['TDSPRAS']]}', '$matTxt', '".$timedate->nowDb()."', '".$timedate->nowDb()."', 0)");
            }
        }
    }
    */

    return true;
}
