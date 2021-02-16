<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\Questions;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class Question extends SugarBean {

    public $table_name = "questions";
    public $object_name = "Question";
    public $module_dir = 'Questions';
    public $unformated_numbers = true;


    public function __construct() {
        parent::__construct();
    }


    public function bean_implements($interface) {
        switch($interface) {
            case 'ACL': return true;
        }
        return false;
    }


    public function get_summary_text() {
        return $this->name;
    }


    public function mark_deleted( $id ) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $rel_beans = $this->get_linked_beans( 'questionoptions' ,'QuestionOption' );
        foreach ( $rel_beans as $rel_bean ) $rel_bean->mark_deleted( $rel_bean->id );
        parent::mark_deleted( $id );
    }


    public function save( $check_notify = FALSE, $fts_index_bean = TRUE ) {
        $db = DBManagerFactory::getInstance();
        // A new question must be ordered at the end of all questions (of the same question set). So it has to get the highest position value.
        // But only, if the position value isn´t set already (by the ui).
        if (( !isset( $this->id ) or $this->new_with_id ) and !isset( $this->position ) and isset( $this->questionset_id[0])) {
            $highestPosition = $db->getOne( sprintf( 'SELECT max(position) FROM questions WHERE questionset_id = "%s" AND deleted = 0', $db->quote( $this->questionset_id )));
            $this->position = $highestPosition+1;
        }
        return parent::save( $check_notify, $fts_index_bean );
    }

    public function getQuestionArray( $questiontype ) {
        $resultArray = [
            "id"                => $this->id,
            "name"              => $this->name,
            "questionparameter" => json_decode( $this->questionparameter, true) ?: (object)null,
            "position"          => $this->position,
            "description"       => $this->description
        ];

        $resultArray['questionoptions'] = $this->getQuestionOptions( $questiontype );

        return $resultArray;
    }

    private function getQuestionOptions( $questiontype ) {
        $db = DBManagerFactory::getInstance();

        $questionoptions = [];
        $dbResult = $db->query(
            'SELECT qo.* FROM questionoptions qo ' .
            'WHERE qo.deleted = 0 AND qo.question_id="' . $this->id . '"'
        );

        if ($dbResult->num_rows === 0) {
            return null;
        }

        while ($qo = $db->fetchByAssoc($dbResult, false)) {
            $questionoption = BeanFactory::getBean('QuestionOptions', $qo['id']);
            $questionoptions[$qo['id']] = $questionoption->getQuestionOptionArray( $questiontype );
        }
        return $questionoptions;
    }
}
