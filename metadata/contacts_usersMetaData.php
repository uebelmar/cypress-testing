<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['contacts_users'] = array(
    'table' => 'contacts_users',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'contact_id' => array(
            'name' => 'contact_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'user_id' => array(
            'name' => 'user_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'external_data' => array(
            'name' => 'external_data',
            'type' => 'text'
        ),
        'external_id' => [
            'name' => 'external_id',
            'type' => 'varchar',
            'len'  => 165,
        ],
        'date_modified' => array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false
        )
    ),
    'indices' => array(
        array(
            'name' => 'contacts_userspk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_con_users_con',
            'type' => 'index',
            'fields' => array('contact_id')
        ),
        array(
            'name' => 'idx_con_users_user',
            'type' => 'index',
            'fields' => array('user_id')
        ),
        array(
            'name' => 'idx_contacts_users',
            'type' => 'alternate_key',
            'fields' => array('contact_id', 'user_id')
        ),
        array(
            'name'   => 'idx_contacts_users_external_id',
            'type'   => 'index',
            'fields' => ['external_id'],
        )
    ),
    'relationships' => array(
        'contacts_users' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'contacts_users',
            'join_key_lhs' => 'contact_id',
            'join_key_rhs' => 'user_id'
        )
    )
);
