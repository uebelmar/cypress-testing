<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\ProjectPlannedActivities;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
        

class ProjectPlannedActivity extends SugarBean {
    public $module_dir = 'ProjectPlannedActivities';
    public $object_name = 'ProjectPlannedActivity';
    public $table_name = 'projectplannedactivities';


    public function __construct(){
        parent::__construct();
    }

    public function get_summary_text(){
        if($this->projectactivitytype_id){
            return $this->project_name . '/'. $this->projectwbs_name . '/' . $this->assigned_user_name . '/' . $this->name;
        } else {
            return $this->project_name . '/'. $this->projectwbs_name . '/' . $this->assigned_user_name . '/' . $this->activity_type . '/' . $this->activity_level;
        }
    }

    public function retrieve($id = -1, $encode = false, $deleted = true, $relationships = true)
    {
        $bean =  parent::retrieve($id, $encode, $deleted, $relationships);

        if($bean){
            $this->consumed = 0;
            $activitySeed = BeanFactory::getBean('ProjectActivities');
            $activities = $activitySeed->get_full_list('', "projectplannedactivity_id='{$bean->id}'");

            $activities = $this->db->query("SELECT activity_start, activity_end FROM projectactivities WHERE projectplannedactivity_id='{$bean->id}' AND deleted = 0");
            while($activity = $this->db->fetchByAssoc($activities)){
                $duration = (strtotime($activity['activity_end']) - strtotime($activity['activity_start'])) / 3600;
                $this->consumed += $duration;
            }
            $this->ratio = $this->consumed / $this->effort;

            // set project_name
            $wbs = BeanFactory::getBean('ProjectWBSs', $this->projectwbs_id);
            if($wbs){
                // todo: workaround needs to be fixed
                $project = BeanFactory::getBean('Projects', $wbs->project_id);
                $this->project_name = $project->name;
                unset($wbs);
            }
        }

        return $this;
    }

}
