<?php


$i = 0;
$dictionary['SAPIdocRelationMessages'] = array(
    'table' => 'sapidocrelationmessages',
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
        'sapidocmessage_id' => array(
            'name' => 'sapidocmessage_id',
            'vname' => 'LBL_SAPIDOCMESSAGE_ID',
            'type' => 'id',
            'required' => false,
        ),
        'sapidocsegment_id' => array(
            'name' => 'sapidocsegment_id',
            'vname' => 'LBL_SAPIDOCSEGMENT_ID',
            'type' => 'id',
            'required' => false,
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocrelationmessagespk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocrelationmessages' . ( ++$i), 'type' => 'index', 'fields' => array('sapidocmessage_id')),
        array('name' => 'idx_sapidocrelationmessages' . ( ++$i), 'type' => 'index', 'fields' => array('sapidocsegment_id')),
    ),
);

