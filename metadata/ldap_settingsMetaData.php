<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['ldap_settings'] = [
    'table' => 'ldap_settings',
    'fields' => [
        'id' => [
            'name'     => 'id',
            'vname'    => 'LBL_ID',
            'type'     => 'id',
            'required' => true,
            'comment'  => 'Unique identifier',
        ],
        'date_entered' => [
            'name'     => 'date_entered',
            'vname'    => 'LBL_DATE_ENTERED',
            'type'     => 'datetime',
            'required' => true,
        ],
        'date_modified' => [
            'name'     => 'date_modified',
            'vname'    => 'LBL_DATE_MODIFIED',
            'type'     => 'datetime',
            'required' => true,
        ],
        'deleted' => [
            'name'       => 'deleted',
            'vname'      => 'LBL_DELETED',
            'type'       => 'bool',
            'required'   => false,
            'reportable' => false,
        ],
        'hostname' => [
            'name'     => 'hostname',
            'vname'    => 'LBL_HOSTNAME',
            'type'     => 'varchar',
            'len'      => 255,
            'required' => true,
        ],
        'port' => [
            'name'     => 'port',
            'vname'    => 'LBL_PORT',
            'type'     => 'varchar',
            'len'      => 5,
        ],
        'base_dn' => [
            'name'     => 'base_dn',
            'vname'    => 'LBL_BASE_DN',
            'type'     => 'varchar',
            'len'      => 50,
        ],
        'login_filter' => [
            'name'     => 'login_filter',
            'vname'    => 'LBL_LOGIN_FILTER',
            'type'     => 'varchar',
            'len'      => 50,
        ],
        'bind_attr' => [
            'name'     => 'bind_attr',
            'vname'    => 'LBL_BIND_ATTR',
            'type'     => 'varchar',
            'len'      => 50,
        ],
        'login_attr' => [
            'name'     => 'login_attr',
            'vname'    => 'LBL_LOGIN_ATTR',
            'type'     => 'varchar',
            'len'      => 50,
        ],
        'admin_user' => [
            'name'     => 'admin_user',
            'vname'    => 'LBL_ADMIN_USER',
            'type'     => 'varchar',
            'len'      => 50,
        ],
        'admin_password' => [
            'name'     => 'admin_password',
            'vname'    => 'LBL_ADMIN_PASSWORD',
            'type'     => 'varchar',
            'len'      => 255,
        ],
        'auto_create_users' => [
            'name'     => 'auto_create_users',
            'vname'    => 'LBL_AUTO_CREATE_USERS',
            'type'     => 'bool',
            'len'      => 1,
            'required' => false,
            'default'  => '0',
        ],
        'is_active' => [
            'name'     => 'is_active',
            'vname'    => 'LBL_IS_ACTIVE',
            'type'     => 'bool',
            'len'      => 1,
            'required' => false,
            'default'  => '0',
        ],
        'priority' => [
            'name'     => 'priority',
            'vname'    => 'LBL_PRIORITY',
            'type'     => 'varchar',
            'len'      => 3,
        ],
        'ldap_authentication' => [
            'name'    => 'ldap_authentication',
            'vname'   => 'LBL_LDAP_AUTHENTICATION',
            'type'    => 'bool',
            'comment' => '1 means LDAP auth is necessary, 0 means LDAP auth is not necessary',
        ],
        'ldap_groups' => [
            'name'    => 'ldap_groups',
            'vname'   => 'LBL_LDAP_GROUPS',
            'type'    => 'text',
            'comment' => 'comma seperated list of ldap groups',
        ],
    ],
    'indices' => [
        [
            'name'   => 'ldap_settingspk',
            'type'   => 'primary',
            'fields' => ['id'],
        ]
    ],
];