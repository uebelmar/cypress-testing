<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['events_contacts'] = array(
    'table' => 'events_contacts',
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
        'contact_id' => array(
            'name' => 'contact_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'contact_role' => array(
            'name' => 'contact_role',
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
            'name' => 'idx_events_contacts_primary',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_events_contact',
            'type' => 'alternate_key',
            'fields' => array('event_id', 'contact_id')
        )
    ),
    'relationships' => array(
        'events_contacts' => array(
            'lhs_module' => 'Events',
            'lhs_table' => 'events',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'events_contacts',
            'join_key_lhs' => 'event_id',
            'join_key_rhs' => 'contact_id'
        )
    )
);
