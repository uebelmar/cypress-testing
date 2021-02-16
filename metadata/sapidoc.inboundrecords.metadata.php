<?php


$i = 0;
$dictionary['SAPIdocInboundRecords'] = array(
    'table' => 'sapidocinboundrecords',
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
        'handled' => array(
            'name' => 'handled',
            'vname' => 'LBL_HANDLED',
            'type' => 'datetime',
        ),
        'segment_id' => array(
            'name' => 'segment_id',
            'vname' => 'LBL_SEGMENT_ID',
            'type' => 'id',
            'required' => false,
        ),
        'sapidoc_id' => array(
            'name' => 'sapidoc_id',
            'vname' => 'LBL_SAPIDOC_ID',
            'type' => 'id',
            'required' => true,
        ),
        'bean_id' => array(
            'name' => 'bean_id',
            'vname' => 'LBL_BEAN_ID',
            'type' => 'id',
            'required' => false,
        ),
        'bean_type' => array(
            'name' => 'bean_type',
            'vname' => 'LBL_BEAN_TYPE',
            'type' => 'enum',
            'options' => 'parent_type_display',
            'required' => false,
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocinboundrecordspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocinboundrecords' . ( ++$i), 'type' => 'index', 'fields' => array('segment_id')),
        array('name' => 'idx_sapidocinboundrecords' . ( ++$i), 'type' => 'index', 'fields' => array('sapidoc_id')),
        array('name' => 'idx_sapidocinboundrecords' . ( ++$i), 'type' => 'index', 'fields' => array('bean_id')),
    ),
);

