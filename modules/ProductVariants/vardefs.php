<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProductVariant'] = array(
    'table' => 'productvariants',
    'fields' => array(
        'ext_id' => array (
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => 50
        ),
        'product_id' => array (
            'name' => 'product_id',
            'vname' => 'LBL_PRODUCT_ID',
            'type' => 'id',
            'comment' => 'Eindeutige SugarID des Produktes'
        ),
        'products' => array (
            'name' => 'products',
            'vname' => 'LBL_PRODUCT',
            'type' => 'link',
            'relationship' => 'product_productvariants',
            'source' => 'non-db'
        ),
        'product_name' => array(
            'name' => 'product_name',
            'rname' => 'name',
            'id_name' => 'product_id',
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
        'product_status' => array(
            'name' => 'product_status',
            'vname' => 'LBL_PRODUCT_STATUS',
            'type' => 'enum',
            'options' => 'product_status_dom'
        ),
        'base_uom_id' => array(
            'name' => 'base_uom_id',
            'type' => 'id',
        ),
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
            'relationship' => 'productvariant_uomconversions',
            'module' => 'UOMConversions',
            'default' => true,
            'source' => 'non-db'

        ),
        'uomunits' => array(
            'name' => 'uomunits',
            'type' => 'link',
            'relationship' => 'uomunit_productvariants',
            'source' => 'non-db',
            'module' => 'UOMUnits',
            'vname' => 'LBL_UNIT_OF_MEASURE'
        ),
        'productattributevalues' => array (
            'name' => 'productattributevalues',
            'vname' => 'LBL_PRODUCTATTRIBUTEVALUES',
            'type' => 'link',
            'relationship' => 'productvariant_productattributevalues',
            'source' => 'non-db',
            'module' => 'ProductAttributeValues',
            'default' => true
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
        'product_type'=> array (
            'name' => 'product_type',
            'vname' => 'LBL_PRODUCT_TYPE',
            'type' => 'enum',
            'options' => 'product_types_dom',
            'len' => 15
        ),
        'product_ocurrence'=> array (
            'name' => 'product_ocurrence',
            'vname' => 'LBL_PRODUCT_OCCURENCE',
            'type' => 'enum',
            'options' => 'product_occurence_dom',
            'len' => 15
        ),
        'manufacturer_id' => array(
            'name' => 'manufacturer_id',
            'vname' => 'LBL_MANUFACTURER_ID',
            'type' => 'varchar',
            'len' => 36
        ),
        'manufacturer_name' => array(
            'name' => 'manufacturer_name',
            'rname' => 'name',
            'id_name' => 'manufacturer_id',
            'vname' => 'LBL_MANUFACTURER',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'Accounts',
            'table' => 'accounts',
            'source' => 'non-db',
            'link' => 'manufacturer',
        ),
        'manufacturer' => array(
            'name' => 'manufacturer',
            'vname' => 'LBL_MANUFACTURER',
            'type' => 'link',
            'module' => 'Accounts',
            'relationship' => 'productvariant_manufacturer',
            'source' => 'non-db'
        ),
        'resellers' => array(
            'name' => 'resellers',
            'vname' => 'LBL_RESELLERS',
            'type' => 'link',
            'module' => 'Accounts',
            'relationship' => 'productvariants_resellers',
            'source' => 'non-db'
        ),
        'productgroup_id' => array(
            'name' => 'productgroup_id',
            'vname' => 'LBL_PRODUCTGROUP_ID',
            'type' => 'varchar',
            'len' => 36,
            'source' => 'non-db',
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
            'join_name' => 'productgroups',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'productgroup',
            'unified_search' => true,
            'importable' => 'true',
        ),
        'productgroup' => array(
            'name' => 'productgroup',
            'vname' => 'LBL_PRODUCTGROUP',
            'type' => 'link',
            'module' => 'ProductGroups',
            'relationship' => 'productvariants_productgroups',
            'source' => 'non-db'
        ),
        'spicetexts' => array(
            'name' => 'spicetexts',
            'type' => 'link',
            'relationship' => 'productvariant_spicetexts',
            'module' => 'SpiceTexts',
            'source' => 'non-db',
            'vname' => 'LBL_SPICE_TEXTS',
        ),
        'serviceorderitems' => array(
            'name' => 'serviceorderefforts',
            'type' => 'link',
            'vname' => 'LBL_SERVICE_ORDER_ITEMS',
            'relationship' => 'product_serviceorderefforts_parent',
            'module' => 'ServiceOrderEfforts',
            'source' => 'non-db',
        ),
        'serviceorderefforts' => array(
            'name' => 'serviceorderefforts',
            'type' => 'link',
            'vname' => 'LBL_SERVICE_ORDER_EFFORTS',
            'relationship' => 'productvariant_serviceorderefforts_parent',
            'module' => 'ServiceOrderEfforts',
            'source' => 'non-db',
        ),
        'productvariant_image' => [
            'name' => 'productvariant_image',
            'vname' => 'LBL_IMAGE',
            'type' => 'image',
            'dbType' => 'longtext',
            'maxWidth' => 300,
            'maxHeight' => 300
        ]
    ),
    'relationships' => array(
        'productvariant_uomconversions' => array(
            'lhs_module' => 'ProductVariants',
            'lhs_table' => 'productvariants',
            'lhs_key' => 'id',
            'rhs_module' => 'UOMConversions',
            'rhs_table' => 'uomconversions',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ProductVariants',
        ),
        'productvariant_productattributevalues' => array(
            'lhs_module' => 'ProductVariants',
            'lhs_table' => 'productvariants',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductAttributeValues',
            'rhs_table' => 'productattributevalues',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ProductVariants',
        ),
        'productvariants_productgroups' => array(
            'lhs_module' => 'ProductGroups',
            'lhs_table' => 'productgroups',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductVariants',
            'rhs_table' => 'productvariants',
            'rhs_key' => 'product_id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'products',
            'join_key_lhs' => 'productgroup_id',
            'join_key_rhs' => 'id'
        ),
        'productvariant_manufacturer' => array(
            'rhs_module' => 'ProductVariants',
            'rhs_table' => 'productvariants',
            'rhs_key' => 'manufacturer_id',
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ),
        'productvariant_spicetexts' => array(
            'lhs_module' => 'ProductVariants',
            'lhs_table' => 'productvariants',
            'lhs_key' => 'id',
            'rhs_module' => 'SpiceTexts',
            'rhs_table' => 'spicetexts',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ProductVariants'
        ),
        'productvariant_serviceorderitems_parent' => array(
            'lhs_module' => 'ProductVariants', 'lhs_table' => 'productvariants', 'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderItems', 'rhs_table' => 'serviceorderitems', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ProductVariants'
        ),
        'productvariant_serviceorderefforts_parent' => array(
            'lhs_module' => 'ProductVariants', 'lhs_table' => 'productvariants', 'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderEfforts', 'rhs_table' => 'serviceorderefforts', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ProductVariants'
        ),
    ),
    'indices' => array(
        array('name' => 'idx_product_id', 'type' => 'index', 'fields' => array('product_id')),
    ),
);

VardefManager::createVardef('ProductVariants', 'ProductVariant', array('default', 'assignable'));
