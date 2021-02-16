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

$dictionary['WorkflowTaskDefinition'] = array(
    'table' => 'workflowtaskdefinitions',
    'audited' => true,
    'fields' => array(
        'workflowdefinition_id' => array(
            'name' => 'workflowdefinition_id',
            'type' => 'id'
        ),
        'sequence' => array(
            'name' => 'sequence',
            'type' => 'varchar',
            'vname' => 'LBL_SEQUENCE',
            'len' => 3
        ),
        'primarytask' => array(
            'name' => 'primarytask',
            'vname' => 'LBL_PRIMARY',
            'type' => 'bool'
        ),
        'ishidden' => array(
            'name' => 'ishidden',
            'type' => 'bool'
        ),
        'allowcomment' => array(
            'name' => 'allowcomment',
            'type' => 'bool'),
        'closetask' => array(
            'name' => 'closetask',
            'type' => 'bool'
        ),
        'prevclosedreq' => array(
            'name' => 'prevclosedreq',
            'type' => 'bool'
        ),
        'previoustask' => array(
            'name' => 'previoustask',
            'vname' => 'LBL_PREVIOUSTASK',
            'type' => 'id'
        ),
        'nexttask' => array(
            'name' => 'nexttask',
            'vname' => 'LBL_NEXTTASK',
            'type' => 'id'
        ),
        'decisionoptions' => array(
            'name' => 'decisionoptions',
            'type' => 'text'
        ),
        'tasktype' => array(
            'name' => 'tasktype',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'workflowftastktypes_dom'
        ),
        'assigntouser' => array(
            'name' => 'assigntouser',
            'type' => 'id'
        ),
        'assignparent' => array(
            'name' => 'assignparent',
            'type' => 'bool'
        ),
        'assigntype' => array(
            'name' => 'assigntype',
            'type' => 'enum',
            'options' => 'workflowdefinition_assgintotypes'
        ),
        'assignclass' => array(
            'name' => 'assignclass',
            'type' => 'backendmethod',
            'dbtype' => 'varchar',
        ),
        'assignparams' => array(
            'name' => 'assignparams',
            'type' => 'text'
        ),
        'sendemail' => array(
            'name' => 'sendemail',
            'type' => 'bool'),

        'mailbox' => array(
            'name' => 'mailbox',
            'vname' => 'LBL_MAILBOX',
            'type' => 'mailbox',
            'dbtype' => 'varchar',
            'length' => 32
        ),
        'emailtype' => array(
            'name' => 'emailtype',
            'type' => 'enum',
            'options' => 'workflowdefinition_emailtypes',
            'length' => 1
        ),
        'emailto' => array(
            'name' => 'emailto',
            'type' => 'varchar',
            'length' => 100
        ),
        'emailtemplate' => array(
            'name' => 'emailtemplate',
            'type' => 'id'
        ),
        'emailtemplate_name' => array(
            'name' => 'emailtemplate_name',
            'rname' => 'name',
            'id_name' => 'emailtemplate',
            'vname' => 'LBL_EMAILTEMPLATE',
            'type' => 'relate',
            'table' => 'email_templates',
            'isnull' => 'true',
            'module' => 'EmailTemplates',
            'dbType' => 'varchar',
            'link' => 'emailtemplates',
            'len' => '255',
            'source' => 'non-db',
        ),
        'emailtemplates' => array(
            'name' => 'emailtemplates',
            'type' => 'link',
            'relationship' => 'workflowtaskdefinitions__emailtemplate',
            'source' => 'non-db',
            'module' => 'EmailTemaplates'
        ),
        'emailclass' => array(
            'name' => 'emailclass',
            'type' => 'backendmethod',
            'dbtype' => 'varchar',
        ),
        'emailcontclass' => array(
            'name' => 'emailcontclass',
            'type' => 'backendmethod',
            'dbtype' => 'varchar',
        ),
        'emailparams' => array(
            'name' => 'emailparams',
            'type' => 'text'
        ),
        'sysclass' => array(
            'name' => 'sysclass',
            'type' => 'backendmethod',
            'dbtype' => 'varchar',
        ),
        'sysparams' => array(
            'name' => 'sysparams',
            'type' => 'text'
        ),
        'timetostart' => array(
            'name' => 'timetostart',
            'vname' => 'LBL_TIMETOSTART',
            'type' => 'timedifference',
            'dbtype' => 'varchar',
            'len' => 10
        ),
        'timetocomplete' => array(
            'name' => 'timetocomplete',
            'vname' => 'LBL_TIMETOCOMPLETE',
            'type' => 'timedifference',
            'dbtype' => 'varchar',
            'len' => 10
        ),
        'timetoalert' => array(
            'name' => 'timetoalert',
            'vname' => 'LBL_TIMETOALERT',
            'type' => 'timedifference',
            'dbtype' => 'varchar',
            'len' => 10
        ),
        'timetoescalate' => array(
            'name' => 'timetoescalate',
            'vname' => 'LBL_TIMETOESCALATE',
            'type' => 'timedifference',
            'dbtype' => 'varchar',
            'len' => 10
        )
    ),
    'relationships' => array(
        'workflowdefinitions_workflowtaskdefinitions' => array(
            'rhs_module' => 'WorkflowDefinitions',
            'rhs_table' => 'workflowdefinitions',
            'rhs_key' => 'id',
            'lhs_module' => 'WorkflowTaskDefinitions',
            'lhs_table' => 'workflowtaskdefinitions',
            'lhs_key' => 'workflowdefinition_id',
            'relationship_type' => 'one-to-many'
        ),
        'workflowtaskdefinitions__emailtemplate' => array(
            'lhs_module' => 'EmailTemplates',
            'lhs_table' => 'email_templates',
            'lhs_key' => 'id',
            'rhs_module' => 'WorkflowTaskDefinitions',
            'rhs_table' => 'workflowtaskdefinitions',
            'rhs_key' => 'emailtemplate',
            'relationship_type' => 'one-to-many'
        )
    ),
    'indices' => array(
        array(
            'name' => 'id_deleted',
            'type' => 'index',
            'fields' => array('id', 'deleted')
        ),
        array(
            'name' => 'workflowdefinition_id',
            'type' => 'index',
            'fields' => array('workflowdefinition_id')
        )
    )
);

//
VardefManager::createVardef('WorkflowTaskDefinitions', 'WorkflowTaskDefinition', array(
    'basic',
    'default'
));
