<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\UserAbsences;

use SpiceCRM\data\SugarBean;
use DateTime;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class UserAbsence extends SugarBean {
    public $module_dir = 'UserAbsences';
    public $object_name = 'UserAbsence';
    public $table_name = 'userabsences';
    public $new_schema = true;

    public function get_summary_text(){
        return $this->type;
    }

    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }

    public function getSubstituteIDs(){
        global $timedate;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        $userIDs = [];
        $today = new DateTime();
        $today = $today->format($timedate->get_date_format());
        $substituteids = $db->query("SELECT distinct assigned_user_id FROM userabsences WHERE representative_id='{$current_user->id}' AND date_start <= '$today' AND date_end >= '$today' AND deleted = 0");
        while($substitute = $db->fetchByAssoc($substituteids)){
            $userIDs[] = $substitute['assigned_user_id'];
        }
        return $userIDs;
    }
}
