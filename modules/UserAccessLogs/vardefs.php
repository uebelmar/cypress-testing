<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


use SpiceCRM\includes\SugarObjects\VardefManager;

$GLOBALS['dictionary']['UserAccessLog'] = array(
    'table' => 'useraccesslogs',
    'fields' => array(
        'ipaddress' => array(
            'name' => 'ipaddress',
            'type' => 'varchar',
            'len' => 15
        ),
        'action' => array(
            'name' => 'action',
            'type' => 'varchar',
            'len' => 30
        ),
        'user' => array(
            'name' => 'user',
            'type' => 'link',
            'relationship' => 'users_useraccesslogs',
            'source' => 'non-db',
            'vname' => 'LBL_USER',
        ),
        'login_name' => array(
            'name' => 'login_name',
            'type' => 'varchar',
            'len' => 255
        )
    ),
    'relationships' => array(
        'users_useraccesslogs' =>
            array(
                'lhs_module' => 'Users',
                'lhs_table' => 'users',
                'lhs_key' => 'id',
                'rhs_module' => 'UserAccessLogs',
                'rhs_table' => 'useraccesslogs',
                'rhs_key' => 'assigned_user_id',
                'relationship_type' => 'one-to-many'
            )
    ),
    'indices' => array(
        array(
            'name' => 'idx_useraccesslogsuserid',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        )
    )
);

VardefManager::createVardef('UserAccessLogs', 'UserAccessLog', array('default', 'assignable'));
