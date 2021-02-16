<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProjectActivityType'] = [
    'table' => 'projectactivitytypes',
    'comment' => 'ProjectActivityTypes Module for billing purpose',
    'audited' => false,
    'fields' => [

        'project_id' => [
            'name' => 'project_id',
            'vname' => 'LBL_PROJECT_ID',
            'type' => 'id'
        ],
        'project_name' => [
            'name' => 'project_name',
            'vname' => 'LBL_PROJECT',
            'type' => 'relate',
            'source' => 'non-db',
            'link' => 'projects',
            'id_name' => 'project_id',
            'rname' => 'name',
            'module' => 'Projects',
            'join_name' => 'projects',
        ],
        'projects' => [
            'name' => 'projects',
            'vname' => 'LBL_PROJECTS',
            'relationship' => 'projectactivitytype_projects',
            'source' => 'non-db',
            'module' => 'Projects'
        ],
        'projectplannedactivities' => [
            'name' => 'projectplannedactivities',
            'vname' => 'LBL_PROJECTPLANNEDACTIVITIES',
            'type' => 'link',
            'relationship' => 'projectactivitytype_projectplannedactivities',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db',
            'module' => 'ProjectPlannedActivities'
        ],
        'projectactivities' => [
            'name' => 'projectactivities',
            'vname' => 'LBL_PROJECTACTIVITIES',
            'type' => 'link',
            'relationship' => 'projectactivitytype_projectactivities',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db',
            'module' => 'ProjectActivities'
        ]
    ],
    'relationships' => [
        'projectactivitytype_projects' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'ProjectActivityTypes',
            'rhs_table' => 'projectactivitytypes',
            'rhs_key' => 'project_id',
            'relationship_type' => 'one-to-many'
        ],
        'projectactivitytype_projectplannedactivities' => [
            'lhs_module' => 'ProjectActivityTypes',
            'lhs_table' => 'projectactivitytypes',
            'lhs_key' => 'id',
            'rhs_module' => 'ProjectPlannedActivities',
            'rhs_table' => 'projectplannedactivities',
            'rhs_key' => 'projectactivitytype_id',
            'relationship_type' => 'one-to-many'
        ],
        'projectactivitytype_projectactivities' => [
            'lhs_module' => 'ProjectActivityTypes',
            'lhs_table' => 'projectactivitytypes',
            'lhs_key' => 'id',
            'rhs_module' => 'ProjectPlannedActivities',
            'rhs_table' => 'projectplannedactivities',
            'rhs_key' => 'projectactivitytype_id',
            'relationship_type' => 'one-to-many'
        ]
    ],
    'indices' => [
        ['name' => 'idx_projectactivitytypes_projectiddel', 'type' => 'index', 'fields' => ['project_id', 'deleted']]
    ]
];

VardefManager::createVardef('ProjectActivityTypes', 'ProjectActivityType', ['default', 'assignable']);
