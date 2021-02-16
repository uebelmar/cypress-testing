<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProductGroup'] = array(
    'table' => 'productgroups',
    'fields' => array(
        'member_count' => array(
            'name' => 'member_count',
            'type' => 'int',
            'source' => 'non-db',
            'vname' => 'LBL_MEMBER_COUNT',
        ),
        'product_count' => array(
            'name' => 'product_count',
            'type' => 'int',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCT_COUNT',
        ),
        'left_node_id' => array(
            'name' => 'left_node_id',
            'vname' => 'LBL_LEFT_NODE_ID',
            'type' => 'int',
            'comment' => 'left value for modified treeorder traversal'
        ),
        'right_node_id' => array(
            'name' => 'right_node_id',
            'vname' => 'LBL_RIGHT_NODE_ID',
            'type' => 'int',
            'comment' => 'right value for modified treeorder traversal'
        ),
        'sortseq' => array(
            'name' => 'sortseq',
            'vname' => 'LBL_SORTSEQ',
            'type' => 'int'
        ),
        'sortparam' => array(
            'name' => 'sortparam',
            'vname' => 'LBL_SORTPARAM',
            'type' => 'varchar',
            'len' => '50'
        ),
        'external_id' => array(
            'name' => 'external_id',
            'vname' => 'LBL_EXTERNAL_ID',
            'type' => 'varchar',
            'len' => '50'
        ),
        'shorttext' => array(
            'name' => 'shorttext',
            'vname' => 'LBL_SHORTTEXT',
            'type' => 'varchar',
            'len' => '50'
        ),
        'productattributes' => array(
            'name' => 'productattributes',
            'vname' => 'LBL_PRODUCTATTRIBUTES',
            'type' => 'link',
            'relationship' => 'productgroups_productattributes',
            'source' => 'non-db',
        ),
        'productattributevalues' => array(
            'name' => 'productattributevalues',
            'vname' => 'LBL_PRODUCTATTRIBUTEVALUES',
            'type' => 'link',
            'relationship' => 'productgroup_productattributevalues',
            'source' => 'non-db',
        ),
        'parent_productgroup_id' =>
            array(
                'name' => 'parent_productgroup_id',
                'vname' => 'LBL_PARENT_PRODUCTGROUP_ID',
                'type' => 'varchar',
                'len' => 36,
                'required' => false,
                'audited' => true
            ),
        'parent_productgroup_name' => array(
            'name' => 'parent_productgroup_name',
            'rname' => 'name',
            'id_name' => 'parent_productgroup_id',
            'vname' => 'LBL_PARENT_PRODUCTGROUP',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'ProductGroups',
            'table' => 'productgroups',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'parent_productgroup'
        ),
        'parent_productgroup' => array(
            'name' => 'parent_productgroup',
            'type' => 'link',
            'relationship' => 'parent_productgroup',
            'module' => 'ProductGroups',
            'bean_name' => 'ProductGroup',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_PARENT_PRODUCTGROUP',
            'side' => 'right'
        ),
        'productgroups' => array(
            'name' => 'productgroups',
            'type' => 'link',
            'relationship' => 'parent_productgroup',
            'module' => 'ProductGroups',
            'bean_name' => 'ProductGroup',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCTGROUPS',
        ),
        'products' => array(
            'name' => 'products',
            'type' => 'link',
            'relationship' => 'productgroup_products',
            'module' => 'Products',
            'bean_name' => 'Product',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCTS',
        ),
        'productvariants' => array(
            'name' => 'productvariants',
            'type' => 'link',
            'relationship' => 'productvariants_productgroups',
            'module' => 'ProductVariants',
            'bean_name' => 'ProductVariant',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCTS',
        ),
        'spicetexts' => array(
            'name' => 'spicetexts',
            'type' => 'link',
            'relationship' => 'productgroup_spicetexts',
            'module' => 'SpiceTexts',
            'source' => 'non-db',
            'vname' => 'LBL_SPICE_TEXTS',
        ),
    ),
    'relationships' => array(
        'productgroup_productattributevalues' => array(
            'lhs_module' => 'ProductGroups',
            'lhs_table' => 'productgroups',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductAttributeValues',
            'rhs_table' => 'productattributevalues',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ProductGroups',
        ),
        'productgroup_products' => array(
            'lhs_module' => 'ProductGroups',
            'lhs_table' => 'productgroups',
            'lhs_key' => 'id',
            'rhs_module' => 'Products',
            'rhs_table' => 'products',
            'rhs_key' => 'productgroup_id',
            'relationship_type' => 'one-to-many',
        ),
        'parent_productgroup' => array(
            'lhs_module' => 'ProductGroups',
            'lhs_table' => 'productgroups',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductGroups',
            'rhs_table' => 'productgroups',
            'rhs_key' => 'parent_productgroup_id',
            'relationship_type' => 'one-to-many'
        ),
        'productgroup_spicetexts' => array(
            'lhs_module' => 'Products',
            'lhs_table' => 'products',
            'lhs_key' => 'id',
            'rhs_module' => 'SpiceTexts',
            'rhs_table' => 'spicetexts',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ProductGroups'
        )
    ),
    'indices' => array(
        array('name' => 'idx_productgroups_extid', 'type' => 'index', 'fields' => array('external_id')),
        array('name' => 'idx_productgroups_parengrpid_del', 'type' => 'index', 'fields' => array('parent_productgroup_id', 'deleted')),
    ),
);

VardefManager::createVardef('ProductGroups', 'ProductGroup', array('default', 'assignable'));
