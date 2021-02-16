<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\ProjectWBSStatusReports;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;

class ProjectWBSStatusReport extends SugarBean
{
    public $module_dir = 'ProjectWBSStatusReports';
    public $object_name = 'ProjectWBSStatusReport';
    public $table_name = 'projectwbsstatusreports';

    /**
     * save the new date entered in ProjectWBSStatusReport in the date_end field of the related ProjectWBS
     * @param false $check_notify
     * @param bool $fts_index_bean
     * @return String|void
     */
    public function save($check_notify = false, $fts_index_bean = true)
    {
        if (!empty($this->projectwbs_id)) {
            $projectwbs = BeanFactory::getBean('ProjectWBSs', $this->projectwbs_id);
            if($projectwbs) {
                $this->date_end = $projectwbs->date_end;
                $projectwbs->date_end = $this->new_date_end;
                $projectwbs->level_of_completion = $this->level_of_completion;
                if($projectwbs->level_of_completion == 100){
                    $projectwbs->wbs_status = 2; // completed
                }
                $projectwbs->save();
            }
        }

        return parent::save($check_notify, $fts_index_bean);
    }
}
