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
namespace SpiceCRM\modules\WorkflowDefinitions;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SysModuleFilters\SysModuleFilters;
use SpiceCRM\modules\WorkflowConditions\WorkflowCondition;
use SpiceCRM\modules\WorkflowSystemActions\WorkflowSystemAction;
use SpiceCRM\modules\WorkflowTaskDecisions\WorkflowTaskDecision;
use SpiceCRM\modules\WorkflowTaskDefinitions\WorkflowTaskDefinition;

class WorkflowDefinition extends SugarBean
{
    var $new_schema = true;
    var $module_dir = 'WorkflowDefinitions';
    var $object_name = 'WorkflowDefinition';
    var $table_name = 'workflowdefinitions';
    var $importable = false;
    var $id;
    var $name;
    var $date_entered;
    var $date_modified;
    var $modified_user_id;
    var $modified_by_name;
    var $created_by;
    var $created_by_name;
    var $description;
    var $deleted;
    var $created_by_link;
    var $modified_user_link;
    var $assigned_user_id;
    var $assigned_user_name;
    var $assigned_user_link;

    function __construct()
    {
        parent::__construct();
    }

    function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /*
    function initialize()
    {
        $return['labels'] = return_module_language($GLOBALS['current_language'], "WorkflowDefinitions", true);
        $return['moduleList'] = $GLOBALS['moduleList'];

        $usersObj = \SpiceCRM\data\BeanFactory::getBean("Users");

        
        $return['users'] = $usersObj->get_full_list("user_name");

        //transform user to array
        foreach ($return['users'] as $key => $userObj) {
            $return['users'][$key] = array(
                'id' => $userObj->id, 
                'user_name' => $userObj->user_name,
                'full_name' => $userObj->full_name . ' (' . $userObj->user_name . ')'
                
            );
        }
        
        
        $WorkflowDefinitionsObject = new WorkflowDefinition();
        $return['WorkflowDefinitions'] = $WorkflowDefinitionsObject->get_full_list();
        foreach ($return['WorkflowDefinitions'] as $key => $WorkflowConditionsObj) {
            $return['WorkflowDefinitions'][$key] = $WorkflowConditionsObj->toArray();
        }
        $EmailTemplatesObj = new EmailTemplate();
        $return['emailTemplates'] = $EmailTemplatesObj->get_full_list();
        foreach ($return['emailTemplates'] as $key => $EmailTemplateObj) {
            $return['emailTemplates'][$key] = $EmailTemplateObj->toArray();
        }

        //if (!$GLOBALS['app_list_strings']) $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['app_list_strings'] = return_app_list_strings_language('en_us');

        $doms = array(
            'workflowdefinition_status',
            'workflowdefinition_assgintotypes',
            'workflowtask_status',
            'workflowdefinition_precondition',
            'workflowdefinition_emailtypes'
        );
        foreach ($doms as $domName) {
            $return['doms'][$domName] = $GLOBALS['app_list_strings'][$domName];
        }

        return $return;
    }


    function getUsers()
    {
        $usersObj = \SpiceCRM\data\BeanFactory::getBean("Users");
        $userList = $usersObj->get_list("users.user_name", "users.user_name like '%" . $_REQUEST['query'] . "%'");
        foreach ($userList as $key => $userObj) {
            $userList[$key] = $userObj->toArray();
        }
        return array(
            'status' => 'success',
            'items' => $userList
        );
    }
    */

