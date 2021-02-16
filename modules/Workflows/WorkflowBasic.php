<?PHP
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
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\SugarObjects\templates\basic\Basic;
use SpiceCRM\includes\authentication\AuthenticationController;


class WorkflowBasic extends Basic {

    var $new_schema = true;
    var $module_dir = 'Workflows';
    var $object_name = 'Workflow';
    var $table_name = 'workflows';
    var $importable = false;
    var $force_create_workflow = array();
    var $displayname;

    # Config Options
    var $config = array(
        'show_tasks' => 'display_total_list', // display_processed_list
        'layout_style' => 'button_on_top', //bottom_bar
        'show_only_my_tasks' => true,
        'support_visible_in_tasklist' => true,
        'workflow_status_to_delete' => 60,
        'show_icon_workflowtask_edit' => false,
        'displayDetails' => null,
    );
    # debugging
    var $debug = array(
        'show_workflowTask_query' => false,
        'show_workflowTaskList_query' => false
    );

    //    var $WorkflowDefinitionID;
    public function __construct() {
        parent::__construct();
        $this->config();
    }

    /**
     *
     * Enter description here ...
     */
    function config() {


        foreach ($this->config as $configparameter => $value) {
            if (isset(SpiceConfig::getInstance()->config[$this->object_name][$configparameter])) {
                $this->config[$configparameter] = SpiceConfig::getInstance()->config[$this->object_name][$configparameter];
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see SugarBean::bean_implements()
     */
    function bean_implements($interface) {
        switch ($interface) {
            case 'ACL': return true;
        }
        return false;
    }

    /**
     *
     * 	1.) Select all scheduled workflowdefinitions
     * 	2.) Select all beans per workflowdefinition and condition exclude custom_conditions definition !! THIS CAN BE A BIG LIST !!
     * 	3.) Process the Beans
     *
     */
    function WorkflowHandlerScheduled() {
        global $beanList, $beanFiles, $app_list_strings;

        $query = "SELECT * FROM workflowdefinitions WHERE workflowdefinitions.status = 'active_scheduled' AND deleted=0";
        $result = $this->db->query($query, "ERROR: SELECT SCHEDULED WorkflowDefinitions ");
        while ($workflowdefinition = $this->db->fetchByAssoc($result)) {
            $table_name = strtolower(array_search($workflowdefinition['linked_module'], $beanList));

            if (empty($table_name)) {
                $table_name = strtolower(array_search($workflowdefinition['linked_module'], $app_list_strings['moduleListSingular']));
            }

            if ($table_name != false) {

                $query = "SELECT * FROM workflowconditiondefinition WHERE workflowdefinition_id = '" . $workflowdefinition['id'] . "' and deleted='0'";
                $workflow_conditions = $this->db->query($query);
                $query_where = "WHERE " . $table_name . ".deleted = 0 ";
                if ($this->db->getRowCount($workflow_conditions) > 0) {
                    while ($workflowConditionsDetail = $this->db->fetchByAssoc($workflow_conditions)) {
                        // read Relation
                        /*
                          if(isset($bean->field_defs[$workflowConditionsDetail['object_field']]) && ($bean->field_defs[$workflowConditionsDetail['object_field']]['type'] === 'relate')) {
                          $bean->fill_in_relationship_fields();
                          } */

                        // if basci condition is not met return false

                        switch ($workflowConditionsDetail['object_operator']) {
                            case "EQ":
                                if (!($bean->{$workflowConditionsDetail['object_field']} == $workflowConditionsDetail['object_value']))
                                    return false;
                                break;
                            case "GT":
                                if (!($bean->{$workflowConditionsDetail['object_field']} > $workflowConditionsDetail['object_value']))
                                    return false;
                                break;
                            case "GE":
                                if (!($bean->{$workflowConditionsDetail['object_field']} >= $workflowConditionsDetail['object_value']))
                                    return false;
                                break;
                            case "LT":
                                if (!($bean->{$workflowConditionsDetail['object_field']} < $workflowConditionsDetail['object_value']))
                                    return false;
                                break;
                            case "LE":
                                if (!($bean->{$workflowConditionsDetail['object_field']} <= $workflowConditionsDetail['object_value']))
                                    return false;
                                break;
                            case "NE":
                                if (!($bean->{$workflowConditionsDetail['object_field']} != $workflowConditionsDetail['object_value']))
                                    return false;
                                break;
                            case "CT";
                                if ($bean->field_defs[$workflowConditionsDetail['object_field']][type] == 'multienum') {
                                    $workflowConditionsDetail['object_value'] = "^" . $workflowConditionsDetail['object_value'] . "^";
                                }
                                if (strpos($bean->{$workflowConditionsDetail['object_field']}, $workflowConditionsDetail['object_value']) === false)
                                    return false;
                                break;
                            case "TE";
                                if (isset($bean->$workflowConditionsDetail['object_field']) && !empty($workflowConditionsDetail['object_value'])) {
                                    // build a time analysis
                                    $subtract = false;
                                    if (substr($workflowConditionsDetail['object_value'], 0, 1) == '-')
                                        $subtract = true;
                                    // handle the operation
                                    if ($subtract)
                                        $workflowConditionsDetail['object_value'] = str_replace('-', '', $workflowConditionsDetail['object_value']);
                                    $timeArray = explode(':', $workflowConditionsDetail['object_value']);
                                    $timeStampDiff = 60 * ($timeArray[2] + 60 * ($timeArray[1] + 24 * $timeArray[0]));
                                    if ($subtract == true)
                                        $targetDate = date("Y-m-d H:i:s", gmmktime() - $timeStampDiff);
                                    else
                                        $targetDate = date("Y-m-d H:i:s", gmmktime() + $timeStampDiff);

                                    // reformat the date from teh bean since this is retrieved in User Format
                                    $objDateArr = $GLOBALS['timedate']->to_db_date_time($bean->{$workflowConditionsDetail['object_field']});
                                    $objDate = $objDateArr[0] . ' ' . $objDateArr[1];
                                    if ($objDate > $targetDate)
                                        return false;
                                } else {
                                    return false;
                                }
                                break;
                        }

                        // conditions are met ... check Change Flag for each Condition
                        // check if onchange is set, if we have a fetched row in the bean and if the values are the same ...
                        // if yes return false since change condition is not met
                        //if($workflowConditionsDetail['on_change'] == '1' && is_array($bean->fetched_row) && $bean->$workflowConditionsDetail['object_field'] == $bean->kfetched_row[$workflowConditionsDetail['object_field']]) return false;
                        // call custom check ... return false or true
                    }
                }


                /* Aachtung beim durchdenken !!! LEFT JOIN workflows ..... AND workflows.id IS NULL !!!
                 *
                 *
                 * no_other_active_workflow = true [ workflows.workflow_status <= 20 ]
                 * no_other_active_workflow = false[ ]
                 *
                 * run_only_once = true [workflows.workflowdefinition_id = '".$workflowdefinition['id']."']
                 * run_only_once = false [ ]
                 */
                if ($workflowdefinition['no_other_active_workflow'] == 1) {
                    // Es dürfen keine anderen workflows aktiv sein .. deswegen OR

                    if ($workflowdefinition['run_only_once'] == 1) {
                        $query_join_where = " AND workflows.workflow_status <= 20 OR workflows.workflowdefinition_id = '" . $workflowdefinition['id'] . "' ";
                    } else {
                        $query_join_where = " AND workflows.workflow_status <= 20 ";
                    }
                } else {
                    // Es können andere Workflows aktiv sein deswegen => AND workflows.workflowdefinition_id = '".$workflowdefinition['id']."' "

                    if ($workflowdefinition['run_only_once'] == 1) {
                        $query_join_where = " AND workflows.workflowdefinition_id = '" . $workflowdefinition['id'] . "' ";
                    } else {
                        $query_join_where = " AND workflows.workflow_status <= 20 AND workflows.workflowdefinition_id = '" . $workflowdefinition['id'] . "' ";
                    }
                }

                $query = "SELECT " . $table_name . ".id as id, workflows.id as workflow_id FROM " . $table_name .
                        " LEFT JOIN workflows ON workflows.parent_id=" . $table_name . ".id " . $query_join_where . " " .
                        $query_where . " AND workflows.id IS NULL";


                $result_beans = $this->db->query($query);
                if ($this->db->getRowCount($result_beans) > 0) {

                    $bean_name = $workflowdefinition['linked_module'];

// CR1000426 cleanup backend, module Cases removed
//                    if ($bean_name == "Case") {
//                        $bean_name = "aCase";
//                    }

                    if (file_exists($beanFiles[$bean_name])) {
                        require_once($beanFiles[$bean_name]);
                        $bean = new $bean_name();
                        while ($bean_id = $this->db->fetchByAssoc($result_beans)) {
                            $bean->$bean_name();
                            $bean->retrieve($bean_id['id']);
                            self::WorkflowHandler($bean);
                            $this->cleanBean();
                            //unset($bean);
                        }
                    }
                }
            }
        }
    }

    /**
     * the workflow handler called from the hook in before save
     *
     * @param $bean
     * @return bool
     */
    public function WorkflowHandler(&$bean/* , $event, $arguments */) {

        $db = DBManagerFactory::getInstance();

        if (get_class($bean) == 'Workflow' || get_class($bean) == 'WorkflowTask')
            return false;

        // get all active workflows and see if one should be finished
        $activeWorkflowsWithEndConditions = $db->query("SELECT wf.id workflowid, wd.id workflowdefinitonid FROM workflows wf, workflowdefinitions wd WHERE wd.id = wf.workflowdefinition_id AND wf.workflow_status < 30 AND wf.parent_id = '$bean->id' AND (wd.conditions_end IS NOT NULL OR wd.condclass_end IS NOT NULL)");
        while($activeWorkflowsWithEndCondition = $db->fetchByAssoc($activeWorkflowsWithEndConditions)){
            $workflowDefinition = BeanFactory::getBean('WorkflowDefinitions', $activeWorkflowsWithEndCondition['workflowdefinitonid']);
            if ($workflowDefinition->checkConditions($bean, 'end')) {
                $workflow = BeanFactory::getBean('Workflows', $activeWorkflowsWithEndCondition['workflowid']);
                $workflow->setWorkflowClosed(true);
            }
        }
        // select WorkflowDefinition ID
        $workflowCandidates = $this->getWorkflowDefinitionsCandidates($bean);

        // loop over the results
        if ($workflowCandidates !== false) {
            foreach ($workflowCandidates as $thisWorkflowCandidateId => $thisWorkflowCandidateData) {
                $newWorkflow = $this->initializeWorkflow($thisWorkflowCandidateData, $bean);
                // save Workflow
                $newWorkflow->save();
                // create primary task
                $newWorkflow->createPrimaryTasks();

                // create primary Tasks
                // $this->createPrimaryTasks($thisWorkflowCandidateId, $newWorkflow->id, $newWorkflow->parent_type, $newWorkflow->parent_id);
            }
        }
    }

    /**
     *
     * selects potential workflow candidates for a given bean. These are all teh workflows that match the given criteria
     *
     * @param $bean the bean to check for
     * @return array|bool returns either an aray of workflowdefinitions or false if none is found
     */
    function getWorkflowDefinitionsCandidates($bean) {

        global $beanList;

        if ( !empty( $GLOBALS['installing'] )) return false;

        $candidatesArray = array();

        // assess if we have anew bean or if we are in changemode
        if ($bean->fetched_row)
            $wfPreCondition = "('u', 'a')";
        else
            $wfPreCondition = "('n', 'a')";

        $query = "SELECT * FROM workflowdefinitions WHERE workflowdefinition_module = '{$bean->_module}' AND ((workflowdefinition_status  = 'active' AND NOT EXISTS (SELECT id FROM workflows WHERE workflowdefinition_id = workflowdefinitions.id AND workflow_status < 30 AND parent_id = '" . $bean->id . "')) OR (workflowdefinition_status  = 'active_once' AND NOT EXISTS (SELECT id FROM workflows WHERE workflowdefinition_id = workflowdefinitions.id AND parent_id = '" . $bean->id . "'))) AND deleted='0' AND workflowdefinition_precond in " . $wfPreCondition . " ORDER BY workflowdefinition_priority";

        $workflowCandidates = $this->db->query($query);

        if ($workflowCandidates) {
            while ($workflowCandidateDetail = $this->db->fetchByAssoc($workflowCandidates)) {
                $workflowCandidate = BeanFactory::getBean('WorkflowDefinitions', $workflowCandidateDetail['id']);
                if ($workflowCandidate->checkConditions($bean)) {
                    $candidatesArray[$workflowCandidateDetail['id']] = $workflowCandidate;
                }
            }
        }
        if (count($candidatesArray) > 0)
            return $candidatesArray;
        else
            return false;
    }

    public function initializeWorkflow($workflowDefinition, $bean) {
        $thisWorkflow = new Workflow();
        $thisWorkflow->workflow_status = '10';
        $thisWorkflow->workflowdefinition_id = $workflowDefinition->id;
        $thisWorkflow->parent_type = $bean->module_dir;
        $thisWorkflow->parent_id = $bean->id;
        $thisWorkflow->name = $this->parseText((!empty($workflowDefinition->display_name) ? $workflowDefinition->display_name : $workflowDefinition->name), $bean);

        return $thisWorkflow;
    }

    private function parseText($text, $bean) {
        $returnValue = preg_match_all('/(?<!\\w)\\$bean_\\w+/', $text, $matches);
        if ($returnValue) {
            foreach ($matches[0] as $thisMatch) {
                $fieldValue = str_replace('$bean_', '', $thisMatch);
                $text = str_replace($thisMatch, $bean->$fieldValue, $text);
            }
        }
        return $text;
    }

    /**
     * set the complete wf to closed once we reach a certain point
     */
    function setWorkflowClosed($save = false, $closeStatus = '40') {
        // function to set the complete workflow to closed one
        // currently set hardcoded to 40 ... should be made configurable later on
        $this->db->query("UPDATE workflowtasks SET workflowtask_status=$closeStatus WHERE workflowtask_status < 30 AND workflow_id='" . $this->id . "' AND deleted ='0'");

        // set the status of the workflow
        $this->workflow_status = $closeStatus;

        // save the Workflow is the save flag has been passed
        if ($save)
            $this->save();
    }

    
    public function getTaskByDefinitionId($thisDefinitonId){
       return $this->db->fetchByAssoc($this->db->query("SELECT * FROM workflowtasks WHERE workflow_id='".$this->id."' AND workflowtaskdefinition_id='".$thisDefinitonId."' AND deleted=0"));
    }

    /**
     * creates the primary task(s) for a workflow
     *
     * @return bool
     */
    function createPrimaryTasks() {
        $primaryTaskCreated = false;
        $thisWorkflowTaskDefinition = BeanFactory::getBean('WorkflowTaskDefinitions');
        foreach ($thisWorkflowTaskDefinition->get_full_list("", "workflowdefinition_id='" . $this->workflowdefinition_id . "' AND primarytask = 1") as $taskId => $taskObj) {
            $newWorkflowTask = BeanFactory::getBean('WorkflowTasks');
            $newWorkflowTask->createFromWorkflowDefiniton($this, $taskObj);
            $newWorkflowTask->save();

            $primaryTaskCreated = true;
        }

        if (!$primaryTaskCreated) {
            $definitionList = $thisWorkflowTaskDefinition->get_full_list("sequence", "workflowdefinition_id='" . $this->workflowdefinition_id . "'");
            if (is_array($definitionList) && count($definitionList) > 0) {
                $firstDefinition = reset($definitionList);
                $newWorkflowTask = BeanFactory::getBean('WorkflowTasks');
                $newWorkflowTask->createFromWorkflowDefiniton($this, $firstDefinition);
                $newWorkflowTask->save();

                $primaryTaskCreated = true;
            }
        }

        return $primaryTaskCreated;
    }

    /*
    function createPrimaryTasksOld($DefintionID, $workflowID, $parentType, $parentID) {


        // $query = "SELECT * FROM workflowtaskdefinitions WHERE workflowdefinition_id ='{$DefintionID}' AND primary_task='1' and deleted='0'";
        $taskRes = $this->db->query("SELECT * FROM workflowtaskdefinitions WHERE workflowdefinition_id ='{$DefintionID}' AND primary_task='1' and deleted='0'");

        $taskResDetails = array();
        while ($taskResDetail = $this->db->fetchByAssoc($taskRes)) {
            $taskResDetails[] = $taskResDetail;
        }

        // if we have no primary task we take the lowest sequence and assume this is the one we start with
        if (count($taskResDetails) == 0) {
            if (\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['dbconfig']['db_type'] == "mysql") {
                $taskRes = $this->db->query("SELECT *, MIN(sequence) as lowestsequence FROM workflowtaskdefinitions WHERE workflowdefinition_id ='{$DefintionID}' AND deleted='0'");
            }

            if (\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['dbconfig']['db_type'] == "mssql") {
                $taskRes = $this->db->query("SELECT TOP 1 * FROM workflowtaskdefinitions WHERE workflowdefinition_id ='{$DefintionID}' AND deleted='0' ORDER BY sequence ASC");
            }

            while ($taskResDetail = $this->db->fetchByAssoc($taskRes)) {
                $taskResDetails[] = $taskResDetail;
            }
        }

        // loop through the initial tasks and create them
        foreach ($taskResDetails as $index => $taskResDetail) {
            if (!WorkflowTask::checkForExistence($workflowID, $taskResDetail['sequence'])) {

                // MODIFICATION BIACSICS Override PrimaryTasksinformations 20120523
                if (isset($this->primaryTasksInformations[$DefintionID])) {
                    $options['primaryTasksInformations'] = $this->primaryTasksInformations[$DefintionID];
                } else {
                    $options = array();
                }
                $newWorkflowTask = new WorkflowTask($workflowID, $taskResDetail['id'], $parentType, $parentID, $options);

                // save task
                $newWorkflowTask->save();
            }
        }
    }
    */

    function getWorkflowsForBean($beanId, $getList = true) {
        $retArray = array();
        $wfObj = $this->db->query("SELECT id,workflowdefinition_id FROM workflows WHERE parent_id='{$beanId}' AND deleted= 0 AND workflow_status < 30");
        while ($wfDet = $this->db->fetchByAssoc($wfObj)) {
            if ($getList === false)
                return true;
            $retArray[$wfDet['id']] = $wfDet['workflowdefinition_id'];
        }

        if (count($retArray) > 0)
            return $retArray;
        else
            return false;
    }

    /**
     * Query the workflow_id & workflowdefinition_id from parent id
     * @param unknown_type $parentID    "GUID"
     * @param unknown_type $parentType    "MODULENAME"
     */
    function getWorkflowsForParentId($parentID, $parentType) {
        $retArray = array();
        $wfObj = $this->db->query("SELECT id, workflowdefinition_id FROM workflows WHERE parent_id='{$parentID}' AND parent_type='{$parentType}' AND deleted= 0");
        while ($wfDet = $this->db->fetchByAssoc($wfObj)) {
            $retArray[$wfDet['id']] = $wfDet['workflowdefinition_id'];
        }

        return $retArray;
    }

    function getWorkflowTaskList($WorkflowID = null, $WorkflowDefinitionID = null) {
        if ($WorkflowID == null) {
            $WorkflowID = $this->id;
        }

        if ($WorkflowDefinitionID == null) {
            $WorkflowDefinitionID = $this->workflowdefinition_id;
        }


        if (isset($this->config['support_visible_in_tasklist']) && $this->config['support_visible_in_tasklist'] == true) {
            $visible_in_tasklist = "AND kd.visible_in_tasklist = 'task'";
        } else {
            $visible_in_tasklist = "";
        }

        //SELECT for the tasks
        $query = "(
                           SELECT kd.sequence, kd.name, kd.enum, kt.status, kt.action_response, kt.comment, kt.date_entered, kt.assigned_user_id, '' as team_id, kt.id, kt.date_due
                           FROM workflowtaskdefinitions AS kd
                           INNER JOIN workflowtasks AS kt ON kd.id = kt.workflowtaskdefinition_id
                           AND kt.workflow_id='{$WorkflowID}'
                           AND kd.deleted='0'
                           AND kt.deleted='0'
                           AND kt.assigned_user_id <> 'multiple'
                           WHERE
                           kd.workflowdefinition_id='{$WorkflowDefinitionID}'
                           AND kd.deleted='0'
                           " . $visible_in_tasklist . ")";

        //ORDER BY kd.sequence)";
        //SELECT for the definitions
        $query.=" UNION ";
        $query.="(
                           SELECT kd.sequence, kd.name, kd.enum, kt.status, kt.action_response, kt.comment, kt.date_entered, kt.assigned_user_id, '' as team_id, kt.id, kt.date_due
                           FROM workflowtaskdefinitions AS kd
                           LEFT JOIN workflowtasks AS kt ON kd.id = kt.workflowtaskdefinition_id
                           AND kt.workflow_id='{$WorkflowID}'
                           AND kd.deleted='0'
                           AND kt.deleted='0'
                           AND kt.assigned_user_id <> 'multiple'
                           WHERE
                           kd.workflowdefinition_id='{$WorkflowDefinitionID}'
                           AND kd.deleted='0'
                           AND kd.visible_in_tasklist = 'definition')";
        //ORDER BY kd.sequence)";
        //$query.=" UNION ";
        //$query.="(SELECT sequence, name, '' as enum, status, '' as action_response,comment, date_entered, assigned_user_id, '' as team_id, id ,'' FROM workflowtasks k WHERE workflow_id='{$WorkflowID}' AND workflowtaskdefinition_id IS NULL  AND deleted='0')";

        $query.=" ORDER BY sequence";


        if ($this->debug['show_workflowTaskList_query']) {
            echo $query;
        }
        return $WorkflowTaskRes = $this->db->query($query);
    }

    function getWorkflowDetails(){

        $thisWorkFlowArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'workflow_status' => $this->workflow_status,
            'date_entered' => $this->date_entered,
            'worflowtasks' => array()
        );
        $thisWorkflowTasks = $this->get_linked_beans('workflowtasks_link', 'WorkflowTask');
        foreach ($thisWorkflowTasks as $thisWorkflowTask) {
            if ($thisWorkflowTask->workflowdefinition->ishidden == 0) {
                $thisWorkFlowArray['worflowtasks'][] = array(
                    'id' => $thisWorkflowTask->id,
                    'sequence' => $thisWorkflowTask->sequence,
                    'status' => $thisWorkflowTask->workflowtask_status,
                    'name' => $thisWorkflowTask->name,
                    'assigned_user_name' => $thisWorkflowTask->assigned_user_name,
                    'assigned_user_id' => $thisWorkflowTask->assigned_user_id,
                    'enablecomments' => $thisWorkflowTask->workflowdefinition->allowcomment,
                    'description' => $thisWorkflowTask->workflowdefinition->description,
                    'comments' => $thisWorkflowTask->getComments(),
                    'newcomment' => '',
                    'actions' => $thisWorkflowTask->getAvailableActions(),
                    'date_start' => $thisWorkflowTask->date_start,
                    'date_completed' => $thisWorkflowTask->date_completed,
                    'decision' => $this->getTaskDecision($thisWorkflowTask->task_decision)
                );
            }
        }

        usort($thisWorkFlowArray['worflowtasks'], function ($a, $b) {
            if ($a['sequence'] == $b['sequence'])
                return 0;
            elseif ($a['sequence'] > $b['sequence'])
                return 1;
            else
                return -1;
        });

        return $thisWorkFlowArray;
    }

