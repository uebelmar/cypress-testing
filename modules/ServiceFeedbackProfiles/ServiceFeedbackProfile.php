<?php
namespace SpiceCRM\modules\ServiceFeedbackProfiles;

use SpiceCRM\data\SugarBean;

class ServiceFeedbackProfile extends SugarBean {
    public $module_dir  = 'ServiceFeedbackProfiles';
    public $object_name = 'ServiceFeedbackProfile';
    public $table_name  = 'servicefeedbackprofiles';
    public $new_schema  = true;

    public $additional_column_fields = [];

    public $relationship_fields = [];

    public function get_summary_text() {
        return $this->name;
    }

    public function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }
        return false;
    }
}
