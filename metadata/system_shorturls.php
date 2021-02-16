<?php

$dictionary['sysshorturls'] = [
    'table' => 'sysshorturls',
    'audited' => true,
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'isnull' => false,
            'required' => true
        ],
        'urlkey' => [
            'name' => 'urlkey',
            'type' => 'varchar',
            'len' => 20,
            'isnull' => false,
            'required' => true
        ],
        'route' => [
            'name' => 'route',
            'type' => 'varchar',
            'len' => 1000,
            'isnull' => false,
            'required' => true
        ],
        'active' => [
            'name' => 'active',
            'type' => 'bool',
            'default' => true,
            'isnull' => false,
            'required' => true
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => 0,
            'isnull' => false,
            'required' => true
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_sysshorturls',
            'type' => 'primary',
            'fields' => ['id']
        ],
        [
            'name' => 'idx_sysshorturls_urlkey',
            'type' => 'index',
            'fields' => ['urlkey']
        ],
        [
            'name' => 'idx_sysshorturls_active',
            'type' => 'index',
            'fields' => ['active']
        ],
        [
            'name' => 'idx_sysshorturls_deleted',
            'type' => 'index',
            'fields' => ['deleted']
        ]
    ]
];
