<?php
/* * *******************************************************************************
 * This file is part of Workflow. Workflow is an enhancement developed
 * by AAC s.r.o. All rights are (c) 2014 by AAC s.r.o
 *
 * This Version of the Workflow is licensed software and may only be used in
 * alignment with the License Agreement received with this Software.
 * This Software is copyrighted and may not be further distributed without
 * witten consent of AAC s.r.o
 *
 * You can contact us at office@all-about-crm.com
 * ****************************************************************************** */

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['WorkflowTask'] = array(
    'table' => 'workflowtasks',
    'audited' => true,
    'fields' => array(
        'workflowparenttask_id' => array(
            'name' => 'workflowparenttask_id',
            'vname' => 'LBL_KWORKFLOWPARENTTASK_ID',
            'type' => 'varchar',
            'len' => '36',
            'required' => false,
            'massupdate' => false
        ),
        'workflowtaskdefinition_id' => array(
            'name' => 'workflowtaskdefinition_id',
            'vname' => 'LBL_KWORKFLOWTASKDEFINITION_ID',
            'type' => 'varchar',
            'len' => '36',
            'required' => true,
            'massupdate' => false
        ),
        'date_start' => array(
            'name' => 'date_start',
            'vname' => 'LBL_DATE_START',
            'type' => 'datetime'
        ),
        'date_due' => array(
            'name' => 'date_due',
            'vname' => 'LBL_DATE_DUE',
            'type' => 'datetime'
        ),
        'date_completed' => array(
            'name' => 'date_completed',
            'vname' => 'LBL_DATE_COMPLETED',
            'type' => 'datetime'
        ),
        'workflowtask_status' => array(
            'name' => 'workflowtask_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'workflowtask_status',
            'len' => 25
        ),
        'taskcomment' => array(
            'name' => 'taskcomment',
            'vname' => 'LBL_COMMENT',
            'type' => 'text'
        ),
        'add_comment' => array(
            'name' => 'add_comment',
            'vname' => 'LBL_ADD_COMMENT',
            'type' => 'text',
            'source' => 'non-db'
        ),
        'workflow_id' => array(
            'name' => 'workflow_id',
            'vname' => 'LBL_KWORKFLOW_ID',
            'type' => 'varchar',
            'len' => '36'
        ),
        'workflow_name' => array(
            'name' => 'workflow_name',
            'type' => 'relate',
            'source' => 'non-db',
            'vname' => 'LBL_KWORKFLOW_NAME',
            'save' => true,
            'id_name' => 'workflow_id',
            'link' => 'workflow_link',
            'table' => 'workflows',
            'module' => 'Workflows',
            'rname' => 'name'
        ),
        'workflow_link' => array(
            'name' => 'workflow_link',
            'vname' => 'LBL_KWORKFLOW_LINK',
            'type' => 'link',
            'relationship' => 'workflowtask_workflow',
            'module' => 'Workflows',
            'bean_name' => 'Workflow',
            'source' => 'non-db'
        ),
        'assigned_users_link' => array(
            'name' => 'assigned_users_link',
            'vname' => 'LBL_ASSIGNED_USERS_LINK',
            'type' => 'link',
            'relationship' => 'workflowtasks_users',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db'
        ),
        'workflowtaskcomments' => array(
            'name' => 'workflowtaskcomments',
            'vname' => 'LBL_KWORKFLOWTASKCOMMENTS_LINK',
            'type' => 'link',
            'relationship' => 'workflowtaskcomments_workflowtask',
            'module' => 'WorkflowTaskComments',
            'bean_name' => 'WorkflowTaskComment',
            'source' => 'non-db',
            'default' => true
        ),
        'task_decision' => array(
            'name' => 'task_decision',
            'vname' => 'LBL_TASK_DECISION',
            'type' => 'varchar',
            'len' => 36
        ),
        // Relation to Modules
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'varchar',
            'group' => 'parent_name',
            'len' => '25',
            'source' => 'non-db'
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => false,
            'vname' => 'LBL_PARENT_ID',
            'source' => 'non-db'
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => false,
            'vname' => 'LBL_PARENT_NAME',
            'source' => 'non-db'
        )
    ),
    'indices' => array(
        'idx_workflowtask_delete_pk' => array('name' => 'idx_workflowtask_delete_pk', 'type' => 'index', 'fields' => array('id', 'deleted')),
        'idx_workflowtaskdefinition_id' => array('name' => 'idx_workflowtaskdefinition_id', 'type' => 'index', 'fields' => array('workflowtaskdefinition_id', 'deleted'),),
        'idx_workflowtask_workflow_id' => array('name' => 'idx_workflowtask_workflow_id', 'type' => 'index', 'fields' => array('workflow_id')),
    // 'parenttask_workflow_id' => array('name' => 'workflowtask_pk', 'type' => 'index', 'fields' => array('workflow_id', 'parenttask_id')),
    ),
    'relationships' => array(
        'workflowtask_workflow' =>
        array(
            'lhs_module' => 'Workflows',
            'lhs_table' => 'workflows',
            'lhs_key' => 'id',
            'rhs_module' => 'WorkflowTasks',
            'rhs_table' => 'workflowtasks',
            'rhs_key' => 'workflow_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    'optimistic_lock' => true,
);


VardefManager::createVardef('WorkflowTasks', 'WorkflowTask', array('basic', 'assignable'));