    /**
     * returns the name for a given decision
     *
     * @param null $decisionid
     * @return |null
     */
    private function getTaskDecision($decisionid = null){
        if($decisionid){
            $decision = BeanFactory::getBean('WorkflowTaskDecisions', $decisionid);
            return $decision->name;
        } else {
            return null;
        }
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public function WorkflowMarkDelted($id) {
        $query = "SELECT id FROM workflows WHERE parent_id='{$id}' AND deleted = 0 AND workflow_status < {$this->config['workflow_status_to_delete']}";

        $result = $this->db->query($query);
        while ($markDeleteIds = $this->db->fetchByAssoc($result)) {
            $this->db->query("UPDATE workflows SET deleted = 1 WHERE id = '{$markDeleteIds['id']}'");
            $this->db->query("UPDATE workflowtasks SET deleted = 1 WHERE workflow_id  = '{$markDeleteIds['id']}'");
        }
    }

    public function mark_deleted($id) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $date_modified = gmdate($GLOBALS['timedate']->get_db_date_time_format());
        if (isset($_SESSION['show_deleted'])) {
            $this->mark_undeleted($id);
        } else {
            // call the custom business logic
            $custom_logic_arguments['id'] = $id;
            $this->call_custom_logic("before_delete", $custom_logic_arguments);

            if (isset($this->field_defs['modified_user_id'])) {
                if (!empty($current_user)) {
                    $this->modified_user_id = $current_user->id;
                } else {
                    $this->modified_user_id = 1;
                }
                $query = "UPDATE $this->table_name set deleted=1 , date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' where id='$id'";
            } else
                $query = "UPDATE $this->table_name set deleted=1 , date_modified = '$date_modified' where id='$id'";
            $this->db->query($query, true, "Error marking record deleted: ");
            //$this->mark_relationships_deleted($id);
            // Take the item off the recently viewed lists
            $tracker = BeanFactory::getBean('Trackers');
            $tracker->makeInvisibleForAll($id);

            // call the custom business logic
            $this->call_custom_logic("after_delete", $custom_logic_arguments);
        }
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $bean
     */
    public function hook_before_save(&$bean) {
        $bean->kfetched_row = $bean->fetched_row;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $bean
     */
    public function hook_after_save(&$bean) {
        $this->WorkflowHandler($bean);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $bean
     */
    public function hook_after_delete(&$bean) {
        $this->WorkflowMarkDelted($bean->id);
    }

}
