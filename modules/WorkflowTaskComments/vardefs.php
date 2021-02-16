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

$dictionary['WorkflowTaskComment'] = array(
    'table' => 'workflowtaskcomments',
    'audited' => true,
    'fields' => array(
        'workflowtask_id' => array(
            'name' => 'workflowtask_id',
            'vname' => 'LBL_KWORKFLOWTASK_ID',
            'type' => 'varchar',
            'len' => '36',
            'required' => false,
            'massupdate' => false
        ),
        'workflowtask' => array(
            'name' => 'workflowtask',
            'vname' => 'LBL_KWORKFLOWTASK',
            'type' => 'link',
            'relationship' => 'workflowtaskcomments_workflowtask',
            'module' => 'WorkflowTasks',
            'bean_name' => 'WorkflowTask',
            'source' => 'non-db',
            'massupdate' => false
        )
    ),
    'indices' => array(
        'idx_kwc_delete_pk' => array('name' => 'idx_kwc_delete_pk', 'type' => 'index', 'fields' => array('id', 'deleted')),
        'idx_kwc_workflowtask_id' => array('name' => 'idx_kwc_workflowtask_id', 'type' => 'index', 'fields' => array('workflowtask_id'))
    ),
    'relationships' => array(
        'workflowtaskcomments_workflowtask' =>
        array(
            'lhs_module' => 'WorkflowTasks',
            'lhs_table' => 'workflowtasks',
            'lhs_key' => 'id',
            'rhs_module' => 'WorkflowTaskComments',
            'rhs_table' => 'workflowtaskcomments',
            'rhs_key' => 'workflowtask_id',
            'relationship_type' => 'one-to-many',
        )
    ),
    'optimistic_lock' => true
);


VardefManager::createVardef('WorkflowTaskComments', 'WorkflowTaskComment', array('default','basic'));
