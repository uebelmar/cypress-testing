<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceOrder'] = array(
    'table' => 'serviceorders',
    'comment' => 'ServiceOrders Module',
    'audited' => false,
    'duplicate_merge' => false,
    'unified_search' => false,
    'fields' => array(
        'has_notification' => array(
            'name' => 'has_notification',
            'vname' => 'LBL_HAS_NOTIFICATION',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'inidcates that there is a new notification'
        ),
        'internal_note' => array(
            'name' => 'internal_note',
            'vname' => 'LBL_INTERNAL_NOTE',
            'type' => 'text',
            'comment' => 'an internal note to be added to the order'
        ),
        'confirmation_note' => array(
            'name' => 'confirmation_note',
            'vname' => 'LBL_CONFIRMATION_NOTE',
            'type' => 'text',
            'comment' => 'a note that can be added when confirming the order'
        ),
        'travel_time' => array(
            'name' => 'travel_time',
            'vname' => 'LBL_TRAVEL_TIME',
            'type' => 'int',
            'comment' => 'travel time in hours'
        ),
        'travel_mileage' => array(
            'name' => 'travel_mileage',
            'vname' => 'LBL_TRAVEL_MILEAGE',
            'type' => 'int',
            'comment' => 'travel mileage in km'
        ),
        'worklog' => array(
            'name' => 'worklog',
            'vname' => 'LBL_WORKLOG',
            'type' => 'text'
        ),
        'address_street' => array(
            'name' => 'address_street',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150',
            'comment' => 'The street address used for primary address',
            'group' => 'address',
            'merge_filter' => 'enabled',
        ),
        'address_street_2' => array(
            'name' => 'address_street_2',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_2',
            'type' => 'varchar',
            'len' => '80',
        ),
        'address_street_3' => array(
            'name' => 'address_street_3',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_3',
            'type' => 'varchar',
            'len' => '80',
        ),
        'address_street_4' => array(
            'name' => 'address_street_4',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_4',
            'type' => 'varchar',
            'len' => '80',
        ),
        'address_street_number' => array(
            'name' => 'address_street_number',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_NUMBER',
            'type' => 'varchar',
            'len' => 10
        ),
        'address_street_number_suffix' => array(
            'name' => 'address_street_suffix',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_NUMBER_SUFFIX',
            'type' => 'varchar',
            'len' => 25,
            'comment' => 'additional info to the street like Appartmnent, Floor, etc'
        ),
        'address_attn' => array(
            'name' => 'address_attn',
            'vname' => 'LBL_PRIMARY_ADDRESS_ATTN',
            'type' => 'varchar',
            'len' => '150',
            'comment' => 'additonal attention field for the address',
            'group' => 'address',
            'merge_filter' => 'enabled',
        ),
        'address_city' => array(
            'name' => 'address_city',
            'vname' => 'LBL_PRIMARY_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The city used for billing address',
            'group' => 'address',
            'merge_filter' => 'enabled',
        ),
        'address_district' => array(
            'name' => 'address_district',
            'vname' => 'LBL_PRIMARY_ADDRESS_DISTRICT',
            'type' => 'varchar',
            'len' => 100,
            'group' => 'address',
            'comment' => 'The district used for the billing address',
        ),
        'address_state' => array(
            'name' => 'address_state',
            'vname' => 'LBL_PRIMARY_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'address',
            'comment' => 'The state used for billing address',
            'merge_filter' => 'enabled',
        ),
        'address_postalcode' => array(
            'name' => 'address_postalcode',
            'vname' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'address',
            'comment' => 'The postal code used for billing address',
            'merge_filter' => 'enabled',
        ),
        'address_pobox' => array(
            'name' => 'address_pobox',
            'vname' => 'LBL_PRIMARY_ADDRESS_POBOX',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'address',
            'comment' => 'The pobox used for billing address',
            'merge_filter' => 'enabled',
        ),
        'address_country' =>
            array(
                'name' => 'address_country',
                'vname' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
                'type' => 'varchar',
                'group' => 'address',
                'comment' => 'The country used for the billing address',
                'merge_filter' => 'enabled',
            ),
        'address_latitude' =>
            array(
                'name' => 'address_latitude',
                'vname' => 'LBL_PRIMARY_ADDRESS_LATITUDE',
                'type' => 'double',
                'group' => 'address'
            ),
        'address_longitude' => array(
            'name' => 'address_longitude',
            'vname' => 'LBL_PRIMARY_ADDRESS_LONGITUDE',
            'type' => 'double',
            'group' => 'address'
        ),
        'address_georesult' => array(
            'name' => 'address_georesult',
            'vname' => 'LBL_PRIMARY_ADDRESS_GEORESULT',
            'type' => 'text'
        ),
        // ordering party
        'account_op_id' => array(
            'name' => 'account_op_id',
            'vname' => 'LBL_ACCOUNT_OP_ID',
            'type' => 'id',
            'audited' => true,
            'comment' => 'ordering party'
        ),
        'account_op_name' => array(
            'name' => 'account_op_name',
            'rname' => 'name',
            'id_name' => 'account_op_id',
            'vname' => 'LBL_ACCOUNTOP',
            'type' => 'relate',
            'link' => 'accountsop',
            'isnull' => 'true',
            'table' => 'accounts',
            'module' => 'Accounts',
            'source' => 'non-db',
        ),
        'accountsop' => array(
            'name' => 'accountsop',
            'type' => 'link',
            'vname' => 'LBL_ORDERDERING_PARTY',
            'relationship' => 'serviceorder_accountsop',
            'module' => 'Accounts',
            'source' => 'non-db',
        ),
        //account recipient party
        'account_rp_id' => array(
            'name' => 'account_rp_id',
            'vname' => 'LBL_ACCOUNT_RP_ID',
            'type' => 'id',
            'comment' => 'receiving party'
        ),
        'account_rp_name' => array(
            'name' => 'account_rp_name',
            'rname' => 'name',
            'id_name' => 'account_rp_id',
            'vname' => 'LBL_ACCOUNTRP',
            'type' => 'relate',
            'link' => 'accountsrp',
            'isnull' => 'true',
            'table' => 'accounts',
            'module' => 'Accounts',
            'source' => 'non-db',
        ),
        'accountsrp' => array(
            'name' => 'accountsrp',
            'type' => 'link',
            'vname' => 'LBL_ACCOUNTRP',
            'relationship' => 'serviceorder_accountsrp',
            'module' => 'Accounts',
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
            'join_name' => 'contacts'
        ),
        'contacts' => array(
            'vname' => 'LBL_CONTACTS',
            'name' => 'contacts',
            'type' => 'link',
            'module' => 'Contacts',
            'relationship' => 'serviceorders_contacts',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db'
        ),
        'email1' => array(
            'name' => 'email1',
            'vname' => 'LBL_EMAIL1',
            'source' => 'non-db',
            'required' => false
        ),
        'customer_type' => array(
            'name' => 'customer_type',
            'vname' => 'LBL_CUSTOMER_TYPE',
            'type' => 'enum',
            'length' => 1,
            'options' => 'customer_type_dom',
            'default' => 'B',
            'required' => true,
            'comment' => 'the type of the customer if this is a Business or Private order'
        ),
        //serviceticket
        'serviceticket_id' => array(
            'name' => 'serviceticket_id',
            'vname' => 'LBL_SERVICETICKET_ID',
            'type' => 'id',
        ),
        'serviceticket_name' => array(
            'name' => 'serviceticket_name',
            'vname' => 'LBL_SERVICETICKET',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'serviceticket_id',
            'rname' => 'name',
            'db_concat_fields' => array(0 => 'serviceticket_number', 1 => 'name'),
            'module' => 'ServiceTickets',
            'link' => 'servicetickets',
            'join_name' => 'servicetickets',
        ),
        'servicetickets' => array(
            'vname' => 'LBL_SERVICETICKETS',
            'name' => 'servicetickets',
            'type' => 'link',
            'module' => 'ServiceTickets',
            'relationship' => 'serviceorder_parent_serviceticket',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'date_start' => array(
            'name' => 'date_start',
            'vname' => 'LBL_DATE_START',
            'type' => 'datetime',
            'comment' => 'Date of start of maintenance'
        ),
        'date_end' => array(
            'name' => 'date_end',
            'vname' => 'LBL_DATE_END',
            'type' => 'datetime',
            'comment' => 'Date maintenance ends'
        ),
        'duration_hours' => array(
            'name' => 'duration_hours',
            'vname' => 'LBL_DURATION_HOURS',
            'type' => 'int',
            'group' => 'duration',
            'len' => '3',
            'source' => 'non-db' // CR1000436
        ),
        'duration_minutes' => array(
            'name' => 'duration_minutes',
            'vname' => 'LBL_DURATION_MINUTES',
            'type' => 'int',
            'group' => 'duration',
            'len' => '2',
            'source' => 'non-db' // CR1000436
        ),
        'date_start_confirmed' => array(
            'name' => 'date_start_confirmed',
            'vname' => 'LBL_DATE_START_CONFIRMED',
            'type' => 'datetime',
            'comment' => 'confirmed start Date'
        ),
        'date_end_confirmed' => array(
            'name' => 'date_end_confirmed',
            'vname' => 'LBL_DATE_END_CONFIRMED',
            'type' => 'datetime',
            'comment' => 'confirmed end Date'
        ),
        'signature_contact' => array(
            'name' => 'signature_contact',
            'vname' => 'LBL_SIGNATURE_CONTACT',
            'type' => 'canvadraw',
            'dbType' => 'text',
            'comment' => 'Signature of related contact drawn on tablet',
        ),
//        'signature_assigned_user' => array(
//            'name' => 'signature_assigned_user',
//            'vname' => 'LBL_SIGNATURE_ASSIGNED_USER',
//            'type' => 'canvadraw',
//            'dbType' => 'text',
//            'comment' => 'Signature of techy',
//        ),
        'serviceorder_status' => array(
            'name' => 'serviceorder_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'serviceorder_status_dom',
            'comment' => 'Status (ex: new, planned, scheduled, completed, cancelled)',
        ),
        'serviceorder_number' => array(
            'name' => 'serviceorder_number',
            'vname' => 'LBL_ORDER_NUMBER',
            'type' => 'varchar',
            'len' => 10,
            'comment' => 'Number',
        ),
        'servicefeedbacks' => array(
            'vname' => 'LBL_SERVICEFEEDBACKS',
            'name' => 'servicefeedbacks',
            'type' => 'link',
            'module' => 'ServiceFeedbacks',
            'relationship' => 'servicefeedbacks_serviceorders',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        //serviceequipment
        'serviceequipments' => array(
            'vname' => 'LBL_SERVICEEQUIPMENT',
            'name' => 'serviceequipments',
            'type' => 'link',
            'module' => 'ServiceEquipments',
            'relationship' => 'serviceorders_serviceequipments',
            'source' => 'non-db',
            'default' => true
        ),
        'serviceorderitems' => array(
            'name' => 'serviceorderitems',
            'type' => 'link',
            'relationship' => 'serviceorders_serviceorderitems',
            'source' => 'non-db',
            'module' => 'ServiceOrderItems',
            'default' => true,
            'sort' => array(
                'sortfield' => 'itemnr',
                'sortdirection' => 'ASC'
            )
        ),
        'serviceorderefforts' => array(
            'name' => 'serviceorderefforts',
            'type' => 'link',
            'relationship' => 'serviceorder_serviceorderefforts',
            'source' => 'non-db',
            'module' => 'ServiceOrderEfforts',
            'default' => true,
            'sort' => array(
                'sortfield' => 'itemnr',
                'sortdirection' => 'ASC'
            )
        ),
        'users' => array(
            'name' => 'users',
            'type' => 'link',
            'relationship' => 'serviceorders_users',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
            'rel_fields' => array(
                'user_role' => array(
                    'map' => 'serviceorder_user_role'
                )
            )
        ),
        'servicelocation_id' => array(
            'name' => 'servicelocation_id',
            'vname' => 'LBL_SERVICELOCATION_ID',
            'type' => 'id',
            'comment' => 'servicelocation id',
        ),
        'servicelocation_name' => array(
            'name' => 'servicelocation_name',
            'vname' => 'LBL_SERVICELOCATION',
            'type' => 'relate',
            'source' => 'non-db',
            'module' => 'ServiceLocations',
            'link' => 'servicelocations',
            'join_name' => 'servicelocations',
            'id_name' => 'servicelocation_id',
            'rname' => 'name',
            'required' => true,
            'comment' => 'servicelocation name',
        ),
        'servicelocations' => array(
            'name' => 'servicelocations',
            'vname' => 'LBL_SERVICELOCATIONS',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'servicelocation_serviceorders',
            'comment' => '',
        ),
        //maintenance contract SalesDoc relationship
        'maintenance_contract_id' => array(
            'name' => 'maintenance_contract_id',
            'vname' => 'LBL_MAINTENANCE_CONTRACT_ID',
            'type' => 'id'
        ),
        'maintenance_contract_name' => array(
            'name' => 'maintenance_contract_name',
            'rname' => 'name',
            'id_name' => 'maintenance_contract_id',
            'vname' => 'LBL_MAINTENANCE_CONTRACT',
            'type' => 'relate',
            'link' => 'maintenance_contract',
            'isnull' => 'true',
            'table' => 'salesdocs',
            'module' => 'SalesDocs',
            'source' => 'non-db',
        ),
        'maintenance_contract' => array(
            'name' => 'maintenance_contract',
            'type' => 'link',
            'vname' => 'LBL_MAINTENANCE_CONTRACT',
            'relationship' => 'serviceorder_maintenance_contract',
            'module' => 'SalesDocs',
            'source' => 'non-db',
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_LIST_RELATED_TO_ID',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => false,
            'comment' => 'The ID of the parent Sugar object identified by parent_type'
        ),
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'required' => false,
            'group' => 'parent_name',
            'options' => 'parent_type_display_serviceorder',
            'len' => 255,
            'comment' => 'The Sugar object to which the call is related',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'parent_type' => 'record_type_display_serviceorder',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_RELATED_TO',
            'type' => 'parent',
            'group' => 'parent_name',
            'source' => 'non-db',
            'options' => 'parent_type_display_serviceorder',
        ),
        'accounts' => array(
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'serviceorders_accounts_add',
            'source' => 'non-db',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'rel_fields' => array(
                'account_role' => array(
                    'type' => 'enum',
                    'options' => 'serviceorders_accounts_roles_dom',
                    'map' => 'serviceorder_role'
                )
            ),
            'vname' => 'LBL_ACCOUNTS'
        )
    ),
    'relationships' => array(
        'serviceorder_accountsop' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrders',
            'rhs_table' => 'serviceorders',
            'rhs_key' => 'account_op_id',
            'relationship_type' => 'one-to-many'
        ),
        'serviceorder_accountsrp' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrders',
            'rhs_table' => 'serviceorders',
            'rhs_key' => 'account_rp_id',
            'relationship_type' => 'one-to-many'
        ),
        'serviceorders_contacts' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrders',
            'rhs_table' => 'serviceorders',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many'
        ),
        'salesdocs' => array(
            'name' => 'salesdocs',
            'type' => 'link',
            'relationship' => 'salesdocs_serviceorders_parent',
            'module' => 'SalesDocs',
            'bean_name' => 'SalesDoc',
            'source' => 'non-db',
            'vname' => 'LBL_SALESDOCS',
        ),
        'serviceorder_maintenance_contract' => array(
            'lhs_module' => 'SalesDocs',
            'lhs_table' => 'salesdocs',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrders',
            'rhs_table' => 'serviceorders',
            'rhs_key' => 'maintenance_contract_id',
            'relationship_type' => 'one-to-many'
        ),
        'serviceorder_parent_salesdoc' => array(
            'lhs_module' => 'SalesDocs',
            'lhs_table' => 'salesdocs',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrders',
            'rhs_table' => 'serviceorders',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'SalesDocs'
        ),
        'serviceorder_parent_serviceticket' => array(
            'lhs_module' => 'ServiceTickets',
            'lhs_table' => 'servicetickets',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrders',
            'rhs_table' => 'serviceorders',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ServiceTickets'
        ),
    ),
    'indices' => array(
        array('name' => 'idx_serviceorder_accopid', 'type' => 'index', 'fields' => array('account_op_id')),
        array('name' => 'idx_serviceorder_accrpid', 'type' => 'index', 'fields' => array('account_rp_id')),
        array('name' => 'idx_serviceorder_conid', 'type' => 'index', 'fields' => array('contact_id')),
        array('name' => 'idx_serviceorder_ticid', 'type' => 'index', 'fields' => array('serviceticket_id')),
        array('name' => 'idx_serviceorder_accconticdel', 'type' => 'index', 'fields' => array('account_op_id', 'contact_id', 'serviceticket_id', 'deleted')),
    )
);

VardefManager::createVardef('ServiceOrders', 'ServiceOrder', array('default', 'assignable', 'activities'));


//name is not required. Overwrite default
//set global else error with PHP7.1: Uncaught Error: Cannot use string offset as an array
global $dictionary;
$dictionary['ServiceOrder']['fields']['name']['required'] = false;
