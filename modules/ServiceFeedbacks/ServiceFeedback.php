<?php
namespace SpiceCRM\modules\ServiceFeedbacks;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\ErrorHandlers\BadRequestException;

class ServiceFeedback extends SugarBean {
    public $module_dir  = 'ServiceFeedbacks';
    public $object_name = 'ServiceFeedback';
    public $table_name  = 'servicefeedbacks';
    public $new_schema  = true;
    
    public $additional_column_fields = [];

    public $relationship_fields = [];

    public function get_summary_text() {
        return $this->contact_name . '/' . $this->date_entered;
    }

    public function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    /**
     * getQuestionnaire
     *
     * Returns the Questionnaire linked to this ServiceFeedback or throws Exception if there is none or if has
     * already been filled out.
     *
     * @param bool $includeFilled
     * @return SugarBean
     * @throws BadRequestException
     */
    public function getQuestionnaire($includeFilled = false) {
        $questionnaire = BeanFactory::getBean('Questionnaires', $this->questionnaire_id);

        if (!$questionnaire) {
            throw new BadRequestException('This Service Feedback has no Questionnaire attached.');
        }

        if (!$includeFilled) {
            if ($questionnaire->isDone()) {
                throw new BadRequestException('This Questionnaire has already been filled out.');
            }
        }

        return $questionnaire;
    }
}