    function loadCompleteWorkflow()
    {
        $return['workflowDefinition'] = $this->toArray();


        //append moduleFields
        $return['moduleFields'] = array();
        $kworflowModule = BeanFactory::getBean($this->workflowdefinition_module);
        if ($kworflowModule) {


            $return['moduleFields'] = $kworflowModule->field_defs;
            $lang = return_module_language($GLOBALS['current_language'], $this->workflowdefinition_module, true);
            foreach ($return['moduleFields'] as $key => $field) {
                $return['moduleFields'][$key]['label'] = $lang[$field['vname']];
                if (substr($return['moduleFields'][$key]['label'], strlen($return['moduleFields'][$key]['label']) - 1, 1) == ":") {
                    $return['moduleFields'][$key]['label'] = substr($return['moduleFields'][$key]['label'], 0, strlen($return['moduleFields'][$key]['label']) - 1);
                }
                if (!$return['moduleFields'][$key]['label']) $return['moduleFields'][$key]['label'] = $return['moduleFields'][$key]['vname'];
            }
        } else {
            LoggerManager::getLogger()->fatal(__FILE__ . "::" . __FUNCTION__ . "::couldnt instance module " . $this->workflowdefinition_module);
        }

        //append conditions
        $return['workflowConditions'] = array();
        $workflowConditionsObject = new WorkflowCondition();
        $return['workflowConditions'] = $workflowConditionsObject->get_full_list("", "workflowdefinition_id='" . $this->id . "'");
        foreach ($return['workflowConditions'] as $key => $itemObj) {
            $return['workflowConditions'][$key] = $itemObj->toArray();
        }

        //append taskdefinitions
        $return['taskDefinitions'] = array();
        $workflowTaskDefinitionsObject = new WorkflowTaskDefinition();
        $return['taskDefinitions'] = $workflowTaskDefinitionsObject->get_full_list("", "workflowdefinition_id='" . $this->id . "'");
        foreach ($return['taskDefinitions'] as $key => $itemObj) {
            //collect taskdefinition ids
            $taskDefinitionIds[] = $itemObj->id;
            $return['taskDefinitions'][$key] = $itemObj->toArray();

            //html support for task description...
            $return['taskDefinitions'][$key]['description'] = html_entity_decode($return['taskDefinitions'][$key]['description']);
        }
        //append systemActions
        $return['systemActions'] = array();
        if (is_array($taskDefinitionIds) && count($taskDefinitionIds)) {
            $workflowSystemActionsObject = new WorkflowSystemAction();
            $return['systemActions'] = $workflowSystemActionsObject->get_full_list("", "workflowtaskdefinition_id in ('" . implode("','", $taskDefinitionIds) . "')");
            foreach ($return['systemActions'] as $key => $itemObj) {
                $return['systemActions'][$key] = $itemObj->toArray();
            }
        }

        //append decisions
        $return['decisions'] = array();
        if (is_array($taskDefinitionIds) && count($taskDefinitionIds)) {
            $workflowTaskDecisionsObject = new WorkflowTaskDecision();
            $return['decisions'] = $workflowTaskDecisionsObject->get_full_list("", "workflowtaskdefinition_id in ('" . implode("','", $taskDefinitionIds) . "')");
            foreach ($return['decisions'] as $key => $itemObj) {
                $return['decisions'][$key] = $itemObj->toArray();
            }
        }

        return $return;
    }

    function deleteCompleteWorkflow()
    {
        $workflowDetails = html_entity_decode($_REQUEST['workflowDetails']);
        $workflowDetails = json_decode($workflowDetails, true);

        $workflowDefinitionsObject = new WorkflowDefinition();

        $workflowDefinitionsObject->retrieve($workflowDetails['id']);

        //delete conditions
        $workflowConditionsObject = new WorkflowCondition();
        foreach ($workflowConditionsObject->get_full_list("", "workflowdefinition_id='" . $this->id . "'") as $itemObj) {
            $itemObj->deleted = true;
            $itemObj->save();
        }

        //delete taskdefinitions
        $return['taskDefinitions'] = array();
        $workflowTaskDefinitionsObject = new WorkflowTaskDefinition();
        foreach ($workflowTaskDefinitionsObject->get_full_list("", "workflowdefinition_id='" . $this->id . "'") as $key => $itemObj) {
            //collect taskdefinition ids
            $taskDefinitionIds[] = $itemObj->id;
            $itemObj->deleted = true;
            $itemObj->save();
        }

        //delete systemactions
        $workflowSystemActionsObject = new WorkflowSystemAction();
        foreach ($workflowSystemActionsObject->get_full_list("", "workflowtaskdefinition_id in ('" . implode(",", $taskDefinitionIds) . "')") as $itemObj) {
            $itemObj->deleted = true;
            $itemObj->save();
        }

        //delete decisions
        $workflowtaskDecisionObject = new WorkflowTaskDecision();
        foreach ($workflowtaskDecisionObject->get_full_list("", "workflowtaskdefinition_id in ('" . implode(",", $taskDefinitionIds) . "')") as $itemObj) {
            $itemObj->deleted = true;
            $itemObj->save();
        }


        //delete workflowdefintions
        $workflowDefinitionsObject->deleted = true;
        $workflowDefinitionsObject->save();

        return array('status' => 'success');
    }

