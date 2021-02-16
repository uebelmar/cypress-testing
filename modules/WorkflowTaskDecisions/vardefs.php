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

$dictionary['WorkflowTaskDecision'] = array(
    'table' => 'workflowtaskdecisions',
    'audited' => true,
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'workflowtaskdefinition_id' => array(
            'name' => 'workflowtaskdefinition_id',
            'type' => 'id'
        ),
        'name' => array(
            'name' => 'name',
            'type' => 'varchar',
            'len' => 250
        ),
        'nextworkflowtaskdefinition_id' => array(
            'name' => 'nextworkflowtaskdefinition_id',
            'type' => 'id'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool'
        ),
    ),
    'indices' => array(
        array('name' => 'id_deleted', 'type' => 'index', 'fields' => array('id', 'deleted')),
        array('name' => 'workflowtaskdefinition_id', 'type' => 'index', 'fields' => array('workflowtaskdefinition_id')),
        array('name' => 'workflowtaskdefinition_id_deleted', 'type' => 'index', 'fields' => array('workflowtaskdefinition_id', 'deleted'))
    )
);

VardefManager::createVardef('WorkflowTaskDecisions', 'WorkflowTaskDecision', array(
    'basic',
    'default'
));
