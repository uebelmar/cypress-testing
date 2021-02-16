<?php

/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\ProjectWBSs\KREST\controllers;


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

class ProjectWBSsKRESTController
{
    /**
     * delivers an array of ProjectActivityTypes related to the current project
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getActivityTypes($req, $res, $args)
    {
        $projectWBS = BeanFactory::getBean("ProjectWBSs", $args['id']);
        $project = BeanFactory::getBean("Projects", $projectWBS->project_id);
        $projectActivityTypes = $project->get_linked_beans("projectactivitytypes", "ProjectActivityTypes");
        $activityTypes = [];
        foreach ($projectActivityTypes as $activityType) {
            $activityTypes[] = ["id" => $activityType->id, "name" => $activityType->name];
        }
        return $res->withJson(["activitytypes" => $activityTypes]);


    }
}
