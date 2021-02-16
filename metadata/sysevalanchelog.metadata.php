<?php
$dictionary['sysevalanchelog'] = array(
    'table' => 'sysevalanchelog',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'bean_id' => array(
            'name'     => 'bean_id',
            'vname'    => 'LBL_BEAN_ID',
            'type'     => 'id',
            'required' => false,
            'comment'  => 'Bean ID',
        ),
        'bean_type' => array(
            'name'     => 'bean_type',
            'vname'    => 'LBL_BEAN_TYPE',
            'type'     => 'varchar',
            'len'   => 255,
            'required' => false,
            ),
        'method'=> array(
            'name'     => 'method',
            'vname'    => 'LBL_METHOD',
            'type'     => 'varchar',
            'len'   => 255,
            'required' => false,
        ),
        'evalanche_id'=> array(
            'name'     => 'evalanche_id',
            'type'     => 'id',
            'required' => false,
            'comment'  => 'Evalanche ID',
        ),
        'response' => array(
            'name' => 'response',
            'type' => 'text',
            'comment'  => 'Response from Request to Evalanche',
        ),
        'soapfault' => array(
            'name' => 'soapfault',
            'type' => 'text',
            'comment'  => 'Error',
        ),
        'request_date' => array(
            'name' => 'request_date',
            'type'  => 'datetime',
            'comment'  => 'Date and time of sent request',
        )
    ),
    'indices' => array(
        array(
            'name' => 'sysevalanchelogpk',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);
