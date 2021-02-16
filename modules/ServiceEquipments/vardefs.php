<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceEquipment'] = array(
    'table' => 'serviceequipments',
    'comment' => 'ServiceEquipments Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,

    'fields' => array(
        'equipment_status' => array(
            'name' => 'equipment_status',
            'vname' => 'LBL_STATUS',
            'options' => 'serviceequipment_status_dom',
            'type' => 'enum'
        ),
        'date_manufactured' => array(
            'name' => 'date_manufactured',
            'vname' => 'LBL_DATE_MANUFACTURED',
            'type' => 'date'
        ),
        'date_installed' => array(
            'name' => 'date_installed',
            'vname' => 'LBL_DATE_INSTALLED',
            'type' => 'date'
        ),
        'date_warranty_end' => array(
            'name' => 'date_warranty_end',
            'vname' => 'LBL_DATE_WARRANTY_END',
            'type' => 'date'
        ),
        'date_purchased' => array(
            'name' => 'date_purchased',
            'vname' => 'LBL_DATE_PURCHASED',
            'type' => 'date'
        ),
        'serialnr' => array(
            'name' => 'serialnr',
            'vname' => 'LBL_SERIALNR',
            'type' => 'varchar',
			'len' => 100,
        ),
        'equipment_model' => array(
            'name' => 'equipment_model',
            'vname' => 'LBL_MODEL',
            'type' => 'varchar',
            'len' => 255,
        ),
//        'address_street' => array(
//            'name' => 'address_street',
//            'vname' => 'LBL_ADDRESS_STREET',
//            'type' => 'varchar',
//            'len' => '150',
//        ),
//        'address_attn' => array(
//            'name' => 'address_attn',
//            'vname' => 'LBL_ADDRESS_ATTN',
//            'type' => 'varchar',
//            'len' => '150',
//        ),
//        'address_street_2' => array(
//            'name' => 'address_street_2',
//            'vname' => 'LBL_ADDRESS_STREET_2',
//            'type' => 'varchar',
//            'len' => '150',
//            'source' => 'non-db',
//        ),
//        'address_street_3' => array(
//            'name' => 'address_street_3',
//            'vname' => 'LBL_ADDRESS_STREET_3',
//            'type' => 'varchar',
//            'len' => '150',
//            'source' => 'non-db',
//        ),
//        'address_city' => array(
//            'name' => 'address_city',
//            'vname' => 'LBL_ADDRESS_CITY',
//            'type' => 'varchar',
//            'len' => '100',
//        ),
//        'address_state' => array(
//            'name' => 'address_state',
//            'vname' => 'LBL_ADDRESS_STATE',
//            'type' => 'varchar',
//            'len' => '100',
//        ),
//        'address_postalcode' =>
//            array(
//                'name' => 'address_postalcode',
//                'vname' => 'LBL_ADDRESS_POSTALCODE',
//                'type' => 'varchar',
//                'len' => '20',
//            ),
//        'address_country' => array(
//            'name' => 'address_country',
//            'vname' => 'LBL_ADDRESS_COUNTRY',
//            'type' => 'varchar',
//        ),
//        'address_latitude' => array(
//            'name' => 'address_latitude',
//            'vname' => 'LBL_ADDRESS_LATITUDE',
//            'type' => 'double',
//        ),
//        'address_longitude' => array(
//            'name' => 'address_longitude',
//            'vname' => 'LBL_ADDRESS_LONGITUDE',
//            'type' => 'double',
//        ),
        'counter' => [
            'name' => 'counter',
            'type' => 'varchar',
            'vname' => 'LBL_COUNTER',
            'comment' => 'Counter '
        ],
        'counter_unit' => [
            'name' => 'counter_unit',
            'type' => 'enum',
            'options' => 'counter_unit_dom',
            'vname' => 'LBL_COUNTER_UNIT',
            'comment' => 'Counter Unit (meters, hours ...)'
        ],
        'maintenance_cycle' => [
            'name' => 'maintenance_cycle',
            'type' => 'enum',
            'options' => 'maintenance_cycle_dom',
            'len' => 32,
            'vname' => 'LBL_MAINTENANCE_CYLE',
            'comment' => 'maintenance 1x, 2x, 3x a year or every second year'
        ],
        'inventory' => [
            'name' => 'inventory',
            'type' => 'bool',
            'default' => 0,
            'vname' => 'LBL_TO_INVENTORY',
            'comment' => 'flag for inventory'
        ],

        //account
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
            'module' => 'Accounts',
            'type' => 'link',
            'relationship' => 'serviceequipments_accounts',
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
        ),
        'contacts' => array(
            'name' => 'contacts',
            'vname' => 'LBL_CONTACT',
            'type' => 'link',
            'module' => 'Contacts',
            'relationship' => 'serviceequipment_contact',
            'link_type' => 'one',
            'source' => 'non-db'
        ),

