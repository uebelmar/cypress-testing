<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['accounts_users'] = array(
    'table' => 'accounts_users',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'user_id' => array(
            'name' => 'user_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'account_id' => array(
            'name' => 'account_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'user_role' => array(
            'name' => 'user_role',
            'type' => 'varchar',
            'len' => '36'
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'required' => false,
            'default' => '0'
        )
    ),
    'indices' => array(
        array(
            'name' => 'accounts_userspk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_account_user',
            'type' => 'alternate_key',
            'fields' => array('account_id', 'user_id')
        )
    ),
    'relationships' => array(
        'accounts_users' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'accounts_users',
            'join_key_lhs' => 'account_id',
            'join_key_rhs' => 'user_id'
        )
    )
);
