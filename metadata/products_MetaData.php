<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

// adding project-to-bugs relationship
$dictionary['productgroups_productattributes'] = array (
    'table' => 'productgroups_productattributes',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'productgroup_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'productattribute_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false)
    ),
    'indices' => array (
        array('name' => 'prod_proda_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_prod_prodg', 'type' =>'index', 'fields'=>array('productgroup_id')),
        array('name' => 'idx_prod_proda', 'type' =>'index', 'fields'=>array('productattribute_id'))
    ),
    'relationships' => array (
        'productgroups_productattributes' => array(
            'lhs_module' => 'ProductGroups',
            'lhs_table' => 'productgroups',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductAttributes',
            'rhs_table' => 'productattributes',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'productgroups_productattributes',
            'join_key_lhs' => 'productgroup_id',
            'join_key_rhs' => 'productattribute_id'
        )
    )
);

$dictionary['productvariants_resellers'] = array (
    'table' => 'productvariants_resellers',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'productvariant_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'account_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false)
    ),
    'indices' => array (
        array('name' => 'prodvar_resellers_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_prodvar_resellers_acc', 'type' =>'index', 'fields'=>array('account_id')),
        array('name' => 'idx_prodvar_resellers_pv', 'type' =>'index', 'fields'=>array('productvariant_id'))
    ),
    'relationships' => array (
        'productvariants_resellers' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductVariants',
            'rhs_table' => 'productvariants',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'productvariants_resellers',
            'join_key_lhs' => 'account_id',
            'join_key_rhs' => 'productvariant_id'
        )
    )
);
