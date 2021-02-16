<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

$dictionary['spicethemepages_users'] = array(
    'table' => 'spicethemepages_users',
    'fields' => array(
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'spicethemepage_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => false, 'default' => '0')
    ),
    'indices' => array(
        array('name' => 'spicethemepages_userspk', 'type' => 'primary', 'fields' => array('id'))
    ),
    'relationships' => array(
        'ausers_spicethemepages' => array(
            'lhs_module' => 'SpiceThemePages',
            'lhs_table' => 'spicethemepages',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'spicethemepages_users',
            'join_key_lhs' => 'spicethemepage_id',
            'join_key_rhs' => 'user_id')
    )
);

$dictionary['spicethemepages_aclroles'] = array(
    'table' => 'spicethemepages_aclroles',
    'fields' => array(
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'spicethemepage_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'aclrole_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => false, 'default' => '0')
    ),
    'indices' => array(
        array('name' => 'spicethemepages_aclrolespk', 'type' => 'primary', 'fields' => array('id'))
    ),
    'relationships' => array(
        'aclroles_spicethemepages' => array(
            'lhs_module' => 'SpiceThemePages',
            'lhs_table' => 'spicethemepages',
            'lhs_key' => 'id',
            'rhs_module' => 'ACLRoles',
            'rhs_table' => 'acl_roles',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'spicethemepages_aclroles',
            'join_key_lhs' => 'spicethemepage_id',
            'join_key_rhs' => 'aclrole_id')
    )
);
?>
