<?php
$dictionary['sysexchangeuserconfig'] = [
    'table' => 'sysexchangeuserconfig',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id'
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
            'required' => true
        ],
        'sysmodule_id' => [
            'name' => 'sysmodule_id',
            'vname' => 'LBL_SYSMODULEID',
            'type' => 'varchar',
            'len' => 36
        ]
    ],
    'indices' => [
        [
            'name' => 'syssysexchangeuserconfiguserconfigpk',
            'type' => 'primary',
            'fields' => ['id']
        ],
        [
            'name' => 'idx_syssysexchangeuserconfiguserconfiguser',
            'type' => 'index',
            'fields' => ['user_id']
        ]
    ]
];

$dictionary['sysexchangeusersubscriptions'] = [
    'table' => 'sysexchangeusersubscriptions',
    'fields' => [
        'subscriptionid' => [
            'name'     => 'subscriptionid',
            'type'     => 'varchar',
            'len'      => 165,
            'required' => true,
            'isnull'   => false,
        ],
        'watermark' => [
            'name' => 'watermark',
            'type' => 'varchar',
            'len' => 100
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id'
        ],
        'folder_id' => [
            'name' => 'folder_id',
            'type' => 'varchar',
            'len' => 50
        ],
        'last_active' => [
            'name' => 'last_active',
            'type' => 'datetime'
        ]
    ],
    'indices' => [
        [
            'name' => 'sysexchangeusersubscriptionspk',
            'type' => 'primary',
            'fields' => ['subscriptionid']
        ],
        [
            'name' => 'idx_sysexchangeusersubscriptionsuser',
            'type' => 'index',
            'fields' => ['user_id']
        ]
    ]
];


$dictionary['sysexchangeuserresyncjobs'] = [
    'table' => 'sysexchangeuserresyncjobs',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id'
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id'
        ],
        'sysmodule_id' => [
            'name' => 'sysmodule_id',
            'type' => 'id',
        ],
        'date_start' => [
            'name' => 'date_start',
            'type' => 'datetime',
            'comment' => 'resync from calender item starting on this date'
        ],
//        'date_end' => [
//            'name' => 'date_end',
//            'type' => 'datetime',
//            'comment' => 'resync til calender item starting on this date'
//        ],
        'sync_done' => [
            'name' => 'sync_done',
            'type' => 'bool',
            'default' => 0
        ],
        'deleted' => [
            'name' => 'done',
            'type' => 'bool',
            'default' => 0
        ]
    ],
    'indices' => [
        [
            'name' => 'sysexchangeuserresyncjobspk',
            'type' => 'primary',
            'fields' => ['id']
        ],
//        [
//            'name' => 'idx_sysexchangeuserresyncs_user_folder',
//            'type' => 'index',
//            'fields' => ['user_id', 'folder_id']
//        ],
//        [
//            'name' => 'idx_sysexchangeuserresyncs_modid',
//            'type' => 'index',
//            'fields' => ['sysmodule_id']
//        ],
//        [
//            'name' => 'idx_sysexchangeuserresyncs_del',
//            'type' => 'index',
//            'fields' => ['deleted']
//        ]
    ]
];


