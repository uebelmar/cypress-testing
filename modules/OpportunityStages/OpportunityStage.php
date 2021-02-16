<?php
namespace SpiceCRM\modules\OpportunityStages;

use SpiceCRM\data\SugarBean;
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

class OpportunityStage extends SugarBean {

	var $table_name = "opportunitystages";
	var $module_dir = "OpportunityStages";
	var $object_name = "OpportunityStage";

    function ACLAccess($view, $is_owner = 'not_set'){

        switch($view){
            case 'edit':
            case 'delete':
                return false;
                break;
        }

        return parent::ACLAccess($view, $is_owner);
    }
}
