<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

// adding project-to-bugs relationship
$dictionary['projects_bugs'] = array (
    'table' => 'projects_bugs',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'bug_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'project_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false),
    ),
    'indices' => array (
        array('name' => 'projects_bugs_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_proj_bug_proj', 'type' =>'index', 'fields'=>array('project_id')),
//        array('name' => 'idx_proj_bug_bug', 'type' =>'index', 'fields'=>array('bug_id')),
//        array('name' => 'projects_bugs_alt', 'type'=>'alternate_key', 'fields'=>array('project_id','bug_id')),
    ),
    'relationships' => array (
// CR1000426 cleanup backend, module Bugs removed
//        'projects_bugs' => array(
//            'lhs_module' => 'Projects',
//            'lhs_table' => 'projects',
//            'lhs_key' => 'id',
//            'rhs_module' => 'Bugs',
//            'rhs_table' => 'bugs',
//            'rhs_key' => 'id',
//            'relationship_type' => 'many-to-many',
//            'join_table' => 'projects_bugs',
//            'join_key_lhs' => 'project_id',
//            'join_key_rhs' => 'bug_id',
//        ),
    ),
);
?>
