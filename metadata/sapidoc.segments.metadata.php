<?php


$i = 0;
$dictionary['SAPIdocSegments'] = array(
    'table' => 'sapidocsegments',
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
        'active' => array(
            'name' => 'active',
            'vname' => 'LBL_ACTIVE',
            'type' => 'bool',
        ),
        'sap_segment' => array(
            'name' => 'sap_segment',
            'vname' => 'LBL_SAP_SEGMENT',
            'type' => 'varchar',
            'len' => 255,
        ),
        'sysmodule_id' => array(
            'name' => 'sysmodule_id',
            'vname' => 'LBL_SYSMODULE_ID',
            'type' => 'id',
            'required' => true
        ),
        'split_field' => array(
            'name' => 'split_field',
            'vname' => 'LBL_SPLIT_FIELD',
            'type' => 'varchar',
            'len' => 255
        ),
        'split_length' => array(
            'name' => 'split_length',
            'vname' => 'LBL_SPLIT_LENGTH',
            'type' => 'int'
        ),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text'
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocsegmentspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocsegments' . ( ++$i), 'type' => 'index', 'fields' => array('sysmodule_id')),
    ),
);

