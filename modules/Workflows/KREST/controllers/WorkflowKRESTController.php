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
namespace SpiceCRM\modules\Workflows\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\includes\authentication\AuthenticationController;

class WorkflowKRESTController
{
    /**
     * returns the tasky for the current user
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    function getMyTasks($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        $returnArray = array();

        $tasks = $db->query("SELECT wt.id, wt.date_entered, wt.workflowtask_status, wt.name, wf.name wfname, wf.parent_id, wf.parent_type FROM workflowtasks wt, workflows wf WHERE wt.workflow_id = wf.id AND wt.assigned_user_id = '$current_user->id' AND wt.deleted = 0 AND wt.workflowtask_status < 30");
        while ($task = $db->fetchByAssoc($tasks)) {
            $seed = BeanFactory::getBean($task['parent_type'], $task['parent_id']);
            if ($seed) {
                $task['parent_name'] = $seed->get_summary_text();
                $returnArray[] = $task;
            }
        }
        return $res->withJson($returnArray);
    }


    /**
     * returns the workflows for a given bean
     *
     * @param $req
     * @param $res
     * @param $args
     * @return false|string
     */
    function getRelatedWorkflows($req, $res, $args)
    {
        $wf = BeanFactory::getBean('Workflows');
        if (!$wf) {
            return $res->withJson(array());
        }

        $returnArray = array();
        $workflows = $wf->getWorkflowsForParentId($args['id'], $args['module']);
        foreach ($workflows as $thisWorkflowId => $thisWokflowDefinitionId) {
            $workflow = BeanFactory::getBean('Workflows', $thisWorkflowId);
            $returnArray[] = $workflow->getWorkflowDetails();
        }

        return $res->withJson($returnArray);
    }


    /**
     * sets the status of a given Task
     *
     * @param $req
     * @param $res
     * @param $args
     * @return false|string
     */
    function setTaskStatus($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        // $postParams = json_decode($_POST, true);
        $postParams = $req->getParsedBody();

        $wft = BeanFactory::getBean('WorkflowTasks', $args['id']);

        // check if the current user is an ssigned user to the task .. otherwise
        if(!$wft->checkAssignedUser()){
            row (new ForbiddenException("not the assigned user"));
        }

        if (strlen($args['status']) <= 2) {
            $wft->workflowtask_status = $args['status'];
        } else {
            $wft->setDecision($args['status']);
        }
        if (!empty($postParams['comment'])) {
            $newComment = BeanFactory::getBean('WorkflowTaskComments');
            $newComment->workflowtask_id = $wft->id;
            $newComment->description = $postParams['comment'];
            $newComment->name = substr($postParams['comment'], 0, 50) . (strlen($postParams['comment']) > 50 ? '...' : '');
            $newComment->created_by = $current_user->id;
            $newComment->modified_user_id = $current_user->id;
            $newComment->save();
        }

        $wft->save();

        // return the workflow
        $workflow = BeanFactory::getBean('Workflows', $wft->workflow_id);

        // require_once('KREST/handlers/ModuleHandler.php');
        $modulehandler = new ModuleHandler();
        $parentBean = BeanFactory::getBean($workflow->parent_type, $workflow->parent_id);
        $parentData = $modulehandler->mapBeanToArray($workflow->parent_type, $parentBean);

        return $res->withJson(['workflow' => $workflow->getWorkflowDetails(), 'parent' => $parentData]);
    }


    /**
     * adds a comment to a task
     *
     * @param $req
     * @param $res
     * @param $args
     * @return false|string
     */
    function addTaskComment($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        // $postParams = json_decode($_POST, true);
        $postParams = $req->getParsedBody();

        $wft = BeanFactory::getBean('WorkflowTasks', $args['id']);

        // check if the current user is an ssigned user to the task .. otherwise
        if(!$wft->checkAssignedUser()){
            row (new ForbiddenException("not the assigned user"));
        }

        $newComment = BeanFactory::getBean('WorkflowTaskComments');
        $newComment->workflowtask_id = $args['id'];
        $newComment->description = $postParams['comment'];
        $newComment->name = substr($postParams['comment'], 0, 50) . (strlen($postParams['comment']) > 50 ? '...' : '');
        $newComment->created_by = $current_user->id;
        $newComment->modified_user_id = $current_user->id;
        $newComment->save();

        // return the workflow
        $workflow = BeanFactory::getBean('Workflows', $wft->workflow_id);
        return $res->withJson($workflow->getWorkflowDetails());
    }


    /**
     * sets a workflow as closed
     *
     * @param $req
     * @param $res
     * @param $args
     * @return false|string
     */
    function closeWorkflow($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        // check if the current user is an ssigned user to the task .. otherwise
        if(!$current_user->is_admin){
            row (new ForbiddenException("not authorized to close workflows"));
        }

        $wf = BeanFactory::getBean('Workflows', $args['id']);
        $wf->setWorkflowClosed(true);

        return $res->withJson($wf->getWorkflowDetails());
    }


}
