<?php


$i = 0;
$dictionary['SAPIdocMessages'] = array(
    'table' => 'sapidocmessages',
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
        'message_id' => array(
            'name' => 'message_id',
            'vname' => 'LBL_MESSAGE_ID',
            'type' => 'varchar',
            'len' => 25
        ),
        'message_number' => array(
            'name' => 'message_number',
            'vname' => 'LBL_MESSAGE_NUMBER',
            'type' => 'varchar',
            'len' => 25
        ),
        'message_type' => array(
            'name' => 'message_type',
            'vname' => 'LBL_MESSAGE_TYPE',
            'type' => 'varchar',
            'len' => 25
        ),
        'message_function' => array(
            'name' => 'message_function',
            'vname' => 'LBL_MESSAGE_FUNCTION',
            'type' => 'varchar',
            'len' => 255
        ),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text'
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocmessagespk', 'type' => 'primary', 'fields' => array('id')),
    ),
);

