<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['WorkflowCondition'] = array(
    'table' => 'workflowconditions',
    'audited' => true,
    'fields' => array(
        'object_field' => array(
            'required' => false,
            'name' => 'object_field',
            'type' => 'varchar',
            'len' => '50',
        ),
        'object_operator' => array(
            'required' => false,
            'name' => 'object_operator',
            'type' => 'enum',
            'options' => 'workflowdefinition_conditionoperators',
            'len' => '10',
        ),
        'object_value' => array(
            'required' => false,
            'name' => 'object_value',
            'type' => 'varchar',
            'len' => '255',
        ),
        'on_change' => array(
            'required' => false,
            'name' => 'on_change',
            'type' => 'bool'
        ),
        'workflowdefinition_id' =>
            array(
                'name' => 'workflowdefinition_id',
                'type' => 'id'
            )
    ),
    'relationships' => array(
        'kwokflowdefintion_workflowtaskdefinitions' => array(
            'lhs_module' => 'WorkflowDefinitions',
            'lhs_table' => 'workflowdefinitions',
            'lhs_key' => 'id',
            'rhs_module' => 'WorkflowConditions',
            'rhs_table' => 'workflowdefintions',
            'rhs_key' => 'workflowdefintions_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_lock' => true,
    'indices' => array(
        array('name' => 'id_deleted', 'type' => 'index', 'fields' => array('id', 'deleted')),
        array('name' => 'workflowdefinition_id', 'type' => 'index', 'fields' => array('workflowdefinition_id'))
    )
);

VardefManager::createVardef('WorkflowConditions', 'WorkflowCondition', array(
    'basic',
    'default'
));
