<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningContent'] = array(
    'table' => 'salesplanningcontents',
    'audited' => false,
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 40,
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'salesplanningcontentfields' => array(
            'name' => 'salesplanningcontentfields',
            'type' => 'link',
            'relationship' => 'salesplanningcontents_salesplanningcontentfields',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTFIELDS',
            'duplicate_merge' => 'disabled',
            'module' => 'SalesPlanningContentFields',
            'default' => true
        ),
        'salesplanningversions' => array(
            'name' => 'salesplanningversions',
            'type' => 'link',
            'relationship' => 'salesplanningversions_salesplanningcontents',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESPLANNINGPLANNINGVERSIONS',
            'duplicate_merge' => 'disabled',
        ),
    ),
    'indices' => array(
    ),
    'relationships' => array(
    ),
    'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningContents', 'SalesPlanningContent', array('default', 'assignable'));
