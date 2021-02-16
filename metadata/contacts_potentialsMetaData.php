<?php

$dictionary['contacts_potentials'] = array(
    'table' => 'contacts_potentials',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'char',
            'len' => '36'
        ),
        'contact_id' => array(
            'name' => 'contact_id',
            'type' => 'char',
            'len' => '36'
        ),
        'potential_id' => array(
            'name' => 'potential_id',
            'type' => 'char',
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
            'default' => '0'
        )
    ),
    'indices' => array(
        array(
            'name' => 'contacts_potentials_pk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_contact_potential',
            'type' => 'alternate_key',
            'fields' => array('contact_id', 'potential_id')
        )
    ),
    'relationships' => array(
        'contacts_potentials' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'contacts_potentials',
            'join_key_lhs' => 'contact_id',
            'join_key_rhs' => 'potential_id'
        )
    ),
);

