<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['PriceCondition'] = array(
    'table' => 'priceconditions',
    'fields' => array(
        'pricecondition_key' => array (
            'name' => 'pricecondition_key',
            'vname' => 'LBL_PRICECONDITION_KEY',
            'type' => 'varchar',
            'required' => true,
            'len' => 256
        ),
        'priceconditionrequest_id' => array (
            'name' => 'priceconditionrequest_id',
            'vname' => 'LBL_PRICECONDITIONREQUEST_ID',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id foreign key of the priceconditionrequest'
        ),
        'priceconditiontype_id' => array (
            'name' => 'priceconditiontype_id',
            'vname' => 'LBL_PRICECONDITIONTYPE_ID',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id foreign key of the pricecondition'
        ),
        'priceconditiontypedetermination_id' => array (
            'name' => 'priceconditiontypedetermination_id',
            'vname' => 'LBL_PRICECONDITIONTYPEDETERMINATION_ID',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id foreign key of the priceconditiondetermination'
        ),
        'ext_id' => array (
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => 50,
            'comment' => 'an optional external id, in SAP integrated scenarios the KNUMH value'
        ),
        'valid_from' => array (
            'name' => 'valid_from',
            'vname' => 'LBL_VALID_FROM',
            'type' => 'date',
            'required' => false,
            'comment' => 'the date this record is valid from'
        ),
        'valid_to' => array (
            'name' => 'valid_to',
            'vname' => 'LBL_VALID_TO',
            'type' => 'date',
            'required' => false,
            'comment' => 'the date this record is valid to'
        ),
        'currency_id' => array(
            'name' => 'currency_id',
            'type' => 'id',
            'vname' => 'LBL_CURRENCY',
            'comment' => 'the currency this record is maintained in'
        ),
        'uom_id' => array(
            'name' => 'uom_id',
            'vname' => 'LBL_UOM',
            'type' => 'unitofmeasure',
            'dbtype' => 'varchar',
            'len' => 36,
            'comment' => 'the unit of measure this record is maintained in'
        ),
        'amount' => array(
            'name' => 'amount',
            'vname' => 'LBL_amount',
            'type' => 'currency',
            'currency_id' => 'currency_id',
            'comment' => 'the actual value of the record'
        ),
        'priceconditionrequest' => array(
            'name' => 'priceconditionrequest',
            'vname' => 'LBL_PRICECONDITIONREQUEST',
            'module' => 'PriceConditionRequests',
            'type' => 'link',
            'relationship' => 'priceconditionrequest_priceconditions',
            'source' => 'non-db',
            'comment' => 'the link to the pricecondition request for this record'
        ),
        'priceconditionscales' => array(
            'name' => 'priceconditionscales',
            'vname' => 'LBL_PRICECONDITIONSCALES',
            'module' => 'PriceConditionScales',
            'type' => 'link',
            'relationship' => 'pricecondition_priceconditionscales',
            'source' => 'non-db',
            'comment' => 'the link to the scales for this record',
            'default' => true
        ),
        'priceconditionevs' => array(
            'name' => 'priceconditionevs',
            'vname' => 'LBL_PRICECONDITIONELEMENTVALUES',
            'module' => 'PriceConditionElementValues',
            'type' => 'link',
            'relationship' => 'pricecondition_priceconditionev',
            'source' => 'non-db',
            'comment' => 'the link to the elementvalues for this record'
        )
    ),
    'indices' => array(
        array('name' => 'idx_priceconditions_key_validity', 'type' => 'index', 'fields' => array('pricecondition_key', 'valid_from', 'valid_to', 'deleted')),
    )
);

VardefManager::createVardef('PriceConditions', 'PriceCondition', array('default', 'assignable'));

// ToDo: change with Vardef Manager
// do not set name to required
$dictionary['PriceCondition']['fields']['name']['required'] = false;
