<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Product'] = array(
    'table' => 'products',
    'fields' => array(
        'ext_id' => array (
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => 50
        ),
        'productgroup_id' => array(
            'name' => 'productgroup_id',
            'vname' => 'LBL_PRODUCTGROUP_ID',
            'type' => 'id',
            'comment' => 'Eindeutige SugarID der Produktgruppe'
        ),
        'productgroup_name' => array(
            'name' => 'productgroup_name',
            'rname' => 'name',
            'id_name' => 'productgroup_id',
            'vname' => 'LBL_PRODUCTGROUP',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'ProductGroups',
            'table' => 'productgroups',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'productgroup',
            'unified_search' => true,
            'importable' => 'true',
        ),
        'product_name' => array(
            'vname' => 'LBL_PRODUCT',
            'join_name' => 'products',
            'type' => 'relate',
            'link' => 'products',
            'table' => 'products',
            'isnull' => 'true',
            'module' => 'Products',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'price' => array(
            'name' => 'price',
            'vname' => 'LBL_PRICE',
            'type' => 'double',
        ),
        'std_price'=> array (
            'name' => 'std_price',
            'vname' => 'LBL_STD_PRICE',
            'type' => 'double',
            'len' => 7
        ),
        'purchase_price'=> array (
            'name' => 'purchase_price',
            'vname' => 'LBL_PURCHASE_PRICE',
            'type' => 'double',
            'len' => 7
        ),
        'net_weight' => array(
            'name' => 'net_weight',
            'vname' => 'LBL_NET_WEIGHT',
            'type' => 'double',
        ),
        'gross_weight' => array(
            'name' => 'gross_weight',
            'vname' => 'LBL_GROSS_WEIGHT',
            'type' => 'double',
        ),
        'base_uom_id' => array(
            'name' => 'base_uom_id',
            'type' => 'id',
        ),
        'product_status' => array(
            'name' => 'product_status',
            'vname' => 'LBL_PRODUCT_STATUS',
            'type' => 'enum',
            'options' => 'product_status_dom'
        ),
        'manufacturer_id' => array(
            'name' => 'manufacturer_id',
            'type' => 'id',
        ),
        'manufacturer_name' => [
            'name' => 'manufacturer_name',
            'rname' => 'name',
            'id_name' => 'manufacturer_id',
            'vname' => 'LBL_MANUFACTURER',
            'type' => 'relate',
            'module' => 'Accounts',
            'table' => 'accounts',
            'isnull' => 'true',
            'dbType' => 'varchar',
            'len' => '255',
            'link' => 'manufacturer',
            'source' => 'non-db'
        ],
        'base_uom' => [
            'name' => 'base_uom',
            'rname' => 'label',
            'id_name' => 'base_uom_id',
            'vname' => 'LBL_BASE_UNIT_OF_MEASURE',
            'type' => 'relate',
            'module' => 'UOMUnits',
            'table' => 'uomunits',
            'isnull' => 'true',
            'dbType' => 'varchar',
            'len' => '255',
            'link' => 'uomunits',
            'source' => 'non-db'
        ],
        'uomconversions' => array(
            'name' => 'uomconversions',
            'vname' => 'LBL_UNIT_OF_MEASURE_CONVERSION',
            'type' => 'link',
            'relationship' => 'product_uomconversions',
            'module' => 'UOMConversions',
            'default' => true,
            'source' => 'non-db'

        ),
        'manufacturer' => array(
            'name' => 'manufacturer',
            'type' => 'link',
            'relationship' => 'manufacturer_products',
            'source' => 'non-db',
            'module' => 'Accounts',
            'vname' => 'LBL_ACCOUNT'
        ),
        'uomunits' => array(
            'name' => 'uomunits',
            'type' => 'link',
            'relationship' => 'uomunit_products',
            'source' => 'non-db',
            'module' => 'UOMUnits',
            'vname' => 'LBL_UNIT_OF_MEASURE'
        ),
        'productgroup' => array(
            'name' => 'productgroup',
            'vname' => 'LBL_PRODUCTGROUP',
            'type' => 'link',
            'relationship' => 'productgroup_products',
            'source' => 'non-db'
        ),
        'left_node_id' => array(
            'name' => 'left_node_id',
            'vname' => 'LBL_LEFT_NODE_ID',
            'source' => 'non-db',
            'type' => 'relate',
            'link' => 'productgroup',
            'id_name' => 'productgroup_id',
            'module' => 'ProductGroups',
            'rname' => 'left_node_id'
        ),
        'right_node_id' => array(
            'name' => 'right_node_id',
            'vname' => 'LBL_RIGHT_NODE_ID',
            'source' => 'non-db',
            'type' => 'relate',
            'link' => 'productgroup',
            'id_name' => 'productgroup_id',
            'module' => 'ProductGroups',
            'rname' => 'right_node_id'
        ),
        'productvariants' => array(
            'name' => 'productvariants',
            'vname' => 'LBL_PRODUCTVARIANTS',
            'type' => 'link',
            'relationship' => 'product_productvariants',
            'source' => 'non-db'
        ),
        'productattributevalues' => array(
            'name' => 'productattributevalues',
            'vname' => 'LBL_PRODUCTATTRIBUTEVALUES',
            'type' => 'link',
            'relationship' => 'product_productattributevalues',
            'source' => 'non-db',
            'module' => 'ProductAttributeValues',
            'default' => true
        ),
        'spicetexts' => array(
            'name' => 'spicetexts',
            'type' => 'link',
            'relationship' => 'product_spicetexts',
            'module' => 'SpiceTexts',
            'source' => 'non-db',
            'vname' => 'LBL_SPICE_TEXTS',
        ),
        'serviceorderitems' => array(
            'name' => 'serviceorderitems',
            'type' => 'link',
            'vname' => 'LBL_SERVICE_ORDER_ITEMS',
            'relationship' => 'product_serviceorderitems_parent',
            'module' => 'ServiceOrderItems',
            'source' => 'non-db',
        ),
        'serviceorderefforts' => array(
            'name' => 'serviceorderefforts',
            'type' => 'link',
            'vname' => 'LBL_SERVICE_ORDER_ITEMS',
            'relationship' => 'product_serviceorderefforts_parent',
            'module' => 'ServiceOrderEfforts',
            'source' => 'non-db',
        ),
        'product_image' => [
            'name' => 'product_image',
            'vname' => 'LBL_IMAGE',
            'type' => 'image',
            'dbType' => 'longtext',
            'maxWidth' => 300,
            'maxHeight' => 300
        ],
        'outputtemplate_id' => array(
            'name' => 'outputtemplate_id',
            'type' => 'char',
            'len' => 36,
            'dbType' => 'id',
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
            'rname' => 'name',
            'link' => 'outputtemplate',
        ),
        'outputtemplate' => array(
            'name' => 'outputtemplate',
            'type' => 'link',
            'relationship' => 'outputtemplates_products',
            'link_type' => 'one',
            'side' => 'left',
            'source' => 'non-db',
            'vname' => 'LBL_OUTPUTTEMPLATES',
        )
    ),
    'relationships' => array(
        'product_productvariants' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductVariants',
            'rhs_table' => 'productvariants',
            'rhs_key' => 'product_id',
            'relationship_type' => 'one-to-many',
        ),
        'product_productattributevalues' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductAttributeValues',
            'rhs_table' => 'productattributevalues',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Products',
        ),
        'product_uomconversions' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'UOMConversions',
            'rhs_table' => 'uomconversions',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Products',
        ),
        'product_spicetexts' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'SpiceTexts',
            'rhs_table' => 'spicetexts',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Products'
        ),
        'product_serviceorderitems_parent' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderItems',
            'rhs_table' => 'serviceorderitems',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Products'
        ),
        'product_serviceorderefforts_parent' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderEfforts',
            'rhs_table' => 'serviceorderserviceorderefforts',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Products'
        ),
        'outputtemplates_products' => [
            'lhs_module' => 'OutputTemplates',
            'lhs_table' => 'outputtemplates',
            'lhs_key' => 'id',
            'rhs_module' => 'Products',
            'rhs_table' => 'products',
            'rhs_key' => 'outputtemplate_id',
            'relationship_type' => 'one-to-many'
        ]
    ),
    'indices' => array(
        array('name' => 'idx_products_manu_del', 'type' => 'index', 'fields' => array('manufacturer_id', 'deleted')),
        array('name' => 'idx_products_prodgrpid_del', 'type' => 'index', 'fields' => array('productgroup_id', 'deleted')),
        array('name' => 'idx_products_status_del', 'type' => 'index', 'fields' => array('product_status', 'deleted')),
        array('name' => 'idx_products_baseuomid_del', 'type' => 'index', 'fields' => array('base_uom_id', 'deleted')),
        array('name' => 'idx_products_ext_id_del', 'type' => 'index', 'fields' => array('ext_id', 'deleted')),

    ),
);

VardefManager::createVardef('Products', 'Product', array('default', 'assignable'));
