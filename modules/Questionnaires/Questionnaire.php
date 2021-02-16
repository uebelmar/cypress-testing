<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\Questionnaires;

use SpiceCRM\modules\QuestionnaireParticipations\QuestionnaireParticipation;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\BadRequestException;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\ErrorHandlers\Exception;

class Questionnaire extends SugarBean
{

    public $table_name = "questionnaires";
    public $object_name = "Questionnaire";
    public $module_dir = 'Questionnaires';
    public $unformated_numbers = true;

    public function __construct()
    {
        parent::__construct();
    }


    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }


    public function get_summary_text()
    {
        return $this->name;
    }

    public static function getAllCategories()
    {
        $db = DBManagerFactory::getInstance();
        $allCategories = array();
        $dbResult = $db->query(sprintf('SELECT id, name, abbreviation, sortkey FROM questionoptioncategories WHERE deleted = 0 ORDER BY sortkey, name'));

        while ($dummy = $db->fetchByAssoc($dbResult, false)) {
            $allCategories[$dummy['id']] = $dummy;
        }

        return $allCategories;
    }

    /**
     * getQuestionnaireArray
     *
     * Returns an array with the current Questionnaire and its QuestionSets.
     *
     * @return array
     */
    public function getQuestionnaireArray() {

        $resultArray = [
            "id"           => $this->id,
            "name"         => $this->name,
            "textbefore"   => $this->textbefore,
            "textafter"    => $this->textafter,
        ];

        $resultArray['questionsets'] = $this->getQuestionSets();

        return $resultArray;
    }

    /**
     * getQuestionSets
     *
     * Returns an array with QuestionSets belonging to the current Questionnaire.
     * The QuestionSets contain their Questions.
     *
     * @return array|null
     */
    private function getQuestionSets() {
        $db = DBManagerFactory::getInstance();

        $questionSets = [];
        $dbResult = $db->query(
            'SELECT qs.* FROM questionsets qs ' .
            'WHERE qs.deleted = 0 AND qs.questionnaire_id="' . $this->id . '"'
        );

        if ($dbResult->num_rows === 0) {
            return null;
        }

        while ($qs = $db->fetchByAssoc($dbResult, false)) {
            $questionSet = BeanFactory::getBean('QuestionSets', $qs['id'], ['encode'=>false]);
            $questionSets[$qs['id']] = $questionSet->getQuestionSetArray();
        }
        return $questionSets;
    }

    /**
     * saveAnswers
     *
     * Saves the answers to the questions from the current Questionnaire.
     *
     * @param $questionSets
     * @param $parentId
     * @param $parentModule
     * @return string
     * @throws NotFoundException
     * @throws Exception
     */
    public function saveAnswers( $questionSets, $parentId, $parentModule ) {
        $db = DBManagerFactory::getInstance();
        $currentDatetime = gmdate('Y-m-d H:i:s');

        $db->transactionStart();

        # Get the reference record ...
        # ... to determine the attached questionnaire and the attached contact.
        # ... to make sure the reference record exists.
        $reference = BeanFactory::getBean( $parentModule );
        $reference->retrieve( $parentId );
        if ( !isset( $reference->id ))
            throw ( new NotFoundException('Reference for Questionnaire Participation not found.'))->setLookedFor([ 'id' => $parentId, 'module' => $parentModule ]);

        $questionnaireParticipation = BeanFactory::getBean('QuestionnaireParticipations');
        $questionnaireParticipation->starttime = $currentDatetime;
        $questionnaireParticipation->endtime = $currentDatetime;
        $questionnaireParticipation->parent_type = $parentModule;
        $questionnaireParticipation->parent_id = $parentId;
        $questionnaireParticipation->questionnaire_id = $reference->questionnaire_id;
        $questionnaireParticipation->contact_id = $reference->contact_id;
        $questionnaireParticipation->completed = true;
        $questionnaireParticipation->save();

        foreach ($questionSets as $questionSetId => $questionSet) {

            $questionSetParticipation = BeanFactory::getBean('QuestionSetParticipations');
            $questionSetParticipation->starttime      = $currentDatetime;
            $questionSetParticipation->endtime        = $currentDatetime;
            $questionSetParticipation->questionset_id = $questionSetId;
            $questionSetParticipation->referencetype  = $parentModule; # to remove in future
            $questionSetParticipation->referenceid    = $parentId; # to remove in future
            $questionSetParticipation->questionnaireparticipation_id = $questionnaireParticipation->id;
            $questionSetParticipation->save();

            foreach ( $questionSet['answers'] as $questionId => $answer ) {

                # EITHER a selected option OR a optionlessAnswerValue. Otherwise it´s a Bad Request:
                if ( isset( $answer['questionoption_id'] ) and isset( $answer['optionlessAnswerValue'] )) {
                    $db->transactionRollback();
                    throw ( new BadRequestException('EITHER a selected option OR a answer value.'))->setErrorCode('eitherSelectedOptionOrAnswerValue');
                }

                if ( isset( $answer['questionoption_id'] )) {

                    # There might have been submitted more than one option, for example in case of a multiple choice question.
                    # So $answers is an array. If it is not (only one option selected), it should be a string - and then it has to be transformed:
                    if ( !is_array( $answer['questionoption_id'] ) ) $questionOptions = [ $answer['questionoption_id'] ];
                    else $questionOptions = $answer['questionoption_id'];

                    foreach ( $questionOptions as $optionId ) {
                        $questionAnswer = BeanFactory::getBean( 'QuestionAnswers' );
                        $questionAnswer->question_id = $questionId;
                        $questionAnswer->questionsetparticipation_id = $questionSetParticipation->id;
                        $questionAnswer->questionoption_id = $optionId;
                        $questionAnswer->save();
                    }

                } else if ( isset( $answer['optionlessAnswerValue'] )) {

                    $questionAnswer = BeanFactory::getBean( 'QuestionAnswers' );
                    $questionAnswer->question_id = $questionId;
                    $questionAnswer->questionsetparticipation_id = $questionSetParticipation->id;
                    $questionAnswer->optionlessAnswerValue = $answer['optionlessAnswerValue'];
                    $questionAnswer->save();

                }
            }
        }
        $db->transactionCommit();

        QuestionnaireParticipation::getEvaluation( $questionnaireParticipation->id );

        return $questionnaireParticipation->id;

    }

    /**
     * isDone
     *
     * Checks if the Questionnaire was already filled out and saved.
     *
     * todo Implement it properly.
     */
    public function isDone() {
        return false;
    }
}
