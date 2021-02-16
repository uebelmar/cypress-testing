<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesDocItemCondition'] = array(
    'table' => 'salesdocitemconditions',
    'audited' => true,
    'fields' => array(
        'priceconditiontype_id' => array(
            'name' => 'priceconditiontype_id',
            'vname' => 'LBL_PRICECONDITIONTYPE_ID',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesdocitem_id' => array(
            'name' => 'salesdocitem_id',
            'vname' => 'LBL_SALESDOCITEM_ID',
            'type' => 'id'
        ),
        'salesdocitem' => array(
            'name' => 'salesdocitem',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEM',
            'relationship' => 'salesdocitem_salesdocitemconditionss',
            'source' => 'non-db',
            'module' => 'SalesDocItems'
        ),
        'itemnr' => array(
            'name' => 'itemnr',
            'vname' => 'LBL_ITEM_NR',
            'type' => 'int',
            'len' => 4
        ),
        'amount' => array(
            'name' => 'amount',
            'vname' => 'LBL_AMOUNT',
            'type' => 'double'
        ),
        'amount_total' => array(
            'name' => 'amount_total',
            'vname' => 'LBL_AMOUNT_TOTAL',
            'type' => 'double'
        ),
        'percentage' => array(
            'name' => 'percentage',
            'vname' => 'LBL_percentage',
            'type' => 'double'
        )
    ),
    'indices' => array(
        array('name' => 'idx_salesdocitemcond_ssdi_id_del', 'type' => 'index', 'fields' => array('salesdocitem_id', 'deleted'),),
    ),
    'relationships' => array(
        'salesdocitem_salesdocitemconditionss' => array(
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesDocItemConditions',
            'rhs_table' => 'salesdocitemconditions',
            'rhs_key' => 'salesdocitem_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_lock' => true
);



VardefManager::createVardef('SalesDocItemConditions', 'SalesDocItemCondition', array('default', 'assignable'));

