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

$dictionary['WorkflowDefinition'] = array(
    'table' => 'workflowdefinitions',
    'audited' => true,
    'fields' => array(
        'workflowdefinition_module' => array(
            'required' => false,
            'name' => 'workflowdefinition_module',
            'type' => 'varchar',
            'len' => '50',
            'vname' => 'LBL_KWORKFLOWDEFINITIONMODULE',
        ),
        'display_name' => array(
            'required' => false,
            'name' => 'display_name',
            'type' => 'varchar',
            'len' => '255',
            'vname' => 'LBL_DISPLAY_NAME',
        ),
        'workflowdefinition_status' => array(
            'required' => false,
            'name' => 'workflowdefinition_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'workflowdefinition_status',
            'len' => '25', /* active, activerunonce, active_scheduled, inactive */
        ),
        'workflowdefinition_priority' => array(
            'name' => 'workflowdefinition_priority',
            'type' => 'int',
            'vname' => 'LBL_PRIORITY'
        ),
        'workflowtaskdefinitions' => array(
            'name' => 'workflowtaskdefinitions',
            'type' => 'link',
            'vname' => 'LBL_KWORKFLOWTASKDEFINITIONS',
            'relationship' => 'workflowdefinitions_workflowtaskdefinitions',
            'source' => 'non-db',
        ),
        'workflowdefinition_precond' => array(
            'name' => 'workflowdefinition_precond',
            'vname' => 'LBL_PRECONDITION',
            'type' => 'enum',
            'len' => 1,
            'options' => 'workflowdefinition_precondition'
        ),
        'workflowdefinition_exclusive' => array(
            'name' => 'workflowdefinition_exclusive',
            'type' => 'bool',
            'vname' => 'LBL_EXCLUSIVE'
        ),
        'conditions' => array(
            'name' => 'conditions',
            'vname' => 'LBL_CONDITIONS',
            'type' => 'json',
            'dbtype' => 'longtext',
        ),
        'condclass' => array(
            'name' => 'condclass',
            'vname' => 'LBL_CLASS',
            'type' => 'backendmethod',
            'dbtype' => 'varchar',
            'len' => 100
        ),
        'condparams' => array(
            'name' => 'condparams',
            'vname' => 'LBL_PARAMS',
            'type' => 'text'
        ),
        'conditions_end' => array(
            'name' => 'conditions_end',
            'vname' => 'LBL_CONDITIONS_END',
            'type' => 'json',
            'dbtype' => 'longtext',
        ),
        'condclass_end' => array(
            'name' => 'condclass_end',
            'vname' => 'LBL_CLASS_END',
            'type' => 'backendmethod',
            'dbtype' => 'varchar',
            'len' => 100
        ),
        'condparams_end' => array(
            'name' => 'condparams_end',
            'vname' => 'LBL_PARAMS',
            'type' => 'text'
        ),
        'workflowtaskdefinition_link' => array(
            'name' => 'workflowtaskdefinition_link',
            'type' => 'link',
            'relationship' => 'workflowdefinitions_workflowtaskdefinitions',
            'link_type' => 'one',
            // 'side' => 'right',
            'source' => 'non-db',
            'default' => true
        )
    ),
    'optimistic_lock' => true,
);



VardefManager::createVardef('WorkflowDefinitions', 'WorkflowDefinition', array(
    'basic',
    'default'
));

