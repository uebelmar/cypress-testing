<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Address'] = array(
    'table' => 'addresses',
    'audited' => true,
    'unified_search' => true,
    'fields' => array(
        'address_attn' => array(
            'name' => 'address_attn',
            'vname' => 'LBL_ADDRESS_ATTN',
            'type' => 'varchar',
            'len' => '150'
        ),
        'address_street' => array(
            'name' => 'address_street',
            'vname' => 'LBL_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150'
        ),
        'address_street_2' => array(
            'name' => 'address_street_2',
            'vname' => 'LBL_ADDRESS_STREET_2',
            'type' => 'varchar',
            'len' => '150',
        ),
        'address_street_3' => array(
            'name' => 'address_street_3',
            'vname' => 'LBL_ADDRESS_STREET_3',
            'type' => 'varchar',
            'len' => '150',
        ),
        'address_street_4' => array(
            'name' => 'address_street_4',
            'vname' => 'LBL_ADDRESS_STREET_4',
            'type' => 'varchar',
            'len' => '150',
        ),
        'address_city' => array(
            'name' => 'address_city',
            'vname' => 'LBL_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
        ),
        'address_state' => array(
            'name' => 'address_state',
            'vname' => 'LBL_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
        ),
        'address_postalcode' => array(
            'name' => 'address_postalcode',
            'vname' => 'LBL_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
        ),
        'address_country' => array(
            'name' => 'address_country',
            'vname' => 'LBL_ADDRESS_COUNTRY',
            'type' => 'varchar',
        ),
        'address_latitude' => array(
            'name' => 'address_latitude',
            'vname' => 'LBL_ADDRESS_LATITUDE',
            'type' => 'double',
            'group' => 'address',
        ),
        'address_longitude' => array(
            'name' => 'address_longitude',
            'vname' => 'LBL_ADDRESS_LONGITUDE',
            'type' => 'double',
            'group' => 'address',
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ACCOUNT_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
            'audited' => true,
            'comment' => 'Account ID of the parent of this account',
        ),
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'required' => false,
            'group' => 'parent_name',
            'options' => 'parent_type_display',
            'len' => 255,
            'comment' => 'The Sugar object to which the call is related',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_LIST_RELATED_TO',
            'type' => 'parent',
            'group' => 'parent_name',
            'source' => 'non-db',
            'options' => 'parent_type_display',
        ),
        'accounts' => array(
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'account_addresses',
            'module' => 'Accounts',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNT'
        ),
        'contacts' => array(
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'contact_addresses',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS',
            'module' => 'Contacts'
        )

    ),
    'relationships' => array(
        'account_addresses' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Addresses',
            'rhs_table' => 'addresses',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),
        'contact_addresses' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Addresses',
            'rhs_table' => 'addresses',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts'
        )
    ),
    'indices' => array(
        array('name' => 'idx_addresses_id_del', 'type' => 'index', 'fields' => array('id', 'deleted')),
        array('name' => 'idx_addresses_parentid_del', 'type' => 'index', 'fields' => array('parent_id', 'deleted'))
    ),
    'optimistic_locking' => true,
);

VardefManager::createVardef('Addresses', 'Address', array('default', 'assignable', 'basic'));

// name is not required
//set global else error with PHP7.1: Uncaught Error: Cannot use string offset as an array
global $dictionary;
$dictionary['Address']['fields']['name']['required'] = false;
