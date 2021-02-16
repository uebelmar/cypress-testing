<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SystemDeploymentRelease'] = [
    'table' => 'systemdeploymentreleases',
    'comment' => 'SystemDeploymentReleases Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,
	
	'fields' => [
	    'release_status' => [
	        'name' => 'release_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 32,
            'options' => 'deploymentrelease_status_dom',
            'comment' => 'status of release'
        ],
        'systemdeploymentcrs' => [
            'name' => 'systemdeploymentcrs',
            'vname' => 'LBL_SYSTEMDEPLOYMENTCRS',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'systemdeploymentrelease_systemdeploymentcrs',
            'module' => 'SystemDeploymentCRs',
        ],
        // BEGIN CR1000333
        'users' => [
            'name' => 'users',
            'type' => 'link',
            'relationship' => 'systemdeploymentreleases_users',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
            'module' => 'Users',
            'bean_name' => 'User',
            'default' => false,
            'comment' => 'user(s) in charge of testing environment'
        ],
        'planned_date_dev_start' => [
            'name' => 'planned_date_dev_start',
            'type' => 'datetime',
            'vname' => 'LBL_PLANNED_DATE_DEV_START'
        ],
        'planned_date_dev_closed' => [
            'name' => 'planned_date_dev_closed',
            'type' => 'datetime',
            'vname' => 'LBL_PLANNED_DATE_DEV_CLOSED',
            'comment' => 'date and time until which development shall be completed'
        ],
        'planned_date_release_closed' => [
            'name' => 'planned_date_release_closed',
            'type' => 'date',
            'vname' => 'LBL_PLANNED_DATE_RELEASE_CLOSED',
            'comment' => 'date when release shall be completed'
        ],
        // END CR1000333

        // BEGIN CR1000336
        'parent_id' => [
            'name'       => 'parent_id',
            'vname'      => 'LBL_LIST_RELATED_TO_ID',
            'type'       => 'id',
            'group'      => 'parent_name',
            'reportable' => false,
            'comment'    => 'The ID of the parent Sugar object identified by parent_type'
        ],
        'parent_type' => [
            'name'     => 'parent_type',
            'vname'    => 'LBL_PARENT_TYPE',
            'type'     => 'parent_type',
            'dbType'   => 'varchar',
            'required' => false,
            'group'    => 'parent_name',
//            'options'  => 'deploymentrelease_parent_type_display',
            'len'      => 255,
            'comment'  => 'The Sugar object to which the call is related',
        ],
        'parent_name' => [
            'name'        => 'parent_name',
            'parent_type' => 'deploymentrelease_record_type_display',
            'type_name'   => 'parent_type',
            'id_name'     => 'parent_id',
            'vname'       => 'LBL_RELATED_TO',
            'type'        => 'parent',
            'group'       => 'parent_name',
            'source'      => 'non-db',
//            'options'     => 'deploymentrelease_parent_type_display',
        ],

    ],
	'relationships' => [
	    'systemdeploymentrelease_systemdeploymentcrs' => [
            'lhs_module' => 'SystemDeploymentReleases',
            'lhs_table' => 'systemdeploymentreleases',
            'lhs_key' => 'id',
            'rhs_module' => 'SystemDeploymentCRs',
            'rhs_table' => 'systemdeploymentcrs',
            'rhs_key' => 'systemdeploymentrelease_id',
            'relationship_type' => 'one-to-many',
        ]
	],
	'indices' => [
        ['name' => 'idx_release_status_del', 'type' => 'index', 'fields' => ['release_status', 'deleted']],
    ]
];

VardefManager::createVardef('SystemDeploymentReleases', 'SystemDeploymentRelease', ['default', 'assignable']);
