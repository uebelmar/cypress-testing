<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceLocation'] = array(
    'table' => 'servicelocations',
    'comment' => 'ServiceLocations Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,
	
	'fields' => array(

        //account receiving party
        'account_id' => array(
            'name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_ID',
            'type' => 'id',
        ),
        'account_name' => array(
            'name' => 'account_name',
            'vname' => 'LBL_ACCOUNT',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'account_id',
            'rname' => 'name',
            'module' => 'Accounts',
            'link' => 'accounts',
            'join_name' => 'accounts'
        ),
        'accounts' => array(
            'name' => 'accounts',
            'vname' => 'LBL_ACCOUNT',
            'type' => 'link',
            'module' => 'Accounts',
            'relationship' => 'servicelocation_accounts',
            'link_type' => 'one',
            'source' => 'non-db',
        ),

        //contact
        'contact_id' => array(
            'name' => 'contact_id',
            'vname' => 'LBL_CONTACT_ID',
            'type' => 'id',
        ),
        'contact_name' => array(
            'name' => 'contact_name',
            'vname' => 'LBL_CONTACT',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'contact_id',
            'rname' => 'last_name',
            'db_concat_fields' => array(0 => 'first_name', 1 => 'last_name'),
            'module' => 'Contacts',
            'link' => 'contacts',
            'join_name' => 'contacts',
            'required' => false
        ),
        'contacts' => array(
            'vname' => 'LBL_CONTACTS',
            'name' => 'contacts',
            'type' => 'link',
            'module' => 'Contacts',
            'relationship' => 'servicelocations_contacts',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db'
        ),
        'primary_address_street' => array(
            'name' => 'primary_address_street',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150',
            'comment' => 'The street address used for primary address',
            'group' => 'address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_street_2' => array(
            'name' => 'primary_address_street_2',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_2',
            'type' => 'varchar',
            'len' => '80',
        ),
        'primary_address_street_3' => array(
            'name' => 'primary_address_street_3',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_3',
            'type' => 'varchar',
            'len' => '80',
        ),
        'primary_address_street_4' => array(
            'name' => 'primary_address_street_4',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_4',
            'type' => 'varchar',
            'len' => '80',
        ),
        'primary_address_street_number' => array(
            'name' => 'primary_address_street_number',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_NUMBER',
            'type' => 'varchar',
            'len' => 10
        ),
        'primary_address_street_number_suffix' => array(
            'name' => 'primary_address_street_number_suffix',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_NUMBER_SUFFIX',
            'type' => 'varchar',
            'len' => 25,
            'comment' => 'additonal info to the street like Appartmnent, Floor, etc'
        ),
        'primary_address_attn' => array(
            'name' => 'primary_address_attn',
            'vname' => 'LBL_PRIMARY_ADDRESS_ATTN',
            'type' => 'varchar',
            'len' => '150',
            'comment' => 'additonal attention field for the address',
            'group' => 'address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_city' => array(
            'name' => 'primary_address_city',
            'vname' => 'LBL_PRIMARY_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The city used for billing address',
            'group' => 'address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_district' => array(
            'name' => 'primary_address_district',
            'vname' => 'LBL_PRIMARY_ADDRESS_DISTRICT',
            'type' => 'varchar',
            'len' => 100,
            'group' => 'address',
            'comment' => 'The district used for the billing address',
        ),
        'primary_address_state' => array(
            'name' => 'primary_address_state',
            'vname' => 'LBL_PRIMARY_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'address',
            'comment' => 'The state used for billing address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_postalcode' => array(
            'name' => 'primary_address_postalcode',
            'vname' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'address',
            'comment' => 'The postal code used for billing address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_pobox' => array(
            'name' => 'primary_address_pobox',
            'vname' => 'LBL_PRIMARY_ADDRESS_POBOX',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'address',
            'comment' => 'The pobox used for billing address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_country' =>
            array(
                'name' => 'primary_address_country',
                'vname' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
                'type' => 'varchar',
                'group' => 'address',
                'comment' => 'The country used for the billing address',
                'merge_filter' => 'enabled',
            ),
        'primary_address_latitude' =>
            array(
                'name' => 'primary_address_latitude',
                'vname' => 'LBL_PRIMARY_ADDRESS_LATITUDE',
                'type' => 'double',
                'group' => 'address'
            ),
        'primary_address_longitude' => array(
            'name' => 'primary_address_longitude',
            'vname' => 'LBL_PRIMARY_ADDRESS_LONGITUDE',
            'type' => 'double',
            'group' => 'address'
        ),
        'primary_address_georesult' => array(
            'name' => 'primary_address_georesult',
            'vname' => 'LBL_PRIMARY_ADDRESS_GEORESULT',
            'type' => 'text'
        ),
        //serviceequipments
        'serviceequipments' => array(
            'vname' => 'LBL_SERVICEEQUIPEMENTS',
            'name' => 'serviceequipments',
            'type' => 'link',
            'module' => 'ServiceEquipments',
            'relationship' => 'servicelocations_serviceequipments',
            'link_type' => 'one',
            'side' => 'left',
            'source' => 'non-db'
        ),
        'serviceorders' => array(
            'vname' => 'LBL_SERVICEORDERS',
            'name' => 'serviceorders',
            'type' => 'link',
            'module' => 'ServiceOrders',
            'relationship' => 'servicelocation_serviceorders',
            'link_type' => 'one',
            'side' => 'left',
            'source' => 'non-db'
        ),
        //servicetickets
        'servicetickets' => array(
            'vname' => 'LBL_SERVICETICKETS',
            'name' => 'servicetickets',
            'type' => 'link',
            'module' => 'ServiceTickets',
            'relationship' => 'servicetickets_servicelocation',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'emails' => array(
            'name' => 'emails',
            'type' => 'link',
            'relationship' => 'emails_servicelocations_rel',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
        ),
	),
	'relationships' => array(
        'servicelocation_serviceorders' => array(
            'lhs_module' => 'ServiceLocations',
            'lhs_table' => 'servicelocations',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrders',
            'rhs_table' => 'serviceorders',
            'rhs_key' => 'servicelocation_id',
            'relationship_type' => 'one-to-many'
        ),
        'servicelocation_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceLocations',
            'rhs_table' => 'servicelocations',
            'rhs_key' => 'account_id',
            'relationship_type' => 'one-to-many'
        ),
        'servicelocations_contacts' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceLocations',
            'rhs_table' => 'servicelocations',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many'
        ),
        'servicelocations_serviceequipments' => array(
            'lhs_module' => 'ServiceLocations',
            'lhs_table' => 'servicelocations',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceEquipments',
            'rhs_table' => 'serviceequipments',
            'rhs_key' => 'servicelocation_id',
            'relationship_type' => 'one-to-many'
        ),
	),
	'indices' => array(
        array('name' => 'idx_servicelocation_country', 'type' => 'index', 'fields' => array('primary_address_country')),
        array('name' => 'idx_servicelocation_accid', 'type' => 'index', 'fields' => array('account_id')),
        array('name' => 'idx_servicelocation_conid', 'type' => 'index', 'fields' => array('contact_id')),
        array('name' => 'idx_servicelocation', 'type' => 'index', 'fields' => array('account_id', 'contact_id', 'deleted'))
	)
);

VardefManager::createVardef('ServiceLocations', 'ServiceLocation', array('default', 'assignable'));
