<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['calls_users'] = array(
    'table' => 'calls_users',
    'fields' => array(
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'call_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'required', 'type' => 'varchar', 'len' => '1', 'default' => '1'),
        array('name' => 'accept_status', 'type' => 'varchar', 'len' => '25', 'default' => 'none'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false)
    ),
    'indices' => array(
        array('name' => 'calls_userspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_usr_call_call', 'type' => 'index', 'fields' => array('call_id')),
        array('name' => 'idx_usr_call_usr', 'type' => 'index', 'fields' => array('user_id')),
        array('name' => 'idx_call_users', 'type' => 'alternate_key', 'fields' => array('call_id', 'user_id'))
    ),
    'relationships' => array(
        'calls_users' => array(
            'lhs_module' => 'Calls',
            'lhs_table' => 'calls',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'calls_users',
            'join_key_lhs' => 'call_id',
            'join_key_rhs' => 'user_id',
        ),
        // CR1000356
        'calls_users_status_accept' => [
            'lhs_module'		=> 'Calls',
            'lhs_table'			=> 'calls',
            'lhs_key'			=> 'id',
            'rhs_module'		=> 'Users',
            'rhs_table'			=> 'users',
            'rhs_key'			=> 'id',
            'relationship_type'	=> 'many-to-many',
            'relationship_role_column'	=> 'accept_status',
            'relationship_role_column_value'	=> 'accept',
            'join_table'		=> 'calls_users',
            'join_key_lhs'		=> 'call_id',
            'join_key_rhs'		=> 'user_id'
        ],
        'calls_users_status_decline' => [
            'lhs_module'		=> 'Calls',
            'lhs_table'			=> 'calls',
            'lhs_key'			=> 'id',
            'rhs_module'		=> 'Users',
            'rhs_table'			=> 'users',
            'rhs_key'			=> 'id',
            'relationship_type'	=> 'many-to-many',
            'relationship_role_column'	=> 'accept_status',
            'relationship_role_column_value'	=> 'decline',
            'join_table'		=> 'calls_users',
            'join_key_lhs'		=> 'call_id',
            'join_key_rhs'		=> 'user_id'
        ],
        'calls_users_status_tentative' => [
            'lhs_module'		=> 'Calls',
            'lhs_table'			=> 'calls',
            'lhs_key'			=> 'id',
            'rhs_module'		=> 'Users',
            'rhs_table'			=> 'users',
            'rhs_key'			=> 'id',
            'relationship_type'	=> 'many-to-many',
            'relationship_role_column'	=> 'accept_status',
            'relationship_role_column_value'	=> 'tentative',
            'join_table'		=> 'calls_users',
            'join_key_lhs'		=> 'call_id',
            'join_key_rhs'		=> 'user_id'
        ],
    ),
);
?>
