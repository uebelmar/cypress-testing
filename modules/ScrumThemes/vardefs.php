<?php

//dictionary global variable => class name als key
use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ScrumTheme'] = array(
    'table' => 'scrumthemes',
    'comment' => 'SCRUM Themes Module',
    'audited' => true,
    'duplicate_merge' => false,
    'unified_search' => false,

    'fields' => array(
        'name' => array(
            'name' => 'name',
            'type' => 'varchar',
            'len' => '100',
            'vname' => 'LBL_NAME',
        ),
        'description' => array(
            'name' => 'description',
            'type' => 'text',
            'vname' => 'LBL_DESCRIPTION',
        ),
        'scrum_status' => array(
            'name' => 'scrum_status',
            'type' => 'enum',
            'options' => 'scrum_status_dom',
            'len' => 50,
            'vname' => 'LBL_STATUS',
        ),
        'has_epics' => array(
            'name' => 'has_epics',
            'type' => 'bool',
            'source' => 'non-db'
        ),
        'project_id' => array(
            'name' => 'project_id',
            'vname' => 'LBL_PROJECT',
            'type' => 'id',
            'required' => false
        ),
        'project_name' => array(
            'name' => 'project_name',
            'rname' => 'name',
            'id_name' => 'project_id',
            'vname' => 'LBL_PROJECT',
            'type' => 'relate',
            'link' => 'project',
            'isnull' => 'true',
            'table' => 'projects',
            'module' => 'Projects',
            'source' => 'non-db',
        ),
        'project' => array(
            'name' => 'project',
            'type' => 'link',
            'vname' => 'LBL_PROJECT',
            'relationship' => 'project_scrumthemes',
            'module' => 'Projects',
            'source' => 'non-db'
        ),
        'scrumepics' => array(
            'name' => 'scrumepics',
            'type' => 'link',
            'relationship' => 'scrumtheme_scrumepics',
            'rname' => 'name',
            'source' => 'non-db',
            'module' => 'ScrumEpics',
        ),
        'abbreviation' => array(
            'name' => 'abbreviation',
            'type' => 'varchar',
            'len' => '10',
            'vname' => 'LBL_ABBREVIATION'
        ),
    ),
    'relationships' => array(
        'project_scrumthemes' => array(
            'rhs_module' => 'ScrumThemes',
            'rhs_table' => 'scrumthemes',
            'rhs_key' => 'project_id',
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
            'default' => true
        )
    ),

    'indices' => array(),
);
// default (Basic) fields & assignable (implements->assigned fields)
VardefManager::createVardef('ScrumThemes', 'ScrumTheme', array('default', 'assignable'));


