<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProductAttribute'] = array(
    'table' => 'productattributes',
    'fields' => array(
        'productgroups' => array (
            'name' => 'productgroups',
            'vname' => 'LBL_PRODUCTGROUPS',
            'type' => 'link',
            'relationship' => 'productgroups_productattributes',
            'source' => 'non-db'
        ),
        'technical_name' => array (
            'name' => 'technical_name',
            'vname' => 'LBL_TECHNICAL_NAME',
            'type' => 'varchar',
            'len' => 50
        ),
        'searchenabled' => array (
            'name' => 'searchenabled',
            'vname' => 'LBL_SEARCHENABLED',
            'type' => 'bool'
        ),
        'products' => array (
            'name' => 'products',
            'vname' => 'LBL_PRODUCTS',
            'type' => 'link',
            'relationship' => 'products_productattributes',
            'source' => 'non-db'
        ),
        'productvariants' => array (
            'name' => 'productvariants',
            'vname' => 'LBL_PRODUCTVARIANTS',
            'type' => 'link',
            'relationship' => 'productvariants_productattributes',
            'source' => 'non-db'
        ),
        'productattributevalues' => array (
            'name' => 'productattributevalues',
            'vname' => 'LBL_PRODUCTATTRIBUTEVALUES',
            'type' => 'link',
            'relationship' => 'productattribute_productattributevalues',
            'source' => 'non-db'
        ),
        'productattributevaluevalidations' => array (
            'name' => 'productattributevaluevalidations',
            'vname' => 'LBL_PRODUCTATTRIBUTEVALUEVALIDATIONS',
            'type' => 'link',
            'relationship' => 'productattribute_productattributevaluevalidations',
            'source' => 'non-db'
        ),
        'sort_sequence' => array (
            'name' => 'sort_sequence',
            'vname' => 'LBL_SORT_SEQUENCE',
            'type' => 'int',
            'default' => 99
        ),

    //=> from epim project.
        'prat_datatype' => array(
            'name' => 'prat_datatype',
            'vname' => 'LBL_DATATYPE',
            'type' => 'enum',
            'options' => 'productattributedatatypes_dom',
            'len' => '16',
            'comment' => 'di|f|n|s|vc',
        ),

        'prat_length' => array(
            'name' => 'prat_length',
            'vname' => 'LBL_LENGTH',
            'type' => 'int'
        ),

        'prat_precision' => array(
            'name' => 'prat_precision',
            'vname' => 'LBL_PRECISION',
            'type' => 'int'
        ),

        'prat_scale' => array(
            'name' => 'prat_scale',
            'vname' => 'LBL_SCALE',
            'type' => 'int'
        ),

        'prat_numberofvalues' => array(
            'name' => 'prat_numberofvalues',
            'vname' => 'LBL_PRAT_NUMBEROFVALUES',
            'type' => 'int'
        ),
        'uom' => array(
            'name' => 'uom',
            'vname' => 'LBL_UOM',
            'type' => 'varchar',
            'len' => 100
        ),
        'attr_usage' => array(
            'name' => 'attr_usage',
            'vname' => 'LBL_ATTR_USAGE',
            'type' => 'enum',
            'options' => 'productattribute_usage_dom',
            'len' => 32
        ),
        'attr_usagegrp' => array(
            'name' => 'attr_usagegrp',
            'vname' => 'LBL_ATTR_USAGEGROUP',
            'type' => 'varchar',
            'len' => 150
        ),
        'attr_displaygrp' => array(
            'name' => 'attr_displaygrp',
            'vname' => 'LBL_ATTR_DISPLAYGRP',
            'type' => 'varchar',
            'len' => 150
        )
    ),
    'relationships' => array(
        'productattribute_productattributevalues' => array(
            'lhs_module' => 'ProductAttributes',
            'lhs_table' => 'productattributes',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductAttributeValues',
            'rhs_table' => 'productattributevalues',
            'rhs_key' => 'productattribute_id',
            'relationship_type' => 'one-to-many',
        ),
        'productattribute_productattributevaluevalidations' => array(
            'lhs_module' => 'ProductAttributes',
            'lhs_table' => 'productattributes',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductAttributeValueValidations',
            'rhs_table' => 'productattributevaluevalidations',
            'rhs_key' => 'productattribute_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    'indices' => array(
    ),
);

VardefManager::createVardef('ProductAttributes', 'ProductAttribute', array('default', 'assignable'));
