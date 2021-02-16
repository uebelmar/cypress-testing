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

$dictionary['WorkflowSystemAction'] = array(
    'table' => 'workflowsystemactions',
    'audited' => true,
    'fields' => array(
        'field_name' => array(
            'name' => 'field_name',
            'type' => 'varchar',
            'len' => '50',
        ),
        'field_value' => array(
            'name' => 'field_value',
            'type' => 'varchar',
            'len' => '50',
        ),
        'workflowtask_status' => array(
            'name' => 'workflowtask_status',
            'vname' => 'LBL_KWORKFLOWTASK_STATUS',
            'type' => 'enum',
            'options' => 'workflowtask_status',
            'audited' => true,
            'len' => 25
        ),
        'workflowtaskdefinition_id' =>
            array(
                'name' => 'workflowtaskdefinition_id',
                'type' => 'id'
            ),
        'workflowtaskdefinition_link' => array(
            'name' => 'workflowtaskdefinition_link',
            'type' => 'link',
            'relationship' => 'workflowtaskdefinition_workflowsystemactions',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db'
        )
    ),
    'relationships' => array(
        'workflowtaskdefinition_workflowsystemactions' => array(
            'lhs_module' => 'WorkflowTaskDefinitions',
            'lhs_table' => 'workflowtaskdefinitions',
            'lhs_key' => 'id',
            'rhs_module' => 'WorkflowSystemActions',
            'rhs_table' => 'workflowsystemactions',
            'rhs_key' => 'workflowtaskdefinition_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_lock' => true,
    'indices' => array(

        array('name' => 'workflowtaskdefinition_id', 'type' => 'index', 'fields' => array('workflowtaskdefinition_id')),
        array('name' => 'workflowtaskdefinition_id_deleted', 'type' => 'index', 'fields' => array('workflowtaskdefinition_id', 'deleted')),
        array('name' => 'id_deleted', 'type' => 'index', 'fields' => array('id', 'deleted'))
    )
);

VardefManager::createVardef('WorkflowSystemActions', 'WorkflowSystemAction', array(
    'basic',
    'default'
));
