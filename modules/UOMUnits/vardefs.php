<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['UOMUnit'] = array(
    'table' => 'uomunits',
    'comment' => 'UOMUnits Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,

	'fields' => array(
        'name' => array(
            'name' => 'name',
            'type' => 'varchar',
            'len' => '255',
        ),
        'iso' => array (
            'name'  => 'iso',
            'vname' => 'LBL_ISO',
            'type'  => 'varchar',
            'len'   => '55',
            'required' => true
        ),
        'label' => array (
            'name'  => 'label',
            'vname' => 'LBL_NAME',
            'type'  => 'varchar',
            'len'   => '255',
            'required' => true
        ),
        'dimensions' => array (
            'name'  => 'dimensions',
            'vname' => 'LBL_DIMENSIONS',
            'type'  => 'enum',
            'options' => 'uom_unit_dimensions_dom',
        ),
        'main_unit' => array (
            'name'  => 'main_unit',
            'vname' => 'LBL_MAIN_UNIT',
            'type' => 'bool',
            'default' => '0',
        ),
        'nominator' => array (
            'name'  => 'nominator',
            'vname' => 'LBL_NOMINATOR',
            'type'  => 'varchar',
            'len'   => '55',
        ),
        'denominator' => array (
            'name'  => 'denominator',
            'vname' => 'LBL_DENOMINATOR',
            'type'  => 'varchar',
            'len'   => '55',
        ),
        'products' => array(
            'name' => 'products',
            'type' => 'link',
            'relationship' => 'uomunit_products',
            'module' => 'Products',
            'source' => 'non-db',
        ),
        'productvariants' => array(
            'name' => 'productvariants',
            'type' => 'link',
            'relationship' => 'uomunit_productvariants',
            'module' => 'ProductVariants',
            'source' => 'non-db',
        ),
	),
	'relationships' => array(
        'uomunit_products' => array(
            'lhs_module' => 'UOMUnits',
            'lhs_table' => 'uomunits',
            'lhs_key' => 'id',
            'rhs_module' => 'Products',
            'rhs_table' => 'products',
            'rhs_key' => 'base_uom_id',
            'relationship_type' => 'one-to-many',
        ),
        'uomunit_productvariants' => array(
            'lhs_module' => 'UOMUnits',
            'lhs_table' => 'uomunits',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductVariants',
            'rhs_table' => 'productvariants',
            'rhs_key' => 'base_uom_id',
            'relationship_type' => 'one-to-many',
        ),
        'uomunit_serviceorderitems' => array(
            'lhs_module' => 'UOMUnits',
            'lhs_table' => 'uomunits',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderItems',
            'rhs_table' => 'serviceorderitems',
            'rhs_key' => 'uom_id',
            'relationship_type' => 'one-to-many',
        ),
        'uomunit_serviceorderefforts' => array(
            'lhs_module' => 'UOMUnits',
            'lhs_table' => 'uomunits',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceOrderEfforts',
            'rhs_table' => 'serviceorderefforts',
            'rhs_key' => 'uom_id',
            'relationship_type' => 'one-to-many',
        ),
	),
	'indices' => array(
	)
);

VardefManager::createVardef('UOMUnits', 'UOMUnit', array('default', 'assignable'));
