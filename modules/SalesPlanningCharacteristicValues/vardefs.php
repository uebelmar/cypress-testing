<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningCharacteristicValue'] = array(
    'table' => 'salesplanningcharacteristicvalues',
    'audited' => false,
    'fields' => array(
        'cvkey' => array(
            'name' => 'cvkey',
            'vname' => 'LBL_KEY',
            'type' => 'varchar',
            'len' => 36,
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_VALUE',
            'type' => 'varchar',
            'len' => 90,
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'salesplanningcharacteristic_name' => array(
            'name' => 'salesplanningcharacteristic_name',
            'vname' => 'LBL_SALES_PLANNING_CHARACTERISTIC',
            'source' => 'non-db',
            'type' => 'relate',
            'id_name' => 'salesplanningcharacteristic_id',
            'module' => 'SalesPlanningCharacteristics',
            'link' => 'salesplanningcharacteristics',
            'rname' => 'name'
        ),
        'salesplanningcharacteristic_id' => array(
            'name' => 'salesplanningcharacteristic_id',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesplanningcharacteristics' => array(
            'name' => 'salesplanningcharacteristics',
            'type' => 'link',
            'module' => 'SalesPlanningCharacteristics',
            'relationship' => 'salesplanningcharacteristics_salesplanningcharacteristicvalues',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db'
        ),
        'salesplanningnodes' => array(
            'name' => 'salesplanningnodes',
            'type' => 'link',
            'relationship' => 'salesplanningnodes_salesplanningcharacteristicvalues',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESPLANNINGPLANNINGNODES',
            'duplicate_merge' => 'disabled',
        ),
    ),
    'indices' => array(
        array('name' => 'salesplanningcharacteristic_id', 'type' => 'index', 'fields' => array('salesplanningcharacteristic_id')),
    ),
    'relationships' => array(
    ),
    'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningCharacteristicValues', 'SalesPlanningCharacteristicValue', array('default', 'assignable'));
