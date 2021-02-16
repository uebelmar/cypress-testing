<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/



$dictionary['projects_contacts'] = array (
    'table' => 'projects_contacts',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'contact_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'project_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false),
    ),
    'indices' => array (
        array('name' => 'projects_contacts_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_proj_con_proj', 'type' =>'index', 'fields'=>array('project_id')),
        array('name' => 'idx_proj_con_con', 'type' =>'index', 'fields'=>array('contact_id')),
        array('name' => 'projects_contacts_alt', 'type'=>'alternate_key', 'fields'=>array('project_id','contact_id')),
    ),
    'relationships' => array (
        'projects_contacts' => array(
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_contacts',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'contact_id',
        ),
    ),
);
?>
