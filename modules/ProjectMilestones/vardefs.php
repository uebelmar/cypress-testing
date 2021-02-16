<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProjectMilestone'] = [
    'table' => 'projectmilestones',
    'comment' => 'Projectmilestones Module',
    'audited' =>  true,
	
	'fields' => [
        'date_due' => [
            'name' => 'date_due',
            'vname' => 'LBL_DUE_DATE',
            'type' => 'date',
        ],
        'project_id' => [
            'name' => 'project_id',
            'vname' => 'LBL_PROJECT_ID',
            'type' => 'id',
        ],
        'project_name' => [
            'name' => 'project_name',
            'vname' => 'LBL_PROJECT',
            'type' => 'relate',
            'len' => '255',
            'source' => 'non-db',
            'id_name' => 'project_id',
            'module' => 'Projects',
            'link' => 'projects',
            'join_name' => 'projects',
            'rname' => 'name'
        ],
        'projects' => [
            'name' => 'projects',
            'vname' => 'LBL_PROJECTS',
            'type' => 'link',
            'relationship' => 'projects_projectmilestones',
            'source' => 'non-db',
            'module' => 'Projects'
        ],
    ],
	'relationships' => [
	    'projects_projectmilestones' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'ProjectMilestones',
            'rhs_table' => 'projectmilestones',
            'rhs_key' => 'project_id',
            'relationship_type' => 'one-to-many'
        ]
    ],
	'indices' => [
        [
            'name' =>'idx_projectmilestones_projectid_del',
            'type' =>'index',
            'fields'=> ['project_id', 'deleted']
        ],
    ]
];

VardefManager::createVardef('ProjectMilestones', 'ProjectMilestone', ['default', 'assignable']);