// Not sure we need this at all.... commented for now
//        'externaltechnicians' => array(
//            'name' => 'externaltechnicians',
//            'vname' => 'LBL_EXTERNAL_TECHNICIANS',
//            'type' => 'link',
//            'module' => 'Contacts',
//            'relationship' => 'serviceequipments_contacts',
//            'link_type' => 'one',
//            'side' => 'right',
//            'source' => 'non-db'
//        ),
//        //users
//        'technicians' => array(
//            'name' => 'users',
//            'type' => 'link',
//            'relationship' => 'serviceequipments_users',
//            'source' => 'non-db',
//            'vname' => 'LBL_TECHNICIANS',
//            'module' => 'Users',
//            'default' => false
//        ),
        //servicetickets
        'servicetickets' => array(
            'vname' => 'LBL_SERVICETICKETS',
            'name' => 'servicetickets',
            'type' => 'link',
            'module' => 'ServiceTickets',
            'relationship' => 'serviceequipments_servicetickets',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        //serviceorders
        'serviceorders' => array(
            'vname' => 'LBL_SERVICEORDERS',
            'name' => 'serviceorders',
            'type' => 'link',
            'module' => 'ServiceOrders',
            'relationship' => 'serviceorders_serviceequipments',
            'source' => 'non-db'
        ),

        //servicelocation
        'servicelocation_id' => array(
            'name' => 'servicelocation_id',
            'vname' => 'LBL_SERVICELOCATION_ID',
            'type' => 'id',
        ),
        'servicelocation_name' => array(
            'name' => 'servicelocation_name',
            'vname' => 'LBL_SERVICELOCATION',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'servicelocation_id',
            'rname' => 'name',
            'module' => 'ServiceLocations',
            'link' => 'servicelocations',
            'join_name' => 'servicelocations'
        ),
        'servicelocations' => array(
            'vname' => 'LBL_SERVICELOCATIONS',
            'name' => 'servicelocations',
            'type' => 'link',
            'module' => 'ServiceLocations',
            'relationship' => 'servicelocations_serviceequipments',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        // salesdocs
        'salesdocs' => [
            'name' => 'salesdocs',
            'vname' => 'LBL_SALESDOCS',
            'type' => 'link',
            'source' => 'non-db',
            'module' => 'SalesDocs',
            'relationship' => 'salesdocs_serviceequipments',
            'default' => false
        ],

        // parent for product or productvariant
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'audited' => true
        ],
        'parent_type' => [
            'name'     => 'parent_type',
            'vname'    => 'LBL_PARENT_TYPE',
            'type'     => 'parent_type',
            'dbType'   => 'varchar',
            'required' => false,
            'group'    => 'parent_name',
            'options'  => 'serviceorderitem_parent_type_display',
            'len'      => 255,
            'comment'  => 'The bean to which the serviceequipment is related',
        ],
        'parent_name' => [
            'name'        => 'parent_name',
            'parent_type' => 'serviceorderitem_parent_type_display',
            'type_name'   => 'parent_type',
            'id_name'     => 'parent_id',
            'vname'       => 'LBL_PRODUCT',
            'type'        => 'parent',
            'group'       => 'parent_name',
            'source'      => 'non-db',
            'options'     => 'serviceorderitem_parent_type_display',
        ],
        'active_maintenance_contracts_exists' => [
            'name' => 'active_maintenance_contracts_exists',
            'type' => 'bool',
            'source' => 'non-db'
        ],
        'active_order_exists' => [
            'name' => 'active_order_exists',
            'vname' => 'LBL_ACTIVE_ORDER_EXISTS',
            'type' => 'bool',
            'source' => 'non-db'
        ],

    ),
    'relationships' => array(
        'serviceequipments_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceEquipments',
            'rhs_table' => 'serviceequipments',
            'rhs_key' => 'account_id',
            'relationship_type' => 'one-to-many'
        ),
        'serviceequipment_contact' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceEquipments',
            'rhs_table' => 'serviceequipments',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many'
        ),
        'serviceequipment_products' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceEquipments',
            'rhs_table' => 'serviceequipments',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many'
        ),
        'serviceequipment_productvariants' => array(
            'lhs_module' => 'ProductVariants',
            'lhs_table' => 'productvariants',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceEquipments',
            'rhs_table' => 'serviceequipments',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'indices' => array(
        array('name' => 'idx_serviceequipment_servlocid', 'type' => 'index', 'fields' => array('servicelocation_id')),
        array('name' => 'idx_serviceequipment_accid', 'type' => 'index', 'fields' => array('account_id')),
        array('name' => 'idx_serviceequipment_conid', 'type' => 'index', 'fields' => array('contact_id')),
        array('name' => 'idx_serviceequipment_acc', 'type' => 'index', 'fields' => array('account_id', 'contact_id', 'deleted')),
        array('name' => 'idx_serviceequipment_parentdel', 'type' => 'index', 'fields' => ['parent_id', 'parent_type', 'deleted']),

    )
);

VardefManager::createVardef('ServiceEquipments', 'ServiceEquipment', array('default', 'assignable', 'activities'));
