<?php

namespace SpiceCRM\modules\WorkflowDefinitions\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\KREST\handlers\ModuleHandler;
use stdClass;

require_once ('KREST/handlers/ModuleHandler.php');

class WorkflowDefinitionsKRESTController{
    public function getDefinition($req, $res, $args){
        $restHandler = new ModuleHandler();
        $module = $args['module'];
        $retArray = [];
        $wfd = BeanFactory::getBean('WorkflowDefinitions');
        $wfds = $wfd->get_full_list('name', "workflowdefinition_module='$module'");
        foreach ($wfds as $wfd) {
            $wdfObj = new stdClass();
            foreach ($wfd->field_name_map as $fieldname => $fielddata)
                $wdfObj->$fieldname = $wfd->$fieldname;

            // get the linked beans
            $wdfObj->tasks = array();
            $tasks = $wfd->db->query("SELECT id FROM workflowtaskdefinitions WHERE workflowdefinition_id='$wfd->id' AND deleted = 0");
            while ($task = $wfd->db->fetchByAssoc($tasks)) {

                $taskBean = BeanFactory::getBean('WorkflowTaskDefinitions', $task['id']);
                $task = $restHandler->mapBeanToArray('WorkflowTaskDefinitions', $taskBean);

                $task['decisions'] = [];
                $decisions = $wfd->db->query("SELECT * FROM workflowtaskdecisions WHERE workflowtaskdefinition_id='{$task['id']}' AND deleted = 0");
                while ($decision = $wfd->db->fetchByAssoc($decisions)) {
                    $seed = BeanFactory::getBean('WorkflowTaskDecisions', $decision['id']);
                    $task['decisions'][] = $restHandler->mapBeanToArray('WorkflowTaskDecisions', $seed);
                }

                $task['systemactions'] = [];
                $systemactions = $wfd->db->query("SELECT * FROM workflowsystemactions WHERE workflowtaskdefinition_id='{$task['id']}' AND deleted = 0");
                while ($systemaction = $wfd->db->fetchByAssoc($systemactions)) {
                    $task['systemactions'][] = $systemaction;
                }

                $wdfObj->tasks[] = $task;
            }

            $retArray[] = $wdfObj;
        }

        return $res->withJson($retArray);

    }

    public function setDefinition($req, $res, $args){
        $data = $req->getParsedBody();
        $module = $args['module'];
        $id = $args['id'];

        $wdf = BeanFactory::getBean('WorkflowDefinitions');
        if (!$wdf->retrieve($id)) {
            $wdf->id = $id;
            $wdf->new_with_id = true;
        }

        foreach ($wdf->field_name_map as $fieldname => $fielddata) {
            if ($fieldname != 'id' && $fielddata['type'] != 'link') {
                $wdf->{$fieldname} = $data[$fieldname];
            }
        }
        // save the definition
        $wdf->save();

        // go through the conditions
        $wdfc = BeanFactory::getBean('WorkflowConditions');
        foreach ($data['conditions'] as $condition) {
            if (!$wdfc->retrieve($condition['id'], true, true)) {
                $wdfc->id = $condition['id'];
                $wdfc->new_with_id = true;
            }

            foreach ($wdfc->field_name_map as $fieldname => $fielddata) {
                if ($fieldname != 'id' && $fielddata['type'] != 'link') {
                    $wdfc->{$fieldname} = $condition[$fieldname];
                }
            }

            $wdfc->save();
        }

        // go through the tasks
        $wdft = BeanFactory::getBean('WorkflowTaskDefinitions');
        $wdftd = BeanFactory::getBean('WorkflowTaskDecisions');
        $wdfts = BeanFactory::getBean('WorkflowSystemActions');
        foreach ($data['tasks'] as $task) {
            if (!$wdft->retrieve($task['id'], true, true)) {
                if ($task['deleted'] == 1)
                    continue;
                $wdft->id = $task['id'];
                $wdft->new_with_id = true;
            }

            if ($task['deleted'] == 1) {
                $wdft->mark_deleted($task['id']);
            } else {

                // process the task
                foreach ($wdft->field_name_map as $fieldname => $fielddata) {
                    if ($fieldname != 'id' && $fielddata['type'] != 'link') {
                        $wdft->{$fieldname} = $task[$fieldname];
                    }
                }
                $wdft->save();

                // handle descisons
                foreach ($task['decisions'] as $decison) {
                    if (!$wdftd->retrieve($decison['id'], true, true)) {
                        if ($decison['deleted'] == 1)
                            continue;

                        $wdftd->id = $decison['id'];
                        $wdftd->new_with_id = true;
                    }

                    if ($decison['deleted'] == 1) {
                        $wdftd->mark_deleted($decison['id']);
                    } else {
                        foreach ($wdftd->field_name_map as $fieldname => $fielddata) {
                            if ($fieldname != 'id' && $fielddata['type'] != 'link') {
                                $wdftd->{$fieldname} = $decison[$fieldname];
                            }
                        }
                        $wdftd->save();
                    }

                }

                // handle systemactions
                foreach ($task['systemactions'] as $sysaction) {
                    if (!$wdfts->retrieve($sysaction['id'], true, true)) {
                        if ($sysaction['deleted'] == 1)
                            continue;

                        $wdfts->id = $sysaction['id'];
                        $wdfts->new_with_id = true;
                    }

                    if ($sysaction['deleted'] == 1) {
                        $wdfts->mark_deleted($sysaction['id']);
                    } else {
                        foreach ($wdfts->field_name_map as $fieldname => $fielddata) {
                            if ($fieldname != 'id' && $fielddata['type'] != 'link') {
                                $wdfts->{$fieldname} = $sysaction[$fieldname];
                            }
                        }
                        $wdfts->save();
                    }
                }
            }
        }


        return $res->withJson(['status' => 'success']);
    }
}
