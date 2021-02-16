<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['UOMConversion'] = array(
    'table' => 'uomconversions',
    'comment' => 'UOMConversions Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,

	'fields' => array(
        'parent_type' => array (
            'name'  => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type'  => 'varchar',
            'len'   => '255',
        ),
        'parent_id' => array (
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
        ),
        'uom_unit' => array (
            'name'  => 'uom_unit',
            'vname' => 'LBL_UNIT_OF_MEASURE',
            'type'  => 'varchar',
            'len'   => '255',
            'required' => true
        ),
        'reference_uom_unit' => array (
            'name'  => 'reference_uom_unit',
            'vname' => 'LBL_REFERENCE_UNIT_OF_MEASURE',
            'type'  => 'varchar',
            'len'   => '255',
            'required' => true
        ),
        'quantity' => array (
            'name'  => 'quantity',
            'vname' => 'LBL_QUANTITY',
            'type'  => 'varchar',
            'len'   => '255',
            'default' => '1',
            'required' => true
        ),
        'conversion_factor' => array (
            'name'  => 'conversion_factor',
            'vname' => 'LBL_CONVERSION_FACTOR',
            'type'  => 'varchar',
            'len'   => '255',
            'required' => true
        ),
        'products' => array(
            'name' => 'products',
            'type' => 'link',
            'relationship' => 'product_uomconversions',
            'module' => 'Products',
            'bean_name' => 'Product',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCTS',
        ),
        'productvariants' => array(
            'name' => 'productvariants',
            'type' => 'link',
            'relationship' => 'productvariant_uomconversions',
            'module' => 'ProductVariants',
            'bean_name' => 'ProductVariant',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCT_VARIANTS',
        ),
	),
	'relationships' => array(
	),
	'indices' => array(
        'parent_idx' => array('name' => 'parent_idx', 'type' => 'index', 'fields' => array('parent_id'),),
    )
);

VardefManager::createVardef('UOMConversions', 'UOMConversion', array('default', 'assignable'));
