<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['events_consumers'] = array(
    'table' => 'events_consumers',
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
        'consumer_id' => array(
            'name' => 'consumer_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'consumer_role' => array(
            'name' => 'consumer_role',
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
            'name' => 'idx_events_consumers_primary',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_events_consumer',
            'type' => 'alternate_key',
            'fields' => array('event_id', 'consumer_id')
        )
    ),
    'relationships' => array(
        'events_consumers' => array(
            'lhs_module' => 'Events',
            'lhs_table' => 'events',
            'lhs_key' => 'id',
            'rhs_module' => 'Consumers',
            'rhs_table' => 'consumers',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'events_consumers',
            'join_key_lhs' => 'event_id',
            'join_key_rhs' => 'consumer_id'
        )
    )
);
