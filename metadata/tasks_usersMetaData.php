<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


$dictionary['tasks_users'] = array(
    'table' => 'tasks_users',
    'fields' => array(
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'task_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false)
    ),
    'indices' => array(
        array('name' => 'tasks_userspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_usr_task_task', 'type' => 'index', 'fields' => array('task_id')),
        array('name' => 'idx_usr_task_usr', 'type' => 'index', 'fields' => array('user_id')),
        array('name' => 'idx_task_users', 'type' => 'alternate_key', 'fields' => array('task_id', 'user_id'))
    ),
    'relationships' => array(
        'tasks_users' => array(
            'lhs_module' => 'Tasks',
            'lhs_table' => 'tasks',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'tasks_users',
            'join_key_lhs' => 'task_id',
            'join_key_rhs' => 'user_id',
        ),
    ),
);
