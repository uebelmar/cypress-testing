<?php


$dictionary['accounts_potentials'] = array(
    'table' => 'accounts_potentials',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'char',
            'len' => '36'
        ),
        'account_id' => array(
            'name' => 'account_id',
            'type' => 'char',
            'len' => '36'
        ),
        'potential_id' => array(
            'name' => 'potential_id',
            'type' => 'char',
            'len' => '36'
        ),
        'role' => array(
            'name' => 'role',
            'type' => 'varchar',
            'len' => '10'
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0'
        )
    ),
    'indices' => array(
        array(
            'name' => 'accounts_potentials_pk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_account_potential_role',
            'type' => 'alternate_key',
            'fields' => array(
                'account_id',
                'potential_id',
                'role'
            )
        ),
        array(
            'name' => 'idx_account_del_potential',
            'type' => 'index',
            'fields' => array(
                'account_id',
                'deleted',
                'potential_id'
            )
        ),
        array(
            'name' => 'idx_potential_role_del',
            'type' => 'index',
            'fields' => array(
                'potential_id',
                'role',
                'deleted'
            )
        )
    ),
    'relationships' => array(
        'accounts_potentials_resellers' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'relationship_role_column' => 'role',
            'relationship_role_column_value' => 'reseller',
            'join_table' => 'accounts_potentials',
            'join_key_lhs' => 'account_id',
            'join_key_rhs' => 'potential_id'
        ),
        'accounts_potentials_competitors' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'relationship_role_column' => 'role',
            'relationship_role_column_value' => 'competitor',
            'join_table' => 'accounts_potentials',
            'join_key_lhs' => 'account_id',
            'join_key_rhs' => 'potential_id'
        )
    ),
);

