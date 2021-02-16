<?php


$i = 0;
$dictionary['SAPIdocReceivedMessages'] = array(
    'table' => 'sapidocreceivedmessages',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'comment' => 'Unique identifier'
        ),
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
        ),
        'message' => array(
            'name' => 'message',
            'vname' => 'LBL_MESSAGE',
            'type' => 'text'
        ),
        'message_id' => array(
            'name' => 'message_id',
            'vname' => 'LBL_MESSAGE_ID',
            'type' => 'id',
            'required' => true,
        ),
        'sapidoc_id' => array(
            'name' => 'sapidoc_id',
            'vname' => 'LBL_SAPIDOC_ID',
            'type' => 'id',
            'required' => true,
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocreceivedmessagespk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocreceivedmessages' . ( ++$i), 'type' => 'index', 'fields' => array('message_id')),
        array('name' => 'idx_sapidocreceivedmessages' . ( ++$i), 'type' => 'index', 'fields' => array('sapidoc_id')),
    ),
);

