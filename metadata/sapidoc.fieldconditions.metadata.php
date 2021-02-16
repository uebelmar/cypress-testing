<?php


$i = 0;
$dictionary['SAPIdocFieldConditions'] = array(
    'table' => 'sapidocfieldconditions',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'comment' => 'Unique identifier'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
        ),
        'field_id' => array(
            'name' => 'field_id',
            'vname' => 'LBL_FIELD_ID',
            'type' => 'id',
            'required' => false,
        ),
        'condition_id' => array(
            'name' => 'condition_id',
            'vname' => 'LBL_CONDITION_ID',
            'type' => 'id',
            'required' => false,
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocfieldconditionspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocfieldconditions' . ( ++$i), 'type' => 'index', 'fields' => array('field_id')),
        array('name' => 'idx_sapidocfieldconditions' . ( ++$i), 'type' => 'index', 'fields' => array('condition_id')),
    ),
);

