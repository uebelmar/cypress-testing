<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

function E1BPSDHD1_out(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, $parent = null)
{

    $db = DBManagerFactory::getInstance();
    // get the org objet and determine the vkorg
    /*
    $orgObject = \SpiceCRM\data\BeanFactory::getBean('KOrgObjects', $bean->korgobjectmain);
    $vkorg = '1000';
    foreach($orgObject->elemenvalues as $elementvalue){
        if($elementvalue['name'] == 'vkorg')
            $vkorg = $elementvalue['elementvalue'];
    }
    */

    $vkOrgRow = $db->fetchByAssoc($db->query("select elementvalue from korgobjects_korgobjectelementvalues, korgobjectelements where korgobjects_korgobjectelementvalues.korgobjectelement_id = korgobjectelements.id AND korgobjectelements.name = 'vkorg' AND korgobjects_korgobjectelementvalues.korgobject_id = '$bean->korgobjectmain'"));
    $vkorg = $vkOrgRow['elementvalue'];

    // get the account details
    $accountsDetail = BeanFactory::getBean('KAccountsDetails');
    $accountsDetail->retrieve_by_string_fields(array('account_id' => $bean->customer_id, 'vkorg'=> $vkorg));

    // set segment fields for dales org
    $rawFields['SALES_ORG'] = $vkorg;
    $rawFields['SALES_GRP'] = $accountsDetail->vkgrp;
    $rawFields['SALES_OFF'] = $accountsDetail->vkbur;

    // set date fields
    $date = new DateTime();
    $rawFields['REQ_DATE_H'] = $date->format('Ymd');
    $rawFields['PURCH_DATE'] = $date->format('Ymd');

    return true;
}
