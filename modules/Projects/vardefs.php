<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Project'] = [
    'table' => 'projects',
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => false,
    'comment' => 'Projects',
    'fields' => [
        'estimated_start_date' => [
            'name' => 'estimated_start_date',
            'vname' => 'LBL_DATE_START',
            'required' => true,
            'validation' => ['type' => 'isbefore', 'compareto' => 'estimated_end_date', 'blank' => true],
            'type' => 'date',
            'importable' => 'required',
            'enable_range_search' => true,
        ],
        'estimated_end_date' => [
            'name' => 'estimated_end_date',
            'vname' => 'LBL_DATE_END',
            'required' => true,
            'type' => 'date',
            'importable' => 'required',
            'enable_range_search' => true,
        ],
        'project_type' => [
            'name' => 'project_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => 32,
            'options' => 'project_type_dom',
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'project_status_dom',
        ],
        'priority' => [
            'name' => 'priority',
            'vname' => 'LBL_PRIORITY',
            'type' => 'enum',
            'options' => 'projects_priority_options',
        ],
        'total_estimated_effort' => [
            'name' => 'total_estimated_effort',
            'type' => 'double',
            'source' => 'non-db',
            'vname' => 'LBL_TOTAL_ESTIMATED_EFFORT',
        ],
        'total_actual_effort' => [
            'name' => 'total_actual_effort',
            'type' => 'double',
            'source' => 'non-db',
            'vname' => 'LBL_TOTAL_ACTUAL_EFFORT',
        ],
        'accounts' => [
            'name' => 'accounts',
            'type' => 'link',
            'module' => 'Accounts',
            'relationship' => 'projects_accounts',
            'source' => 'non-db',
            'ignore_role' => true,
            'vname' => 'LBL_ACCOUNTS',
        ],
        'contacts' => [
            'name' => 'contacts',
            'type' => 'link',
            'module' => 'Contacts',
            'relationship' => 'projects_contacts',
            'source' => 'non-db',
            'ignore_role' => true,
            'vname' => 'LBL_CONTACTS',
        ],
        'users' => [
            'name' => 'users',
            'type' => 'link',
            'module' => 'Users',
            'relationship' => 'projects_users',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
        ],
        'opportunities' => [
            'name' => 'opportunities',
            'type' => 'link',
            'module' => 'Opportunities',
            'relationship' => 'projects_opportunities',
            'source' => 'non-db',
            'ignore_role' => true,
            'vname' => 'LBL_OPPORTUNITIES',
        ],
        'notes' => [
            'name' => 'notes',
            'type' => 'link',
            'module' => 'Notes',
            'relationship' => 'projects_notes',
            'source' => 'non-db',
            'vname' => 'LBL_NOTES',
        ],
        'tasks' => [
            'name' => 'tasks',
            'type' => 'link',
            'module' => 'Tasks',
            'relationship' => 'projects_tasks',
            'source' => 'non-db',
            'vname' => 'LBL_TASKS',
        ],
        'meetings' => [
            'name' => 'meetings',
            'type' => 'link',
            'module' => 'Meetings',
            'relationship' => 'projects_meetings',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS',
        ],
        'calls' => [
            'name' => 'calls',
            'type' => 'link',
            'module' => 'Calls',
            'relationship' => 'projects_calls',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS',
            'join_name' => 'calls'
        ],
        'emails' => [
            'name' => 'emails',
            'type' => 'link',
            'module' => 'Emails',
            'relationship' => 'emails_projects_rel',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
        ],
        'documents' => [
            'name' => 'documents',
            'type' => 'link',
            'relationship' => 'documents_projects',
            'source' => 'non-db',
            'module' => 'Documents',
            'vname' => 'LBL_DOCUMENTS',
        ],
        'scrumthemes' => [
            'name' => 'scrumthemes',
            'type' => 'link',
            'relationship' => 'project_scrumthemes',
            'rname' => 'name',
            'source' => 'non-db',
            'module' => 'ScrumThemes'
        ],
        'projectactivitytypes' => [
            'name' => 'projectactivitytypes',
            'vname' => 'LBL_PROJECTACTIVITYTYPES',
            'type' => 'link',
            'relationship' => 'projectactivitytype_projects',
            'source' => 'non-db',
            'module' => 'ProjectActivityTypes'
        ],
        'projectmilestones' => [
            'name' => 'projectmilestones',
            'vname' => 'LBL_PROJECTMILESTONES',
            'type' => 'link',
            'module' => 'ProjectMilestones',
            'relationship' => 'projects_projectmilestones',
            'source' => 'non-db',
        ]

    ],

    'relationships' => [
        'projects_notes' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Projects'
        ],
        'projects_tasks' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Tasks',
            'rhs_table' => 'tasks',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Projects'
        ],
        'projects_meetings' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Meetings',
            'rhs_table' => 'meetings',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Projects'
        ],
        'projects_calls' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Calls',
            'rhs_table' => 'calls',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Projects'
        ],
        'projects_emails' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Emails',
            'rhs_table' => 'emails',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Projects'
        ],
        'projects_projectactivities' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'ProjectActivities',
            'rhs_table' => 'projectactivities',
            'rhs_key' => 'projectwbs_id',
            'relationship_type'=>'many-to-many',
            'join_table'=> 'projectwbss',
            'join_key_lhs'=>'project_id',
            'join_key_rhs'=>'id'
        ],
        'projects_assigned_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Projects',
            'rhs_table' => 'projects',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ],
        'projects_modified_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Projects',
            'rhs_table' => 'projects',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ],
        'projects_created_by' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Projects',
            'rhs_table' => 'projects',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ],
        'projects_projectwbss' => [
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'ProjectWBSs',
            'rhs_table' => 'projectwbss',
            'rhs_key' => 'project_id',
            'relationship_type' => 'one-to-many'
        ],
    ],
    'indices' => [
        ['name' => 'idx_projects_typedel', 'type' => 'index', 'fields' => ['project_type', 'deleted']],
        ['name' => 'idx_projects_statusdel', 'type' => 'index', 'fields' => ['status', 'deleted']],
        ['name' => 'idx_projects_typestatusdel', 'type' => 'index', 'fields' => ['project_type', 'status', 'deleted']],
    ],
];

