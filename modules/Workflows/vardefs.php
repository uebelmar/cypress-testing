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

$dictionary['Workflow'] = array(
    'table' => 'workflows',
    'audited' => true,
    'fields' => array(
        'workflow_status' => array(
            'required' => false,
            'name' => 'workflow_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'dbType' => 'varchar',
            'options' => 'workflowstatus_dom',
            'len' => '10',
            'reportable' => true,
            'massupdate' => false,
            'audited' => true,
        ),
        // Relation to Modules
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'varchar',
            'group' => 'parent_name',
            'required' => false,
            'len' => '25',
            'reportable' => true,
            'massupdate' => false
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => true,
            'vname' => 'LBL_PARENT_ID',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_RELATED_TO',
            'type' => 'parent',
            'group' => 'parent_name',
            'source' => 'non-db',
            'options' => 'parent_type_display_workflow',
            'massupdate' => false
        ),
        // Relation to KworkFlowDefinition
        'workflowdefinition_id' => array(
            'name' => 'workflowdefinition_id',
            'vname' => 'LBL_WORKFLOWDEFINITION_ID',
            'type' => 'varchar',
            'len' => '36',
            'required' => false,
            'massupdate' => false
        ),
        'workflowdefinition_name' => array(
            'name' => 'workflowdefinition_name',
            'vname' => 'LBL_WORKFLOWDEFINITION_NAME',
            'rname' => 'name',
            'type' => 'relate',
            'source' => 'non-db',
            'save' => true,
            'id_name' => 'workflowdefinition_id',
            'link' => 'workflowdefinition_link',
            'table' => 'workflowdefinitons',
            'module' => 'WorkflowDefinitions',
            'massupdate' => false
        ),
        'workflowdefinition_link' => array(
            'name' => 'workflowdefinition_link',
            'vname' => 'LBL_WORKFLOWDEFINITION_LINK',
            'type' => 'link',
            'relationship' => 'workflow_workflowdefinition',
            'module' => 'WorkflowDefinitions',
            'bean_name' => 'WorkflowDefinition',
            'source' => 'non-db',
        ),
        'workflowtasks_link' => array(
            'name' => 'workflowtasks_link',
            'vname' => 'LBL_KWORKFLOWTASKS_LINK',
            'type' => 'link',
            'relationship' => 'workflowtask_workflow',
            'module' => 'Workflows',
            'bean_name' => 'Workflow',
            'source' => 'non-db',
        ),
    ),
    'relationships' => array(
        'workflow_workflowdefinition' => array(
            'lhs_module' => 'WorkflowDefinitions',
            'lhs_table' => 'workflowdefinitions',
            'lhs_key' => 'id',
            'rhs_module' => 'Workflows',
            'rhs_table' => 'workflows',
            'rhs_key' => 'workflowdefinition_id',
            'relationship_type' => 'one-to-many',
        ),
        'workflowtask_workflow' => array(
            'lhs_module' => 'Workflows',
            'lhs_table' => 'workflows',
            'lhs_key' => 'id',
            'rhs_module' => 'WorkflowTasks',
            'rhs_table' => 'workflowtasks',
            'rhs_key' => 'workflow_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    'indices' => array(
        'parent_idx' => array('name' => 'parent_idx', 'type' => 'index', 'fields' => array('parent_id'),),
        'parent_del_idx' => array('name' => 'parent_del_idx', 'type' => 'index', 'fields' => array('parent_id', 'parent_type', 'deleted'),),
        'workflowdefinition_id_idx' => array('name' => 'workflowdefinition_id_idx', 'type' => 'index', 'fields' => array('workflowdefinition_id'),),
        'workflow_status_idx' => array('name' => 'workflow_status_idx', 'type' => 'index', 'fields' => array('workflow_status'),),
    ),
    'optimistic_lock' => true,
);


VardefManager::createVardef('Workflows', 'Workflow', array('basic', 'assignable'));
