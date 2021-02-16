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

use DateInterval;
use DateTime;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\templates\basic\Basic;
use SpiceCRM\includes\authentication\AuthenticationController;

class WorkflowTask extends Basic {

    var $new_schema = true;
    var $module_dir = 'WorkflowTasks';
    var $object_name = 'WorkflowTask';
    var $table_name = 'workflowtasks';
    var $workflow;
    var $workflowdefinition;
    var $kWorkflowTaskList = array();

    var $createSteps = array(
        'setDueDates',
        'determineAssignedUser'
    );
    var $preSaveSteps = array(
        'setSystemValues'
    );


    public function retrieve($id = -1, $encode = true, $deleted = true, $relationships = true) {
        $retVal = parent::retrieve($id, $encode, $deleted, $relationships);

        if ($retVal) {
            if (!empty($this->workflowtaskdefinition_id)) {
                $this->workflowdefinition = BeanFactory::getBean('WorkflowTaskDefinitions', $this->workflowtaskdefinition_id);
                $this->sequence = $this->workflowdefinition->sequence;
            }
            if (!empty($this->workflow_id)) {
                $this->workflow = BeanFactory::getBean('Workflows', $this->workflow_id);
                $this->parent_name = $this->workflow->parent_name;
                $this->parent_id = $this->workflow->parent_id;
                $this->parent_type = $this->workflow->parent_type;
            }
        }

        if ($this->assigned_user_id == 'multiple')
            $this->assigned_user_name = 'multiple';
        
        return $retVal;
    }


    public function getStatus() {
        if (empty($thisWorkflowTask->task_decision))
            return $thisWorkflowTask->workflowtask_status;
        else {
            $kworkFlowTaskDecision = BeanFactory::getBean('WorkflowTaskDecisions', $thisWorkflowTask->task_decision);
            return $kworkFlowTaskDecision->name;
        }
    }


    public function getAssignedUserQuery(){
        
    }
    
