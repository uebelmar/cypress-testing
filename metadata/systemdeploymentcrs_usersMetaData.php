<?php
/***** SPICE-HEADER-SPACEHOLDER *****/


$dictionary['systemdeploymentcrs_users'] = array(
    'table' => 'systemdeploymentcrs_users',
    'fields' => array(
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'systemdeploymentcr_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_role', 'type' => 'multienum', 'dbType' => 'text', 'options' => 'cruser_role_dom'), // CR1000333
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false)
    ),
    'indices' => array(
        array('name' => 'systemdeploymentcrs_userspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_systemdeploymentcrs_users', 'type' => 'alternate_key', 'fields' => array('systemdeploymentcr_id', 'user_id')),
        array('name' => 'idx_systemdeploymentcrs_crid', 'type' => 'index', 'fields' => array('systemdeploymentcr_id')),
        array('name' => 'idx_systemdeploymentcrs_userid', 'type' => 'index', 'fields' => array('user_id')),
    ),
    'relationships' => array(
        'systemdeploymentcrs_users' => array(
            'lhs_module' => 'SystemDeploymentCRs',
            'lhs_table' => 'systemdeploymentcrs',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'systemdeploymentcrs_users',
            'join_key_lhs' => 'systemdeploymentcr_id',
            'join_key_rhs' => 'user_id',
        ),
    ),
);
