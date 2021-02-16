<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProductAttributeValue'] = array(
    'table' => 'productattributevalues',
    'fields' => array(
        'productattribute_id' => array (
            'name' => 'productattribute_id',
            'vname' => 'LBL_PRODUCTATTRIBUTE_ID',
            'type' => 'id',
        ),
        'productattribute' => array (
            'name' => 'productattribute',
            'vname' => 'LBL_PRODUCTATTRIBUTES',
            'type' => 'link',
            'relationship' => 'productattribute_productattributevalues',
            'source' => 'non-db'
        ),
        'parent_id'=> array (
            'name'=>'parent_id',
            'vname'=>'LBL_LIST_RELATED_TO_ID',
            'type'=>'id',
        ),
        'parent_type'=> array (
            'name'=>'parent_type',
            'vname'=>'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType'=>'varchar',
            'group'=>'parent_name',
            'len'=>255,
        ),

        //following fields from epim project. Here for now, we will see in the future if we need them all / or need others
        'pratvalue_ordernr'=> array(
            'name' => 'pratvalue_ordernr',
            'vname' => 'LBL_PRATVALUE_ORDERNR',
            'type' => 'int'
        ),
        'pratvalue_id' => array(
            'name' => 'pratvalue_id',
            'vname' => 'LBL_PRATVALUE_ID',
            'type' => 'long'
        ),
        'pratvalue' => array(
            'name' => 'pratvalue',
            'vname' => 'LBL_PRATVALUE',
            'type' => 'text'
        ),
        'valuedate' => array(
            'name' => 'valuedate',
            'vname' => 'LBL_VALUEDATE',
            'type' => 'datetime',
        ),
        'valueflag' => array(
            'name' => 'valueflag',
            'vname' => 'LBL_VALUEFLAG',
            'type' => 'bool'
        ),
        'dict_id' => array(
            'name' => 'dict_id',
            'vname' => 'LBL_DICT_ID',
            'type' => 'int'
        ),
        'sgse_id' => array(
            'name' => 'sgse_id',
            'vname' => 'LBL_SGSE_ID',
            'type' => 'int'
        ),
        'valuetxt' => array(
            'name' => 'valuetxt',
            'vname' => 'LBL_VALUETXT',
            'type' => 'text'
        ),
        'cgco_id' => array(
            'name' => 'cgco_id',
            'vname' => 'LBL_CGCO_ID',
            'type' => 'int'
        ),
        'ugun_id' => array(
            'name' => 'ugun_id',
            'vname' => 'LBL_UGUN_ID',
            'type' => 'int'
        ),
        'valuenum' => array(
            'name' => 'valuenum',
            'vname' => 'LBL_VALUENUM',
            'type' => 'double'
        )


    ),
    'relationships' => array(
    ),
    'indices' => array(
        array('name' => 'idx_prat_id', 'type' => 'index', 'fields' => array('productattribute_id')),
        array('name' => 'idx_pav_par_del', 'type' => 'index', 'fields' => array('parent_id', 'parent_type','deleted')),
        array('name' => 'idx_pratvalue_id', 'type' => 'index', 'fields' => array('pratvalue_id'))
    ),
);



VardefManager::createVardef('ProductAttributeValues', 'ProductAttributeValue', array('default', 'assignable'));
