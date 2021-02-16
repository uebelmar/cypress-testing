<?php


$i = 0;
$dictionary['SAPIdocConditions'] = array(
    'table' => 'sapidocconditions',
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
        'custom_node_function' => array(
            'name' => 'custom_node_function',
            'vname' => 'LBL_CUSTOM_NODE_FUNCTION',
            'type' => 'varchar',
            'len' => 255,
        ),
        'condition_field' => array(
            'name' => 'condition_field',
            'vname' => 'LBL_CONDITION_FIELD',
            'type' => 'varchar',
            'len' => 255,
        ),
        'condition_value' => array(
            'name' => 'condition_value',
            'vname' => 'LBL_CONDITION_VALUE',
            'type' => 'varchar',
            'len' => 255,
        ),
        'scope_segment_id' => array(
            'name' => 'scope_segment_id',
            'vname' => 'LBL_SCOPE_SEGMENT_ID',
            'type' => 'id',
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocconditionspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocconditions' . ( ++$i), 'type' => 'index', 'fields' => array('scope_segment_id')),
    ),
);

