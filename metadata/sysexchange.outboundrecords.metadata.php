<?php

$dictionary['ExchangeOutboundRecords'] = [
    'table' => 'sysexchangeoutboundrecords',
    'fields' => [
        'id' => [
            'name'       => 'id',
            'vname'      => 'LBL_ID',
            'type'       => 'id',
            'comment'    => 'Unique identifier',
        ],
        'requested_at' => [
            'name'  => 'requested_at',
            'vname' => 'LBL_REQUESTED_AT',
            'type'  => 'datetime',
        ],
        'deleted' => [
            'name'    => 'deleted',
            'vname'   => 'LBL_DELETED',
            'type'    => 'bool',
            'comment' => 'Deleted flag',
        ],
        'bean_id' => [
            'name'     => 'bean_id',
            'vname'    => 'LBL_BEAN_ID',
            'type'     => 'id',
            'required' => false,
            'comment'  => 'Bean ID',
        ],
        'bean_type' => [
            'name'     => 'bean_type',
            'vname'    => 'LBL_BEAN_TYPE',
            'type'     => 'varchar',
            'len'   => 255,
            'required' => false,
            'comment'  => 'The type of the bean',
        ],
        'exchange_id' => [
            'name'     => 'exchange_id',
            'vname'    => 'LBL_EXCHANGE_ID',
            'type'     => 'varchar',
            'len'   => '165',
            'required' => false,
            'comment'  => 'Exchange item ID',
        ],
        'request_type' => [
            'name'     => 'request_type',
            'vname'    => 'LBL_REQUEST_TYPE',
            'type'     => 'varchar',
            'len'   => 50,
            'required' => true,
            'comment'  => 'Request type: create, read, update or delete',
        ],
        'request_details' => [
            'name'     => 'request_details',
            'vname'    => 'LBL_REQUEST_DETAILS',
            'type'     => 'longtext',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name'			=> 'sysexchangeoutboundrecordspk',
            'type'			=> 'primary',
            'fields'		=> ['id']
        ],
        [
            'name'			=> 'idx_sysexchoutrec_beanid_del',
            'type'			=> 'index',
            'fields'		=> ['bean_id', 'deleted']
        ],
        [
            'name'			=> 'idx_sysexchoutrec_beantype',
            'type'			=> 'index',
            'fields'		=> ['bean_type']
        ],
        [
            'name'			=> 'idx_sysexchoutrec_exchid',
            'type'			=> 'index',
            'fields'		=> ['exchange_id']
        ]
    ],
];
