<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['events_accounts'] = array(
    'table' => 'events_accounts',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'event_id' => array(
            'name' => 'event_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'account_id' => array(
            'name' => 'account_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'account_role' => array(
            'name' => 'account_role',
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
            'name' => 'idx_events_accounts_primary',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_events_account',
            'type' => 'alternate_key',
            'fields' => array('event_id', 'account_id')
        )
    ),
    'relationships' => array(
        'events_accounts' => array(
            'lhs_module' => 'Events',
            'lhs_table' => 'events',
            'lhs_key' => 'id',
            'rhs_module' => 'Accounts',
            'rhs_table' => 'accounts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'events_accounts',
            'join_key_lhs' => 'event_id',
            'join_key_rhs' => 'account_id'
        )
    )
);
