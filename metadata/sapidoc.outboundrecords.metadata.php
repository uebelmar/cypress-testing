<?php


$i = 0;
$dictionary['SAPIdocOutboundRecords'] = array(
    'table' => 'sapidocoutboundrecords',
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
        'proceeded' => array(
            'name' => 'proceeded',
            'vname' => 'LBL_PROCEEDED',
            'type' => 'bool',
        ),
        'added' => array(
            'name' => 'added',
            'vname' => 'LBL_ADDED',
            'type' => 'datetime',
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
        'triggered_bean_id' => array(
            'name' => 'triggered_bean_id',
            'vname' => 'LBL_TRIGGERED_BEAN_ID',
            'type' => 'id',
            'required' => false,
        ),
        'triggered_bean_type' => array(
            'name' => 'triggered_bean_type',
            'vname' => 'LBL_TRIGGERED_BEAN_TYPE',
            'type' => 'enum',
            'options' => 'parent_type_display',
            'required' => false,
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
        array('name' => 'sapidocoutboundrecordspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocoutboundrecords' . ( ++$i), 'type' => 'index', 'fields' => array('segment_id')),
        array('name' => 'idx_sapidocoutboundrecords' . ( ++$i), 'type' => 'index', 'fields' => array('sapidoc_id')),
        array('name' => 'idx_sapidocoutboundrecords' . ( ++$i), 'type' => 'index', 'fields' => array('bean_id')),
    ),
);

