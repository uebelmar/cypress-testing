<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceOrderEffort'] = [
    'table' => 'serviceorderefforts',
    'comment' => 'ServiceOrderEfforts Module',
    'audited' =>  false,
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
            'comment'  => 'The bean to which the serviceordereffort is related',
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
            'vname' => 'LBL_PLANNED_QUANTITY',
            'type' => 'quantity',
            'dbtype' => 'double',
            'required' => false,
            'comment' => 'planned time'
        ],
        'confirmed_quantity' => [
            'name' => 'confirmed_quantity',
            'vname' => 'LBL_CONFIRMED_QUANTITY',
            'type' => 'quantity',
            'dbtype' => 'double',
            'required' => false,
            'comment' => 'confirmed quantity needed for the job'
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
            'relationship' => 'uomunit_serviceorderefforts',
            'source' => 'non-db',
            'module' => 'UOMUnits',
            'vname' => 'LBL_UNIT_OF_MEASURE'
        ],
        'product' => [
            'name' => 'product',
            'type' => 'link',
            'relationship' => 'product_serviceorderefforts_parent',
            'source' => 'non-db',
            'module' => 'Products',
            'bean_name' => 'Product',
            'vname' => 'LBL_PRODUCT'
        ]
	],
	'relationships' => [
        'serviceorder_serviceorderefforts' => [
            'lhs_module' => 'ServiceOrders',
            'lhs_table' => 'serviceorders',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderEfforts',
            'rhs_table' => 'serviceorderefforts',
            'rhs_key' => 'serviceorder_id',
            'relationship_type' => 'one-to-many'
        ]
	],
	'indices' => [
        ['name' => 'idx_serviceorderefforts_parentdel', 'type' => 'index', 'fields' => ['parent_id', 'parent_type', 'deleted']],
        ['name' => 'idx_serviceorderefforts_orderdel', 'type' => 'index', 'fields' => ['serviceorder_id', 'deleted']],
    ]
];

VardefManager::createVardef('ServiceOrderEfforts', 'ServiceOrderEffort', ['default', 'assignable']);
