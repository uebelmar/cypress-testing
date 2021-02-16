<?php

$dictionary['ExchangeInboundRecords'] = [
    'table' => 'sysexchangeinboundrecords',
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
            'required' => true,
            'comment'  => 'Exchange item ID',
        ],
        'response_details' => [
            'name'     => 'response_details',
            'vname'    => 'LBL_RESPONSE_DETAILS',
            'type'     => 'longtext',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name'			=> 'sysexchangeinboundrecordspk',
            'type'			=> 'primary',
            'fields'		=> ['id']
        ],
        [
            'name'			=> 'idx_sysexchinrec_beanid_del',
            'type'			=> 'index',
            'fields'		=> ['bean_id', 'deleted']
        ],
        [
            'name'			=> 'idx_sysexchinrec_beantype',
            'type'			=> 'index',
            'fields'		=> ['bean_type']
        ],
        [
            'name'			=> 'idx_sysexchinrec_exchid',
            'type'			=> 'index',
            'fields'		=> ['exchange_id']
        ]
    ],
];
