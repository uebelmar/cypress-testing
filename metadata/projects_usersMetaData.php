<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['projects_users'] = array (
    'table' => 'projects_users',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'project_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false),
    ),
    'indices' => array (
        array('name' => 'projects_users_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_proj_usr_proj', 'type' =>'index', 'fields'=>array('project_id')),
        array('name' => 'idx_proj_usr_con', 'type' =>'index', 'fields'=>array('user_id')),
        array('name' => 'projects_users_alt', 'type'=>'alternate_key', 'fields'=>array('project_id','user_id')),
    ),
    'relationships' => array (
        'projects_users' => array(
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_users',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'user_id',
        ),
    ),
);
