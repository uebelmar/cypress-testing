<?php

//dictionary global variable => class name als key
use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ScrumEpic'] = array(
    'table' => 'scrumepics',
    'comment' => 'SCRUM Epics Module',
    'audited' =>  true,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,

    'fields' => array(
        'name' => array(
            'name' => 'name',
            'type' => 'varchar',
            'len' => '100',
            'vname' => 'LBL_NAME',
        ),
        'description'  => array(
            'name' => 'description',
            'type' => 'text',
            'vname' => 'LBL_DESCRIPTION',
        ),
        'scrum_status'=> array(
            'name' => 'scrum_status',
            'type' => 'enum',
            'options' => 'scrum_status_dom',
            'len' => 50,
            'vname' => 'LBL_STATUS',
        ),
        'has_stories'=> array(
            'name' => 'has_stories',
            'type' => 'bool',
            'source' => 'non-db',
        ),
        'scrumtheme_id' => array(
            'name' => 'scrumtheme_id',
            'vname' => 'LBL_SCRUM_THEME',
            'type' => 'id',
            'required' => false
        ),
        'scrumtheme_name' => array(
            'name' => 'scrumtheme_name',
            'rname' => 'name',
            'id_name' => 'scrumtheme_id',
            'vname' => 'LBL_SCRUM_THEME',
            'type' => 'relate',
            'link' => 'scrumtheme',
            'isnull' => 'true',
            'table' => 'scrumthemes',
            'module' => 'ScrumThemes',
            'source' => 'non-db',
        ),
        'scrumtheme' => array(
            'name' => 'scrumtheme',
            'type' => 'link',
            'vname' => 'LBL_SCRUM_THEME',
            'relationship' => 'scrumtheme_scrumepics',
            'module' => 'ScrumThemes',
            'source' => 'non-db'
        ),
        'scrumuserstories' => array(
            'name' => 'scrumuserstories',
            'type' => 'link',
            'relationship' => 'scrumepic_scrumuserstories',
            'rname' => 'name',
            'source' => 'non-db',
            'module' => 'ScrumUserStories',
        ),
        'sequence' => array(
            'name' => 'sequence',
            'type' => 'int',
            'len' => '11',
            'vname' => 'LBL_SEQUENCE',
        ),
    ),
    'relationships' => array(
        'scrumtheme_scrumepics' => array(
            'rhs_module' => 'ScrumEpics',
            'rhs_table' => 'scrumepics',
            'rhs_key' => 'scrumtheme_id',
            'lhs_module' => 'ScrumThemes',
            'lhs_table' => 'scrumthemes',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
            'default' => true
        )

    ),

    'indices' => array(
    ),
);
// default (Basic) fields & assignable (implements->assigned fields)
VardefManager::createVardef('ScrumEpics', 'ScrumEpic', array('default', 'assignable'));


