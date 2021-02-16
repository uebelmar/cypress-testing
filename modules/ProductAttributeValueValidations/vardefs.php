<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProductAttributeValueValidation'] = array(
    'table' => 'productattributevaluevalidations',
    'fields' => array(
        'productattribute_id' => array (
            'name' => 'productattribute_id',
            'vname' => 'LBL_PRODUCTATTRIBUTE_ID',
            'type' => 'id'
        ),
        'productattribute_name' => array(
            'name' => 'productattribute_name',
            'rname' => 'name',
            'id_name' => 'productattribute_id',
            'vname' => 'LBL_PRODUCTATTRIBUTE_NAME',
            'type' => 'relate',
            'module' => 'ProductAttributes',
            'table' => 'productattributes',
            'massupdate' => false,
            'source' => 'non-db'
        ),
        'productattribute' => array (
            'name' => 'productattribute',
            'vname' => 'LBL_PRODUCTATTRIBUTES',
            'type' => 'link',
            'relationship' => 'productattribute_productattributevaluevalidations',
            'source' => 'non-db'
        ),
        'valueshort'=> array (
            'name'=>'valueshort',
            'vname'=>'LBL_VALUESHORT',
            'type'=>'varchar',
            'len'=>'255'
        ),
        'value'=> array (
            'name'=>'value',
            'vname'=>'LBL_VALUE',
            'type'=>'varchar',
            'len'=>'255'
        ),
        'value_from'=> array (
            'name'=>'value_from',
            'vname'=>'LBL_VALUE_FROM',
            'type'=>'varchar',
            'len'=>'255'
        ),
        'value_to'=> array (
            'name'=>'value_to',
            'vname'=>'LBL_VALUE_TO',
            'type'=>'varchar',
            'len'=>'255'
        )
    ),
    'relationships' => array(
    ),
    'indices' => array(
        array('name' => 'idx_prat_id', 'type' => 'index', 'fields' => array('productattribute_id')),
    )
);

VardefManager::createVardef('ProductAttributeValueValidations', 'ProductAttributeValueValidation', array('default', 'assignable'));
//BEGIN PHP7.1 compatibility: avoid PHP Fatal error:  Uncaught Error: Cannot use string offset as an array
global $dictionary;
//END
$dictionary['ProductAttributeValueValidation']['fields']['name']['required'] = false;
