<?php

$dictionary['sysgroupwarebeansyncqueue'] = [
    'table' => 'sysgroupwarebeansyncqueue',
    'fields' => [
        'id' => [
            'name'    => 'id',
            'vname'   => 'LBL_ID',
            'type'    => 'id',
            'comment' => 'Unique identifier',
        ],
        'bean_id' => [
            'name'     => 'bean_id',
            'vname'    => 'LBL_BEAN_ID',
            'type'     => 'id',
            'required' => true,
            'comment'  => 'Bean ID',
        ],
        'bean_type' => [
            'name'     => 'bean_type',
            'vname'    => 'LBL_BEAN_TYPE',
            'type'     => 'varchar',
            'len'      => 255,
            'required' => true,
            'comment'  => 'The type of the bean',
        ],
        'user_id' => [
            'name'     => 'user_id',
            'vname'    => 'LBL_USER_ID',
            'type'     => 'id',
            'comment'  => 'Exchange item ID',
        ],
        'date_entered' => [
            'name'  => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type'  => 'datetime',
        ],
    ],
    'indices' => [
        [
            'name' => 'sysgrbsqpk',
            'type' => 'primary',
            'fields' => ['id']
        ],
    ],
];