// CE version has not all projects modules...
//set global else error with PHP7.1: Uncaught Error: Cannot use string offset as an array
global $dictionary;
if(is_file('modules/ProjectActivities/ProjectActivity.php')) {
    $dictionary['Project']['fields']['projectactivities'] = [
        'name' => 'projectactivities',
        'vname' => 'LBL_PROJECTACTIVITIES',
        'type' => 'link',
        'relationship' => 'projects_projectactivities',
        'source'=>'non-db',
        'module' => 'ProjectActivities',
    ];
}
if(is_file('modules/ProjectWBSs/ProjectWBS.php')) {
    $dictionary['Project']['fields']['projectwbss'] = [
        'name' => 'projectwbss',
        'vname' => 'LBL_PROJECTWBSS',
        'type' => 'link',
        'relationship' => 'projects_projectwbss',
        'source'=>'non-db',
        'module' => 'ProjectWBSs'
    ];
}
if(is_file('modules/Products/Product.php')) {
    $dictionary['Project']['fields']['products'] = [
        'name' => 'products',
        'vname' => 'LBL_PRODUCTS',
        'type' => 'link',
        'module' => 'Products',
        'relationship' => 'projects_products',
        'side' => 'right',
        'source' => 'non-db',
    ];
}

VardefManager::createVardef('Projects', 'Project', ['default', 'assignable']);

global $dictionary;
// CR1000336
if(is_file('modules/SystemDeploymentReleases/SystemDeploymentRelease.php')){
    $dictionary['Project']['relationships']['account_systemdeploymentreleases'] = [
        'lhs_module' => 'Projects', 'lhs_table' => 'projects', 'lhs_key' => 'id',
        'rhs_module' => 'SystemDeploymentReleases', 'rhs_table' => 'systemdeploymentreleases', 'rhs_key' => 'parent_id',
        'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
        'relationship_role_column_value' => 'Projects'
    ];
//    $dictionary['Project']['fields']['systemdeploymentreleases'] = array(
//        'name' => 'systemdeploymentreleases',
//        'type' => 'link',
//        'relationship' => 'project_systemdeploymentreleases',
//        'module' => 'SystemDeploymentReleases',
//        'bean_name' => 'SystemDeploymentRelease',
//        'source' => 'non-db',
//        'vname' => 'LBL_SYSTEMDEPLOYMENTRELEASES',
//    );
}

if(is_file('modules/SalesDocs/SalesDoc.php')) {
    $dictionary['Project']['fields']['salesdocs'] = [
        'name' => 'salesdocs',
        'type' => 'link',
        'relationship' => 'salesdocs_projects_parent',
        'module' => 'SalesDocs',
        'bean_name' => 'SalesDoc',
        'source' => 'non-db',
        'vname' => 'LBL_SALESDOCS',
    ];
}

