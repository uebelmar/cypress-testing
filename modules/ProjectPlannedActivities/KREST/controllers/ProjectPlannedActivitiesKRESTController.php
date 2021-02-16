<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\ProjectPlannedActivities\KREST\controllers;


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class ProjectPlannedActivitiesKRESTController
{
    public function getMyOpenActivities($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();

        $myplannedActivities = array();

        $sql = "SELECT pw.id pwid, pa.id paid 
                FROM projects pr, projectwbss pw, projectplannedactivities pa 
                WHERE pr.id = pw.project_id AND pw.id = pa.projectwbs_id AND pa.assigned_user_id = '$current_user->id' AND pw.deleted = 0 AND pa.deleted = 0 AND pw.wbs_status < 2 AND pr.status in ('active', 'Published') AND pa.is_active=1
                ORDER BY pw.project_id ASC, pw.name";
        $plannedActivities = $db->query($sql);
        while($plannedActivity = $db->fetchByAssoc($plannedActivities)){
            // why not join this tables inside one query together??? why using beans?
            $pw = BeanFactory::getBean('ProjectWBSs', $plannedActivity['pwid']);
            $pa = BeanFactory::getBean('ProjectPlannedActivities', $plannedActivity['paid']);

            if($pw && $pa) {
                $myplannedActivities[] = array(
                    'id' => $pa->id,
                    'projectwbs_id' => $pw->id,
                    'projectwbs_name' => $pw->name,
                    'summary_text' => $pa->get_summary_text(),
                    'project_name' => $pw->project_name,
                    'type' => $pa->activity_type,
                    'level' => $pa->activity_level,
                    'projectactivitytype_name' => $pa->projectactivitytype_name,
                    'projectactivitytype_id' => $pa->projectactivitytype_id,
                );
            }
        }

        return $res->withJson($myplannedActivities);
    }

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
