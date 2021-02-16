<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\QuestionSets;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class QuestionSet extends SugarBean {

    public $table_name = "questionsets";
    public $object_name = "QuestionSet";
    public $module_dir = 'QuestionSets';
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

    public function getQuestionSetArray() {
        $resultArray = [
            "id"                    => $this->id,
            "name"                  => $this->name,
            "textbefore"            => $this->textbefore,
            "textafter"             => $this->textafter,
            "questiontype"          => $this->questiontype,
            "questiontypeparameter" => json_decode($this->questiontypeparameter, true) ?: (object)null,
            "position"              => $this->position,
            "date_entered"          => $this->date_entered,
            "shuffle"               => $this->shuffle
        ];

        $resultArray['questions'] = $this->getQuestions();

        return $resultArray;
    }

    private function getQuestions() {
        $db = DBManagerFactory::getInstance();

        $questions = [];
        $dbResult = $db->query(
            'SELECT q.* FROM questions q ' .
            'WHERE q.deleted = 0 AND q.questionset_id="' . $this->id . '"'
        );

        if ($dbResult->num_rows === 0) {
            return null;
        }

        while ($qs = $db->fetchByAssoc($dbResult, false)) {
            $question = BeanFactory::getBean('Questions', $qs['id'], ['encode'=>false]);
            $questions[$qs['id']] = $question->getQuestionArray( $this->questiontype );
        }
        return $questions;
    }

    public function onClone()
    {
        if ( !isset( $this->questiontype[0])) return; # Without knowing the question type we do nothing.
        # Add 'mappingQuestionsetTypeParameterId' to custom data (if not already exists):
        if ( !isset( $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'] )) $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'] = [];
        $questiontypeparameter = json_decode( html_entity_decode($this->questiontypeparameter), true );
        if ( isset( $questiontypeparameter[$this->questiontype]['entries'] )) {
            foreach ( $questiontypeparameter[$this->questiontype]['entries'] as $k => $v ) {
                if ( !isset( $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'][$v['id']] ) ) {
                    $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'][$v['id']] = create_guid();
                }
                $questiontypeparameter[$this->questiontype]['entries'][$k]['id'] = $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'][$v['id']];
            }
            $this->questiontypeparameter = json_encode( $questiontypeparameter );
        }
    }

}