    function saveCompleteWorkflow()
    {
        $workflowDetails = html_entity_decode($_REQUEST['workflowDetails']);
        $workflowDetails = json_decode($workflowDetails, true);

        $workflowDefinitionsObject = new WorkflowDefinition();


        if ($workflowDetails['workflowDefinition']) {
            //do a retrieve do avoid duplicate entry
            if ($workflowDetails['workflowDefinition']['id']) {
                $workflowDefinitionsObject->retrieve($workflowDetails['workflowDefinition']['id']);
                if (is_array($workflowDefinitionsObject->fetched_row) && count($workflowDefinitionsObject->fetched_row))
                    $workflowDetails['workflowDefinition']['new_with_id'] = false;
            }
            
            foreach ($workflowDetails['workflowDefinition'] as $key => $value) {
                $workflowDefinitionsObject->$key = $value;
            }
            $workflowDefinitionsObject->save();
        }


        if ($workflowDetails['workflowConditions']) {


            foreach ($workflowDetails['workflowConditions'] as $conditionDefinition) {
                $workflowConditionObject = new WorkflowCondition();

                if ($conditionDefinition['id']) {
                    $workflowConditionObject->retrieve($conditionDefinition['id']);
                    if (is_array($workflowConditionObject->fetched_row) && count($workflowConditionObject->fetched_row))
                        $conditionDefinition['new_with_id'] = false;
                }
                foreach ($conditionDefinition as $key => $value) {
                    $workflowConditionObject->$key = $value;
                }
                $workflowConditionObject->workflowdefinition_id = $workflowDefinitionsObject->id;
                $workflowConditionObject->save();
            }

            //associate task to workflow
        }

        if ($workflowDetails['taskDefinitions']) {


            foreach ($workflowDetails['taskDefinitions'] as $taskDefinition) {
                $workflowTaskDefinitionObject = new WorkflowTaskDefinition();

                if ($taskDefinition['id']) {
                    $workflowTaskDefinitionObject->retrieve($taskDefinition['id']);
                    if (is_array($workflowTaskDefinitionObject->fetched_row) && count($workflowTaskDefinitionObject->fetched_row))
                        $taskDefinition['new_with_id'] = false;
                }
                foreach ($taskDefinition as $key => $value) {
                    $workflowTaskDefinitionObject->$key = $value;
                }
                $workflowTaskDefinitionObject->workflowdefinition_id = $workflowDefinitionsObject->id;
                $workflowTaskDefinitionObject->save();
            }

            //associate task to workflow
        }
        if ($workflowDetails['systemActions']) {
            foreach ($workflowDetails['systemActions'] as $systemActionDefinition) {
                $workflowSystemActionsObject = new WorkflowSystemAction();

                if ($systemActionDefinition['id']) {
                    $workflowSystemActionsObject->retrieve($systemActionDefinition['id']);
                    if (is_array($workflowSystemActionsObject->fetched_row) && count($workflowSystemActionsObject->fetched_row))
                        $systemActionDefinition['new_with_id'] = false;
                }
                foreach ($systemActionDefinition as $key => $value) {
                    $workflowSystemActionsObject->$key = $value;
                }
                $workflowSystemActionsObject->save();
            }
        }
        if ($workflowDetails['decisions']) {
            foreach ($workflowDetails['decisions'] as $decisionDefinition) {
                $workflowDecisionObject = new WorkflowTaskDecision();

                if ($decisionDefinition['id']) {
                    $workflowDecisionObject->retrieve($decisionDefinition['id']);
                    if (is_array($workflowDecisionObject->fetched_row) && count($workflowDecisionObject->fetched_row)) $decisionDefinition['new_with_id'] = false;
                }
                foreach ($decisionDefinition as $key => $value) {
                    $workflowDecisionObject->$key = $value;
                }
                $workflowDecisionObject->save();

            }
        }

        //associate task to workflow

        return true;
    }

    /**
     * check if conditions are met to start the workflow
     *
     * @param $bean the bean
     * @return bool return true if all conditions are met and the workflow shoudl be triggered
     */
    public function checkConditions($bean, $conditiontype = 'start')
    {

        $condclass = $conditiontype == 'end' ? $this->condclass : $this->condclass;
        // check with the provided function
        if (!empty($condclass)) {
            $condArray = explode('->', $condclass);
            if(count($condArray) == 2) {
                $condClass = $condArray[0];
                $condMethod = $condArray[1];
                $condObj = new $condClass();
                if (!$condObj->$condMethod($this->function_parameters, $bean, $this)) {
                    return false;
                }
            }
        }

        // check the filter
        $condfilter = $conditiontype == 'end' ? $this->conditions_end : $this->conditions;
        if($condfilter) {
            $filter = new SysModuleFilters();
            return $conditionMet = $filter->checkBeanForFilterMatchGroup(json_decode(html_entity_decode($condfilter)), $bean);
        }

        return true;
    }

    private function getConditions()
    {
        $db = DBManagerFactory::getInstance();
        $conditionsArray = array();
        $conditionsObj = $db->query("SELECT id FROM workflowconditions WHERE workflowdefinition_id = '" . $this->id . "' AND deleted = 0");
        while ($thisConditionRecord = $db->fetchByAssoc($conditionsObj)) {
            $conditionsArray[] = BeanFactory::getBean('WorkflowConditions', $thisConditionRecord['id']);
        }
        return $conditionsArray;
    }

}


