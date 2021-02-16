<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesVoucherRedemption'] = array(
    'table' => 'salesvoucherredemptions',
    'fields' => array(
        'redemption_amount' => array(
            'name' => 'redemption_amount',
            'vname' => 'LBL_AMOUNT',
            'type' => 'currency',
            'currency_id' => 'redemption_currency_id',
            'len' => 20,
            'required' => true,
            'comment' => 'the amount redeemed'
        ),
        'redemption_currency_id' => array(
            'name' => 'redemption_currency_id',
            'type' => 'id',
            'group' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'comment' => 'Currency used for display purposes'
        ),
        'salesvoucher_id' => array(
            'name' => 'salesvoucher_id',
            'type' => 'id',
            'vname' => 'LBL_SALESVOUCHER_ID'
        ),
        'salesvoucher_name' => array(
            'source' => 'non-db',
            'name' => 'salesvoucher_name',
            'vname' => 'LBL_SALESVOUCHER',
            'type' => 'relate',
            'len' => '255',
            'id_name' => 'salesvoucher_id',
            'module' => 'SalesVouchers',
            'rname' => 'name',
            'link' => 'salesvoucher'
        ),
        'salesvoucher' => array(
            'name' => 'salesvoucher',
            'type' => 'link',
            'module' => 'SalesVouchers',
            'relationship' => 'salesvoucher_salesvoucherredemptions',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESVOUCHERS'
        )
    ),
    'relationships' => array(
        'salesvoucher_salesvoucherredemptions' => [
            'lhs_module' => 'SalesVouchers',
            'lhs_table' => 'salesvouchers',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesVoucherRedemptions',
            'rhs_table' => 'salesvoucherredemptions',
            'rhs_key' => 'salesvoucher_id',
            'relationship_type' => 'one-to-many'
        ],
    ),
    'indices' => array(
        'id' => array('name' => 'salesvoucherredemptions_pk', 'type' => 'primary', 'fields' => array('id')),
        'salesvoucherredemptions_salesvoucher_id' => array('name' => 'salesvoucherredemptions_salesvoucher_id', 'type' => 'index', 'fields' => array('salesvoucher_id'))
    ),
);


VardefManager::createVardef('SalesVoucherRedemptions', 'SalesVoucherRedemption', array('default', 'assignable'));
//avoid PHP Fatal error:  Uncaught Error: Cannot use string offset as an array
global $dictionary;
$dictionary['SalesVoucher']['fields']['name']['required'] = false;
