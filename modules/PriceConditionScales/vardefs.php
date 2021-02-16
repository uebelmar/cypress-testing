<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['PriceConditionScale'] = array(
    'table' => 'priceconditionscales',
    'fields' => array(
        'pricecondition_id' => array (
            'name' => 'pricecondition_id',
            'vname' => 'LBL_PRICECONDITION',
            'type' => 'id',
            'required' => true
        ),
        'pricecondition' => array(
            'name' => 'pricecondition',
            'vname' => 'LBL_PRICECONDITION',
            'module' => 'PriceConditions',
            'type' => 'link',
            'relationship' => 'pricecondition_priceconditionscales',
            'source' => 'non-db'
        ),
        'quantitiy_from' => array(
            'name' => 'quantitiy_from',
            'vname' => 'LBL_QUANTITIY_FROM',
            'type' => 'double',
            'comment' => 'the quantitiy this is valid from'
        ),
        'amount' => array(
            'name' => 'amount',
            'vname' => 'LBL_amount',
            'type' => 'double',
            'comment' => 'the actual value of the record'
        ),
    ),
    'relationships' => array(
        'pricecondition_priceconditionscales' => array(
            'lhs_module' => 'PriceConditions',
            'lhs_table' => 'priceconditions',
            'lhs_key' => 'id',
            'rhs_module' => 'PriceConditionScales',
            'rhs_table' => 'priceconditionscales',
            'rhs_key' => 'pricecondition_id',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'indices' => array(
        array('name' => 'idx_priceconditionscale_pcid', 'type' => 'index', 'fields' => array('pricecondition_id')),
    )
);

VardefManager::createVardef('PriceConditionScales', 'PriceConditionScale', array('default', 'assignable'));
