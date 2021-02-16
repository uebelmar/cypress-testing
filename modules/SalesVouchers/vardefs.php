<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesVoucher'] = array(
    'table' => 'salesvouchers',
    'fields' => array(
        'voucher_type' => array(
            'name' => 'voucher_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => 10,
            'options' => 'salesvoucher_type_dom'
        ),
        'voucher_value' => array(
            'name' => 'voucher_value',
            'vname' => 'LBL_VALUE',
            'type' => 'currency',
            'len' => 20,
            'required' => true,
            'isnull' => false
        ),
        'voucher_value_open' => array(
            'name' => 'voucher_value_open',
            'vname' => 'LBL_VALUE_OPEN',
            'type' => 'currency',
            'len' => 20,
            'isnull' => false
        ),
        'currency_id' => array(
            'name' => 'currency_id',
            'type' => 'id',
            'group' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'comment' => 'currency of the voucher'
        ),
        'voucher_number' => array(
            'name' => 'voucher_number',
            'vname' => 'LBL_NUMBER',
            'type' => 'varchar',
            'len' => 100,
            'required' => false,
            'isnull' => false
        ),
        'valid_until' => array(
            'name' => 'valid_until',
            'type' => 'date',
            'vname' => 'LBL_VALID_UNTIL',
            'comment' => 'validity date'
        ),
        'parent_type' => [
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'required' => false,
            'group' => 'parent_name',
            'options' => 'parent_type_display',
            'len' => 255
        ],
        'parent_name' => [
            'name' => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_RELATED_TO',
            'type' => 'parent',
            'group' => 'parent_name',
            'source' => 'non-db',
            'options' => 'parent_type_display',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_LIST_RELATED_TO_ID',
            'type' => 'varchar',
            'len' => 36,
            'group' => 'parent_name',
            'reportable' => false
        ],
        'contacts' => [
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'contacts_salesvouchers',
            'module' => 'Contacts',
            'bean_name' => 'Contact',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS',
        ],
        'email' => [
            'name' => 'email',
            'type' => 'varchar',
            'bean_name' => 'Contact',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL',
            'comment' => 'non db field for the email address of a contact if no contact_id is set'
        ],
        'salesdoc_id' => array(
            'name' => 'salesdoc_id',
            'type' => 'varchar',
            'len' => 36,
            'vname' => 'LBL_SALESDOCS_ID',
            'comment' => 'the salesdoc this vopucher has been redemmed with'
        ),
        'salesdoc_name' => array(
            'source' => 'non-db',
            'name' => 'salesdoc_name',
            'vname' => 'LBL_SALESDOC',
            'type' => 'relate',
            'len' => '255',
            'id_name' => 'salesdoc_id',
            'module' => 'SalesDocs',
            'link' => 'salesdoc',
            'join_name' => 'salesdocs',
            'rname' => 'salesdocnumber', #'name'
        ),
        'salesdoc' => array(
            'name' => 'salesdoc',
            'type' => 'link',
            'relationship' => 'salesdocs_salesvouchers',
            'link_type' => 'one',
            'side' => 'left',
            'source' => 'non-db',
            'vname' => 'LBL_SALESDOC',
        ),
        'product_id' => array(
            'name' => 'product_id',
            'type' => 'varchar',
            'len' => 36,
            'vname' => 'LBL_PRODUCT_ID'
        ),
        'product_name' => array(
            'source' => 'non-db',
            'name' => 'product_name',
            'vname' => 'LBL_PRODUCT',
            'type' => 'relate',
            'len' => '255',
            'id_name' => 'product_id',
            'module' => 'Products',
            'link' => 'product',
            'rname' => 'name'
        ),
        'product' => array(
            'name' => 'product',
            'type' => 'link',
            'relationship' => 'product_salesvouchers',
            'link_type' => 'one',
            'side' => 'left',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCT',
        ),
        'outputtemplate_id' => array(
            'name' => 'outputtemplate_id',
            'type' => 'varchar',
            'len' => 36,
            'vname' => 'LBL_OUTPUTTEMPLATE_ID'
        ),
        'outputtemplate_name' => array(
            'source' => 'non-db',
            'name' => 'outputtemplate_name',
            'vname' => 'LBL_OUTPUT_TEMPLATE',
            'type' => 'relate',
            'len' => '255',
            'id_name' => 'outputtemplate_id',
            'module' => 'OutputTemplates',
            'rname' => 'name'
        ),
        'outputtemplate' => array(
            'name' => 'outputtemplate',
            'type' => 'link',
            'relationship' => 'outputtemplates_salesvouchers',
            'link_type' => 'one',
            'side' => 'left',
            'source' => 'non-db',
            'vname' => 'LBL_OUTPUTTEMPLATES',
        ),
        'voucher_status' => [
            'name' => 'voucher_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'salesvoucher_status_dom',
        ],
        'salesvoucherredemptions' => array(
            'name' => 'salesvoucherredemptions',
            'type' => 'link',
            'relationship' => 'salesvoucher_salesvoucherredemptions',
            'link_type' => 'one',
            'source' => 'non-db',
            'module' => 'SalesVoucherRedemptions',
            'vname' => 'LBL_SALESVOUCHERREDEMPTIONS',
        )
    ),
    'relationships' => array(
        'contacts_salesvouchers' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesVouchers',
            'rhs_table' => 'salesvouchers',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts',
        ],
        'consumer_salesvouchers' => [
            'lhs_module' => 'Consumers',
            'lhs_table' => 'consumers',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesVouchers',
            'rhs_table' => 'salesvouchers',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Consumers',
        ],
        'salesdocitems_salesvouchers' => [
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesVouchers',
            'rhs_table' => 'salesvouchers',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'SalesDocItems',
        ],
        'outputtemplates_salesvouchers' => [
            'lhs_module' => 'OutputTemplates',
            'lhs_table' => 'outputtemplates',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesVouchers',
            'rhs_table' => 'salesvouchers',
            'rhs_key' => 'outputtemplate_id',
            'relationship_type' => 'one-to-many'
        ]
    ),
    'indices' => array(
        'id' => array('name' => 'salesvouchers_pk', 'type' => 'primary', 'fields' => array('id')),
        'salesdocs_salesvouchers_salesdoc_id' => array('name' => 'salesdocs_salesvouchers_salesdoc_id', 'type' => 'index', 'fields' => array('salesdoc_id'))
    ),
);


VardefManager::createVardef('SalesVouchers', 'SalesVoucher', array('default', 'assignable'));
//BEGIN PHP7.1 compatibility: avoid PHP Fatal error:  Uncaught Error: Cannot use string offset as an array
global $dictionary;
//END
$dictionary['SalesVoucher']['fields']['name']['required'] = false;
