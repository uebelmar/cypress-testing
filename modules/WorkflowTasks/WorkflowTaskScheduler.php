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

namespace SpiceCRM\modules\WorkflowTasks;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

class WorkflowTaskScheduler{

    public function runScheduledTasks(){
        global $timedate;
$db = DBManagerFactory::getInstance();
        $nowDB = $timedate->nowDb();
        $scheduledTasks = $db->query("SELECT workflowtasks.id FROM workflowtasks, workflows where workflows.id = workflowtasks.workflow_id AND date_start <= '$nowDB' AND workflows.deleted = 0 AND workflowtasks.deleted = 0 AND workflowtask_status = '5'");
        while($scheduledTask = $db->fetchByAssoc($scheduledTasks)){
            $workflowTask = BeanFactory::getBean('WorkflowTasks', $scheduledTask['id']);
            $workflowTask->workflowtask_status = '10';
            $workflowTask->save();
        }
    }
}
