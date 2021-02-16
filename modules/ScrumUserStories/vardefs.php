<?php

//dictionary global variable => class name als key
use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ScrumUserStory'] = array(
    'table' => 'scrumuserstories',
    'comment' => 'SCRUM User Stories Module',
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
        'level_of_complexity' => array(
            'name' => 'level_of_complexity',
            'type' => 'int',
            'dbtype' => 'double',
            'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
            'vname' => 'LBL_LEVEL_OF_COMPLEXITY',
        ),
        'level_of_completion' => array(
            'name' => 'level_of_completion',
            'type' => 'int',
            'dbtype' => 'double',
            'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
            'vname' => 'LBL_LEVEL_OF_COMPLETION',
        ),
        'ratio' => array(
            'name' => 'ratio',
            'type' => 'int',
            'source' => 'non-db',
            'vname' => 'LBL_RATIO',
        ),
        'scrumepic_id' => array(
            'name' => 'scrumepic_id',
            'vname' => 'LBL_SCRUM_EPIC',
            'type' => 'id',
            'required' => false
        ),
        'scrumepic_name' => array(
            'name' => 'scrumepic_name',
            'rname' => 'name',
            'id_name' => 'scrumepic_id',
            'vname' => 'LBL_SCRUM_EPIC',
            'type' => 'relate',
            'link' => 'scrumepic',
            'isnull' => 'true',
            'table' => 'scrumepics',
            'module' => 'ScrumEpics',
            'source' => 'non-db',
        ),
        'scrumepic' => array(
            'name' => 'scrumepic',
            'type' => 'link',
            'vname' => 'LBL_SCRUM_EPIC',
            'relationship' => 'scrumepic_scrumuserstories',
            'module' => 'ScrumEpics',
            'source' => 'non-db'
        ),
        'systemdeploymentcrs' => array(
            'name' => 'systemdeploymentcrs',
            'type' => 'link',
            'relationship' => 'scrumuserstories_systemdeploymentcrs',
            'vname' => 'LBL_SYSTEMDEPLOYMENTCRS',
            'module' => 'SystemDeploymentCRs',
            'source' => 'non-db',
            'default' => false
        ),
        'projectwbs_id' => array(
            'name' => 'projectwbs_id',
            'vname' => 'LBL_PROJECTWBS',
            'type' => 'id',
            'required' => false
        ),
        'projectwbs_name' => array(
            'name' => 'projectwbs_name',
            'rname' => 'name',
            'id_name' => 'projectwbs_id',
            'vname' => 'LBL_PROJECTWBS',
            'type' => 'relate',
            'link' => 'projectwbs',
            'isnull' => 'true',
            'table' => 'projectwbss',
            'module' => 'ProjectWBSs',
            'source' => 'non-db',
        ),
        'projectwbs' => array(
            'name' => 'projectwbs',
            'type' => 'link',
            'vname' => 'LBL_PROJECTWBS',
            'relationship' => 'projectwbs_scrumuserstories',
            'module' => 'ProjectWBSs',
            'source' => 'non-db'
        ),
        'sequence' => array(
            'name' => 'sequence',
            'type' => 'int',
            'len' => '11',
            'vname' => 'LBL_SEQUENCE',
        ),
    ),
    'relationships' => array(
        'scrumepic_scrumuserstories' => array(
            'rhs_module' => 'ScrumUserStories',
            'rhs_table' => 'scrumuserstories',
            'rhs_key' => 'scrumepic_id',
            'lhs_module' => 'ScrumEpics',
            'lhs_table' => 'scrumepics',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
            'default' => true
        ),
        'projectwbs_scrumuserstories' => array(
            'rhs_module' => 'ScrumUserStories',
            'rhs_table' => 'scrumuserstories',
            'rhs_key' => 'projectwbs_id',
            'lhs_module' => 'ProjectWBSs',
            'lhs_table' => 'projectwbss',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
            'default' => true
        )
    ),

    'indices' => array(
    ),
);
// default (Basic) fields & assignable (implements->assigned fields)
VardefManager::createVardef('ScrumUserStories', 'ScrumUserStory', array('default', 'assignable'));


