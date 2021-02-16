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

namespace SpiceCRM\modules\Workflows;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SysModuleFilters\SysModuleFilters;

class WorkflowScheduler{

    public function rundScheduledWorkflows(){
        global $timedate;
$db = DBManagerFactory::getInstance();

        $workflow = BeanFactory::getBean('Workflows');
        $scheduledWorkflows = $db->query("SELECT id FROM workflowdefinitions where workflowdefinition_status like 'active_scheduled%' AND deleted = 0");
        while($scheduledWorkflow = $db->fetchByAssoc($scheduledWorkflows)){
            $workflowDefinition = BeanFactory::getBean('WorkflowDefinitions', $scheduledWorkflow['id']);

            $seed = BeanFactory::getBean($workflowDefinition->workflowdefinition_module);

            $filterGroup = json_decode(html_entity_decode($workflowDefinition->conditions));
            $filter = new SysModuleFilters();
            $sqlFilter = $filter->buildSQLWhereClauseForGroup($filterGroup, $seed->table_name);

            $candidatesQuery = "SELECT id FROM {$seed->table_name} WHERE $sqlFilter AND deleted = 0";
            if($workflowDefinition->workflowdefinition_status == 'active_scheduled_once'){
                $candidatesQuery .= " AND NOT EXISTS (SELECT id FROM workflows WHERE deleted <> 1 AND parent_type = '{$workflowDefinition->workflowdefinition_module}' AND parent_id = {$seed->table_name}.id AND workflowdefinition_id='{$workflowDefinition->id}')";
            }

            $candidatesObj = $db->query($candidatesQuery);
            while($candidate = $db->fetchByAssoc($candidatesObj)){
                if($seed->retrieve($candidate['id'])) {

                    $newWorkFlow = $workflow->initializeWorkflow($workflowDefinition, $seed);
                    $newWorkFlow->save();
                    $newWorkFlow->createPrimaryTasks();
                }
            }
        }
    }
}
