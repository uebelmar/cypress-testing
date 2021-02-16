<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

function E1MARAM_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){
    $current_user = AuthenticationController::getInstance()->getCurrentUser();

    $bean->weight_net = str_replace('.', ',', $rawFields['NTGEW']);
    $bean->weight_gross = str_replace('.', ',', $rawFields['BRGEW']);

    if(empty($bean->assigned_user_id))
        $bean->assigned_user_id = $current_user->id;

    //grab productgroup_id
    $pgrp = BeanFactory::getBean('ProductGroups');
    $pgrp->retrieve_by_string_fields(array('external_id' => $rawFields['MATKL']));
    if($pgrp->id) {
        $bean->productgroup_id = $pgrp->id;
    }

    return true;
}