    function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false, $ifListForExport = false) {
        return parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect, $ifListForExport);
    }

    function fill_in_additional_list_fields() {
        $this->retrieve($this->id);


        // if($this->assigned_user_id == 'multiple') 
        $this->assigned_user_name = 'multiple';
    }

    public function createFromWorkflowDefiniton($thisWorkflow, $thisWorkflowDefinition) {

        global $timedate;

        $this->id = create_guid();
        $this->new_with_id = true;

        $this->workflowdefinition = $thisWorkflowDefinition;
        $this->workflow = $thisWorkflow;
        
        // also set parent information 
        $this->parent_name = $this->workflow->parent_name;
        $this->parent_id = $this->workflow->parent_id;
        $this->parent_type = $this->workflow->parent_type;

        // chek for timing
        $this->name = $thisWorkflowDefinition->name;
        $this->workflowtaskdefinition_id = $thisWorkflowDefinition->id;

        if(!empty($thisWorkflowDefinition->timetostart)){
            $this->workflowtask_status = '5';

            $interval = explode(' ',$thisWorkflowDefinition->timetostart);

            // get now and convert to UTC
            $dateStart = new DateTime();
            $dateStart->setTimezone(new DateTimeZone('UTC'));

            switch ($interval[1]){
                case 'i':
                    $dateStart->add(new DateInterval('PT'.$interval[0].'M'));
                    break;
                case 'h':
                    $dateStart->add(new DateInterval('PT'.$interval[0].'H'));
                    break;
                default:
                    $dateStart->add(new DateInterval('P'.$interval[0].strtoupper($interval[1])));
                    break;
            }

            $this->date_start = $dateStart->format($timedate->get_db_date_time_format());
        } else {
            $this->workflowtask_status = '10';
        }
        $this->workflow_id = $thisWorkflow->id;

        foreach ($this->createSteps as $thisStep)
            $this->$thisStep();
    }



    public function determineAssignedUser() {
        global $beanFiles, $beanList;

        $parentBean = BeanFactory::getBean($this->workflow->parent_type, $this->workflow->parent_id);

        switch ($this->workflowdefinition->assigntype) {
            case '3':
                $this->assigned_user_id = $parentBean->assigned_user_id;
                break;
            case '4':
                $parentUser = BeanFactory::getBean('Users', $parentBean->assigned_user_id);
                $this->assigned_user_id = $parentUser->reports_to_id;
                break;
            case '5':

                if (!empty($this->workflowdefinition->assignclass)) {

                    $classArray = explode('->', html_entity_decode($this->workflowdefinition->assignclass));
                    if(count($classArray) == 2){
                        $this->workflowdefinition->assignclass = $classArray[0];
                        $this->workflowdefinition->assignmethod = $classArray[1];
                    }

                    if(!class_exists($this->workflowdefinition->assignclass) && !empty($this->workflowdefinition->assignfile)) {
                        require_once($this->workflowdefinition->assignfile);
                        $assignClass = new $this->workflowdefinition->assignclass();
                    } else if (class_exists($this->workflowdefinition->assignclass)) {
                        $assignClass = new $this->workflowdefinition->assignclass();
                    } else {
                        $this->assigned_user_id = $this->workflowdefinition->assigntouser;

                        if(empty($this->assigned_user_id))
                            $this->assigned_user_id = 1;
                    }
                    $assignMethod = $this->workflowdefinition->assignmethod;

                    $assignmentResponse = $assignClass->$assignMethod($parentBean, $this->workflowdefinition->assignparams, $this->workflow, $this->workflowdefinition);

                    if (empty($this->id)) {
                        $this->id = create_guid();
                        $this->new_with_id = true;
                    }

                    if (is_array($assignmentResponse) && count($assignmentResponse) > 1) {
                        $this->assigned_user_id = 'multiple';
                        if($this->load_relationship('assigned_users_link')){
                            $this->assigned_users_link->add($assignmentResponse);
                        }
                    } elseif (is_array($assignmentResponse) && count($assignmentResponse) == 1) {
                        $thisAssignment = reset($assignmentResponse);
                        $this->assigned_user_id = $thisAssignment;
                    } else {
                        $this->assigned_user_id = $this->workflowdefinition->assigntouser;

                        if(empty($this->assigned_user_id))
                            $this->assigned_user_id = 1;
                    }
                }
                break;
            case '6':
                $this->assigned_user_id = $parentBean->created_by;
                break;
            default:
                $this->assigned_user_id = $this->workflowdefinition->assigntouser;

                if(empty($this->assigned_user_id))
                    $this->assigned_user_id = 1;
                break;
        }
    }

    public function checkAssignedUser() {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        $query = "SELECT id FROM workflowtasksassignedusers WHERE workflowtask_id = '{$this->id}' AND assigned_user_id = '{$current_user->id}' and deleted = 0";

        if ($current_user->is_admin || $this->assigned_user_id == $current_user->id || ($this->assigned_user_id == 'multiple' && $db->getOne($query)))
            return true;

        return false;
    }

    public function getAvailableActions() {
        if (!$this->checkAssignedUser())
            return array();

        switch ($this->workflowdefinition->tasktype) {
            case 'decision':
                $returnArray = array();
                if ($this->workflowtask_status < 30) {
                    $decisionObj = $this->db->query("SELECT * FROM workflowtaskdecisions WHERE workflowtaskdefinition_id='" . $this->workflowdefinition->id . "' and deleted=0");
                    while ($thisDecision = $this->db->fetchByAssoc($decisionObj)) {
                        $returnArray[] = array(
                            'name' => $thisDecision['name'],
                            'value' => $thisDecision['id']
                        );
                    }
                }
                return $returnArray;
                break;
            default:
                switch ($this->workflowtask_status) {
                    case '10':
                        return (array(
                            array(
                                'name' => 'start',
                                'value' => '20'
                            ),
                            array(
                                'name' => 'complete',
                                'value' => '30'
                            )
                        ));
                        break;
                    case '20':
                        return (array(
                            array(
                                'name' => 'complete',
                                'value' => '30'
                            )
                        ));
                        break;
                    default:
                        return array();
                }
                break;
        }
    }

    public function getComments() {
        $commentsArray = array();
        $kWorkflowComment = BeanFactory::getBean('WorkflowTaskComments');
        foreach ($kWorkflowComment->get_full_list("date_entered ASC", "workflowtask_id = '" . $this->id . "'") as $thisComment) {
            $commentsArray[] = array(
                'description' => $thisComment->description,
                'name' => $thisComment->name,
                'created_by_name' => $thisComment->created_by_name,
                'date_entered' => $thisComment->date_entered
            );
        }
        return $commentsArray;
    }

    function get_parent_assigned_user_id() {
        $parent_bean = BeanFactory::getBeanName($this->parent_type, $this->parent_id);

        $this->assigned_user_id = $parent_bean->assigned_user_id;
        if (!empty($this->parent_bean->team_id)) {
            $this->team_id = $parent_bean->team_id;
        }
    }

    function bean_implements($interface) {
        switch ($interface) {
            case 'ACL': return true;
        }
        return false;
    }

    function save($check_notify = FALSE, $fts_index_bean = true) {

        global $timedate;

        // see if we have to handle the task
        if (method_exists($this, 'handle_' . $this->workflowdefinition->tasktype))
            $this->{'handle_' . $this->workflowdefinition->tasktype}();

        foreach ($this->preSaveSteps as $thisStep) {
            $this->$thisStep();
        }

        // set the date completed
        if ($this->workflowtask_status >= 30 && empty($this->date_completed)) {
            $this->date_completed = $timedate->nowDb();
        }

        $retVal = parent::save($check_notify, $fts_index_bean);

        if ($this->parenttask_id == '') {
            // post save processing
            // ?? close Workflow
            if ($this->workflowtask_status >= 30) {

                if($this->workflowdefinition->closetask == '1'){
                    $this->workflow->setWorkflowClosed(true);
                } else {
                    $this->startNextTasks();
                }
            }
        }

        if ($this->assigned_user_id == 'multiple' && $this->workflowtask_status > 10)
            $this->assigned_user_id = AuthenticationController::getInstance()->getCurrentUser()->id;

        return $retVal;
    }

    public function startNextTasks() {

        // get the next Task
        if (!empty($this->task_decision)) {
            $thisDecision = BeanFactory::getBean('WorkflowTaskDecisions', $this->task_decision);
            $this->startNextTask(BeanFactory::getBean('WorkflowTaskDefinitions', $thisDecision->nextworkflowtaskdefinition_id));
        } else {
            if (!empty($this->workflowdefinition->nexttask))
                $this->startNextTask(BeanFactory::getBean('WorkflowTaskDefinitions', $this->workflowdefinition->nexttask));
        }
        $this->branchOut();
    }

    public function startNextTask($workflowtaskDefinition) {

        // check if we have the task already ...
        if ($this->workflow->getTaskByDefinitionId($workflowtaskDefinition->id))
            return false;

        if ($this->preCreationCheck($workflowtaskDefinition) === false)
            return false;

        // create the task
        $nextTask = BeanFactory::getBean('WorkflowTasks');
        $nextTask->createFromWorkflowDefiniton($this->workflow, $workflowtaskDefinition);
        $nextTask->save();
    }

    public function branchOut() {
        //load the workflow definition where task is previous task
        $taskList = $this->workflowdefinition->get_full_list("", "workflowdefinition_id = '" . $this->workflowdefinition->workflowdefinition_id . "' AND previoustask='" . $this->workflowdefinition->id . "'");

        foreach ($taskList as $thisTask) {
            $this->startNextTask();
        }
    }

    public function preCreationCheck($workflowtaskDefinition) {
        if ($workflowtaskDefinition->prevclosedreq) {
            $previousTasks = $workflowtaskDefinition->get_full_list("", "nexttask = '" . $workflowtaskDefinition->id . "'");
            foreach ($previousTasks as $thisTaskDefinition) {
                $previousTask = $this->workflow->getTaskByDefinitionId($thisTaskDefinition->id);
                if (!$previousTask || $previousTask['workflowtask_status'] < 30)
                    return false;
            }

// check if we have decisions where this is the next task
            $workflowTaskDecision = BeanFactory::getBean('WorkflowTaskDecisions');
            $previousDecisions = $workflowTaskDecision->get_full_list("", "nextworkflowtaskdefinition_id = '" . $workflowtaskDefinition->id . "'");
            foreach ($previousDecisions as $thisDecision) {
                $previousTask = $this->workflow->getTaskByDefinitionId($thisDecision->workflowtaskdefinition_id);
                if (!$previousTask || $previousTask['task_decision'] != $thisDecision->id)
                    return false;
            }
        }
        return true;
    }

    public static function checkForExistence($workflowId, $sequence){
        $itemExists = false;

        // checks if Workitem already exsits and returns boolean value
        $queryResult = DBManagerFactory::getInstance()->query("SELECT * FROM kworkflowtasks WHERE kworkflow_id='{$workflowId}' AND sequence='{$sequence}' AND deleted='0'");
        $rowCount = DBManagerFactory::getInstance()->getRowCount($queryResult);

        if($rowCount > 0) $itemExists = true;
        return $itemExists;
    }


    public function setDueDates() {
        if (!empty($this->workflowdefinition->timetocomplete) && !empty($this->workflowdefinition->timeunit)) {
            $dueDate = new DateTime();
            $dueDate->add(new DateInterval('PT' . $this->workflowdefinition->timetocomplete . strtoupper($this->workflowdefinition->timeunit)));
            $this->date_due = $dueDate->format('Y-m-d H:i:s');
        }
    }


    public function setSystemValues() {

        // load a bean
        $seed = BeanFactory::getBean('WorkflowSystemActions');
        $sytemActionsList = $seed->get_full_list("", "workflowtaskdefinition_id = '" . $this->workflowdefinition->id . "'");

        $actionExecuted = false;

        if (count($sytemActionsList) > 0) {
            $thisParentBean = BeanFactory::getBean($this->workflow->parent_type, $this->workflow->parent_id);
            foreach ($sytemActionsList as $thisSystemAction) {
                if (empty($thisSystemAction->workflowtask_status) || $this->workflowtask_status == $thisSystemAction->workflowtask_status){
                    $thisParentBean->{$thisSystemAction->field_name} = $thisSystemAction->field_value;
                    $actionExecuted = true;
                }
            }

            if($actionExecuted && !$thisParentBean->in_save){
                $thisParentBean->save();
            }
        }
    }


    public function setDecision($thisDecisionId) {
        $this->task_decision = $thisDecisionId;
        $this->workflowtask_status = '30';
    }




    function handle_task() {
        if ($this->workflowdefinition->sendemail == 1 /* && (empty($this->id) || $this->new_with_id) */)
            $this->handle_email(false);
    }


    function handle_decision() {
        if ($this->workflowdefinition->sendemail == 1)
            $this->handle_email(false);
    }

    function handle_email($updateStatus = true) {
        global $beanFiles, $beanList, $app_list_strings;

        if (!empty($this->workflowtask_status) && $this->workflowtask_status != '10')
            return false;


        /*
        require_once($beanFiles[$beanList[$this->workflow->parent_type]]);
        $parentBean = new $beanList[$this->workflow->parent_type]();
        $parentBean->retrieve($this->workflow->parent_id);
        */

        $parentBean = BeanFactory::getBean($this->workflow->parent_type, $this->workflow->parent_id);

        $emailAddress = $this->workflowdefinition->emailto;

        $email = BeanFactory::getBean('Emails');
        $email->mailbox_id = $this->workflowdefinition->mailbox;

        switch ($this->workflowdefinition->emailtype) {

            /*
             * $app_list_strings['workflowdefinition_emailtypes'] = array(
              '1' => 'user assigned to Task',
              '2' => 'user assigned to Bean',
              '3' => 'user created Bean',
              '4' => 'manager assigned to Bean',
              '5' => 'manager created Bean',
              '6' => 'email address',
              '7' => 'system routine',
              '8' => 'user creator to bean',
              '9' => 'email1 of parent bean',
              'A' => 'email1 of parent or parent bean',
              );
             */

            case '1':
                $taskUser = BeanFactory::getBean('Users', $this->assigned_user_id);
                $emailAddress = $taskUser->email1;
                $this->assigned_user_name = $taskUser->full_name;
                break;
            case '2':
                $parentUser = BeanFactory::getBean('Users', $parentBean->assigned_user_id);
                $emailAddress = $parentUser->email1;
                $this->assigned_user_name = $parentUser->full_name;
                break;
            case '3':
                $parentUser = BeanFactory::getBean('Users', $parentBean->created_by);
                $emailAddress = $parentUser->email1;
                $this->assigned_user_name = $parentUser->full_name;
                break;
            case '4':
                $parentUser = BeanFactory::getBean('Users', $parentBean->assigned_user_id);
                $parentManager = BeanFactory::getBean('Users', $parentUser->reports_to_id);
                $emailAddress = $parentManager->email1;
                $this->assigned_user_name = $parentManager->full_name;
                break;
            case '5':
                $parentUser = BeanFactory::getBean('Users', $parentBean->created_by);
                $parentManager = BeanFactory::getBean('Users', $parentUser->reports_to_id);
                $emailAddress = $parentManager->email1;
                $this->assigned_user_name = $parentManager->full_name;
                break;
            case '7':
                $emailAddressesClassSource = explode('->', html_entity_decode($this->workflowdefinition->emailclass));
                $emailAddressesClass = $emailAddressesClassSource[0];
                $emailAddressesMethod = $emailAddressesClassSource[1];
                if (!class_exists($emailAddressesClass)) {
                    $this->status = '20';
                    $this->taskcomment = 'Misconfiguration in emaildetermination. Class ' . $emailAddressesClass . ' does not exist';
                    return false;
                }
                $emailProcessor = new $emailAddressesClass();
                if (!method_exists($emailProcessor, $emailAddressesMethod)) {
                    $this->status = '20';
                    $this->taskcomment = 'Misconfiguration in emaildetermination. Method ' . $emailAddressesMethod . ' does not exist';
                    return false;
                }

                $emailAddress = $emailProcessor->$emailAddressesMethod($this->workflowdefinition->emailparams, $this->parent_type, $this->parent_id);
                break;
            case '8':
                $parentUser = BeanFactory::getBean('Users', $parentBean->created_by);
                $emailAddress = $parentUser->email1;
                $this->assigned_user_name = $parentUser->full_name;
                break;
            case '9':
                $emailAddress = $parentBean->email1;
                break;
            case 'A':
                $pparentSeed = BeanFactory::getBean($parentBean->parent_type, $parentBean->parent_id);
                $emailAddress = $pparentSeed->email1;
                break;
            case 'B':
                $contactSeed = BeanFactory::getBean('Contacts', $parentBean->contact_id);
                $emailAddress = $contactSeed->email1;
                break;
            default:
                $emailAddress = explode(',', $this->workflowdefinition->emailto);
                break;
        }

        // set the email address
        if ($emailAddress != '') {
            if (!is_array($emailAddress))
                $email->addEmailAddress('to', $emailAddress);
            else {
                foreach ($emailAddress as $thisAddress){
                    $email->addEmailAddress('to', $thisAddress);
                }
            }
        } else {
            if ($updateStatus) {
                $this->status = 20;
                $this->taskcomment = 'no email address could be determined';
            }
            return;
        }

        // see if the name is equal to a template then pares the template if not use the text
        if (!empty($this->workflowdefinition->emailcontclass)) {
            $emailContentSource = explode('->', $this->workflowdefinition->emailcontclass);
            $emailContentClass = $emailContentSource[0];
            $emailContentMethod = $emailContentSource[1];
            if (!class_exists($emailContentClass)) {
                if ($updateStatus) {
                    $this->status = '20';
                    $this->taskcomment = 'Misconfiguration in emaildetermination. Class ' . $emailContentClass . ' does not exist';
                }
                return false;
            }
            $emailProcessor = new $emailContentClass();
            if (!method_exists($emailProcessor, $emailContentMethod)) {
                if ($updateStatus) {
                    $this->status = '20';
                    $this->taskcomment = 'Misconfiguration in emaildetermination. Method ' . $emailContentMethod . ' does not exist';
                }
                return false;
            }

            $emailContent = $emailProcessor->$emailContentMethod($this->parent_type, $this->parent_id, $this->workflowdefinition->emailparams);
            $email->name = $emailContent['subject'];
            $email->body =  $emailContent['body'];
        } elseif (!empty($this->workflowdefinition->emailtemplate)) {
            $template = BeanFactory::getBean('EmailTemplates');
            $template->retrieve($this->workflowdefinition->emailtemplate);
            $parsedTpl = $template->parse($parentBean);
            $email->name = $parsedTpl['subject'];
            $email->body = $parsedTpl['body_html'];
        } else {
            $email->name = $this->name;
            $email->body = $this->workflowdefinition->description;
        }

        if ($email->sendEmail()) {
            if ($updateStatus) {
                $this->workflowtask_status = '30';
            }
        } else {
            if ($updateStatus) {
                $this->taskcomment = $email->ErrorInfo;
                $this->workflowtask_status = '20';
            }
        }

        // save the bean
        $email->parent_type = $this->workflow->parent_type;
        $email->parent_id = $this->workflow->parent_id;
        $email->save(false, true);
    }


    private function parseText($tag, $text, $bean) {
        $returnValue = preg_match_all('/(?<!\\w)\\' . $tag . '\\w+/', $text, $matches);
        if ($returnValue) {
            foreach ($matches[0] as $thisMatch) {
                $fieldValue = str_replace($tag, '', $thisMatch);
                $text = str_replace($thisMatch, $bean->$fieldValue, $text);
            }
        }
        return $text;
    }

    function handle_system() {

        if ($this->workflowdefinition->sendemail == 1)
            $this->handle_email(false);

        if (!empty($this->workflowdefinition->sysclass)) {
            $namespaceArray = explode('->', $this->workflowdefinition->sysclass);
            $class = $namespaceArray[0];
            $method = $namespaceArray[1];

            if (!class_exists($class)) {
                $this->status = '20';
                $this->taskcomment = 'Misconfiguration in systemtask. Class ' . $class . ' does not exist';
                return false;
            }

            $classInstance = new $class();
            if (!method_exists($classInstance, $method)) {
                $this->status = '20';
                $this->taskcomment = 'Misconfiguration in systemtask. Method ' . $method . ' does not exist';
                return false;
            }

            $processResult = $classInstance->$method($this);
            if ($processResult === true)
                $this->workflowtask_status = '30';
            else {
                $this->taskcomment = $processResult;
                $this->workflowtask_status = '20';
            }
        } else
            $this->workflowtask_status = '30';
    }

}
