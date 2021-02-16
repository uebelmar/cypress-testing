<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceOrderItem'] = [
    'table' => 'serviceorderitems',
    'audited' => true,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,

    'fields' => [
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
            'comment'  => 'The bean to which the serviceorderitem is related',
        ],
        'parent_name' => [
            'name'        => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name'   => 'parent_type',
            'id_name'     => 'parent_id',
            'vname'       => 'LBL_RELATED_TO',
            'type'        => 'parent',
            'group'       => 'parent_name',
            'source'      => 'non-db',
            'options'     => 'serviceorderitem_parent_type_display',
        ],
        
        'contact_id' => [
            'name' => 'contact_id',
            'vname' => 'LBL_CONTACT_ID',
            'type' => 'varchar',
            'len' => 36,
            'audited' => true
        ],
        'contacts' => [
            'name' => 'contacts',
            'vname' => 'LBL_CONTACTS',
            'type' => 'link',
            'relationship' => 'contacts_serviceorderitems',
            'source' => 'non-db'
        ],
        'contact_name' => [
            'name' => 'contact_name',
            'rname' => 'name',
            'id_name' => 'contact_id',
            'vname' => 'LBL_CONTACTS',
            'join_name' => 'contacts',
            'type' => 'relate',
            'link' => 'contacts',
            'table' => 'contacts',
            'isnull' => 'true',
            'module' => 'Contacts',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ],
        'serviceorder_id' => [
            'name' => 'serviceorder_id',
            'vname' => 'LBL_SERVICEORDER_ID',
            'type' => 'id',
            'audited' => true,
        ],
        'itemnr' => [
            'name' => 'itemnr',
            'vname' => 'LBL_ITEM_NR',
            'type' => 'varchar',
            'len' => 5,
            'audited' => true
        ],
        'quantity' => [
            'name' => 'quantity',
            'vname' => 'LBL_QUANTITY',
            'type' => 'quantity',
            'dbtype' => 'double',
            'required' => false,
            'comment' => 'planned quantity'
        ],
        'confirmed_quantity' => [
            'name' => 'confirmed_quantity',
            'vname' => 'LBL_CONFIRMED_QUANTITY',
            'type' => 'quantity',
            'dbtype' => 'double',
            'required' => false,
            'comment' => 'confirmed quantity needed for the job'
        ],
        'serviceorderitem_status' => [
            'name' => 'serviceorderitem_status',
            'type' => 'enum',
            'options' => 'serviceorderitem_status_dom',
            'len' => '10',
            'vname' => 'LBL_STATUS'
        ],
        'feedback' => [
            'name' => 'feedback',
            'type' => 'mediumtext',
            'vname' => 'LBL_FEEDBACK'
        ],
        'uom_id' => [
            'name' => 'uom_id',
            'type' => 'id',
        ],
        'uom' => [
            'name' => 'uom',
            'rname' => 'label',
            'id_name' => 'uom_id',
            'vname' => 'LBL_UNIT_OF_MEASURE',
            'type' => 'relate',
            'module' => 'UOMUnits',
            'table' => 'uomunits',
            'isnull' => 'true',
            'dbType' => 'varchar',
            'len' => '255',
            'link' => 'uomunits',
            'source' => 'non-db'
        ],
        'uomunits' => [
            'name' => 'uomunits',
            'type' => 'link',
            'relationship' => 'uomunit_serviceorderitems',
            'source' => 'non-db',
            'module' => 'UOMUnits',
            'vname' => 'LBL_UNIT_OF_MEASURE'
        ],
        'product' => [
            'name' => 'product',
            'type' => 'link',
            'relationship' => 'product_serviceorderitems_parent',
            'source' => 'non-db',
            'module' => 'Products',
            'bean_name' => 'Product',
            'vname' => 'LBL_PRODUCT'
        ]
    ],
    'indices' => [
        ['name' => 'idx_serviceorderitems_parentdel', 'type' => 'index', 'fields' => ['parent_id', 'parent_type', 'deleted']],
        ['name' => 'idx_serviceorderitems_orderdel', 'type' => 'index', 'fields' => ['serviceorder_id', 'deleted']]
    ],

    'relationships' => [
        'contacts_serviceorderitems' => [
            'lhs_module' => 'ServiceOrderItems',
            'lhs_table' => 'serviceorderitems',
            'lhs_key' => 'contact_id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-many'
        ],
        'serviceorders_serviceorderitems' => [
            'lhs_module' => 'ServiceOrders',
            'lhs_table' => 'serviceorders',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderItems',
            'rhs_table' => 'serviceorderitems',
            'rhs_key' => 'serviceorder_id',
            'relationship_type' => 'one-to-many'
        ],
    ]
];


VardefManager::createVardef('ServiceOrderItems', 'ServiceOrderItem', ['default', 'assignable']);
