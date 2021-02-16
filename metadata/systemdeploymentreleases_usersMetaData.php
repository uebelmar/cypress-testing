<?php
/***** SPICE-HEADER-SPACEHOLDER *****/


$dictionary['systemdeploymentreleases_users'] = array(
    'table' => 'systemdeploymentreleases_users',
    'fields' => array(
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'systemdeploymentrelease_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false)
    ),
    'indices' => array(
        array('name' => 'systemdeploymentreleases_userspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_systemdeploymentrs_users', 'type' => 'alternate_key', 'fields' => array('systemdeploymentrelease_id', 'user_id')),
        array('name' => 'idx_systemdeploymentrs_crid', 'type' => 'index', 'fields' => array('systemdeploymentrelease_id')),
        array('name' => 'idx_systemdeploymentrs_userid', 'type' => 'index', 'fields' => array('user_id')),
    ),
    'relationships' => array(
        'systemdeploymentreleases_users' => array(
            'lhs_module' => 'SystemDeploymentReleases',
            'lhs_table' => 'systemdeploymentreleases',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'systemdeploymentreleases_users',
            'join_key_lhs' => 'systemdeploymentrelease_id',
            'join_key_rhs' => 'user_id',
        ),
    ),
);
