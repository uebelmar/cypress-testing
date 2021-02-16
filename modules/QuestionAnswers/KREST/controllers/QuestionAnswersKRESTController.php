<?php

namespace SpiceCRM\modules\QuestionAnswers\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\ErrorHandlers\BadRequestException;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\modules\QuestionnaireParticipations\QuestionnaireParticipation;

class QuestionAnswersKRESTController
{

    private $currentDatetime;
    private $req;
    private $args;
    private $questionsetsOfQuestionnaire;
    private $db;
    private $questionSetParticipationsInDB;
    private $answersFromFrontend;
    private $questionnaireParticipation;
    private $questionnaire;
    private $answersInDB;
    private $questionsOfQuestionnaire = [];
    private $participationParent;
    private $affectedQuestionsets;

    private function init( $req, $args ) {
        $this->currentDatetime = gmdate('Y-m-d H:i:s');
        $this->req = $req;
        $this->args = $args;
        $this->db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
    }

    # todo: wasserdicht machen !
    public function getAnswers_byParent( $req, $res, $args ) {

        $this->init( $req, $args );

        $participationId = QuestionnaireParticipation::getParticipationId_byReference( $args['parentType'], $args['parentId'] );

        if ( $participationId === false ) {
            $parent = BeanFactory::getBean( $args['parentType'] );
            $parent->retrieve( $args['parentId'] );
            if ( $parent === false ) {
                throw ( new \SpiceCRM\KREST\NotFoundException( 'Parent of QuestionnaireParticipation not found.' ) )->setLookedFor( [ 'module' => $args['parentType'], 'id' => $args['parentId'] ] );
            }
            $questionnaireId = $parent->questionnaire_id;
        } else {
            $this->questionnaireParticipation = BeanFactory::getBean( 'QuestionnaireParticipations', $participationId );
            if ( $this->questionnaireParticipation === false ) {
                throw ( new \SpiceCRM\KREST\NotFoundException( 'QuestionnaireParticipation not found.' ) )->setLookedFor( [ 'module' => 'QuestionnaireParticipations', 'id' => $args['participationId'] ] );
            }
            $questionnaireId = $this->questionnaireParticipation->questionnaire_id;
        }

        $this->retrieveQuestionnaireByID( $questionnaireId );
        $this->retrieveQuestionsetsOfQuestionnaire();

        return $res->withJson( self::getAnswers( $questionnaireId, $this->questionnaireParticipation ? $this->questionnaireParticipation : null ));

    }

    public function getAnswers_byParticipation( $req, $res, $args ) {

        $this->init();

        $participation = BeanFactory::getBean('QuestionnaireParticipations', $args['participationId'] );
        if ( $participation === false ) {
            throw ( new \SpiceCRM\KREST\NotFoundException('QuestionnaireParticipation not found.'))->setLookedFor(['module' => 'QuestionnaireParticipations', 'id' => $args['participationId'] ]);
        }

        return $res->withJson( $this->getAnswers( $participation->questionnaire_id, $participation ));

    }

    private function getAnswers( $questionnaireId, $participation ) {

        $response = ['answers' => []];

        $response['participationId'] = null;
        $response['questionnaireId'] = $questionnaireId;

        if ( isset( $participation )) {
            $response['participationId'] = $participation->id;
            $response['isCompleted'] = (boolean)$participation->completed;
            $questionsetParticipations = $participation->get_linked_beans('questionsetparticipations');
            foreach ( $questionsetParticipations as $questionsetParticipation ) {

                $answers = $questionsetParticipation->get_linked_beans('questionanswers');
                foreach ( $answers as $answer ) {
                    $question = $answer->get_linked_beans('question');
                    $question = $question[0];
                    if ( $this->getQuestiontype( $question ) === 'text' or $this->getQuestiontype( $question ) === 'nps' ) {
                        $response['answers'][$answer->question_id]['optionlessAnswerValue'] = $answer->optionlessAnswerValue;
                    } else {
                        $response['answers'][$answer->question_id]['options'][$answer->questionoption_id] = isset( $answer->optionlessAnswerValue[0] ) ? $answer->optionlessAnswerValue : true;
                    }
                }
            }
        }
        return $response;

    }

    /**
     * Get the question type of a question.
     */
    private function getQuestiontype( $question ) {
        if ( isset( $question->questiontype[0] )) return $question->questiontype;
        return $this->questionsetsOfQuestionnaire[$question->questionset_id]->questiontype;
    }

    /**
     * Does a question type have options?
     */
    private function questiontypeWithOptions( $type ) {
        return !!preg_match('/^binary|single|multi|ist|rating$/', $type );
    }

    /**
     * Does a question type have a answer value?
     */
    private function questiontypeWithValue( $type ) {
        return preg_match('/^text|ist|nps$/', $type );
    }

    /**
     * Prepare the answers got from the frontend.
     */
    private function prepareAnswersFromFrontend() {
        # Get the answers from the frontend ...
        $answersFromFrontend = $this->req->getParam('answers');
        # ... and prepare/sanitize them:
        # +) remove all options with value === false
        # +) remove all answers with no options AND optionlessAnswerValue === ''
        foreach ( $answersFromFrontend as $questionId => $answerValueOrOptions ) {
            if ( isset( $answerValueOrOptions['options'] )) {
                foreach ( $answerValueOrOptions['options'] as $optionId => $value ) {
                    if ( $value === false ) unset( $answersFromFrontend[$questionId]['options'][$optionId] );
                }
                if ( count( $answersFromFrontend[$questionId]['options'] ) === 0 ) {
                    unset(  $answersFromFrontend[$questionId]['options'] );
                }
            }
            if ( !isset( $answersFromFrontend[$questionId]['optionlessAnswerValue'][0] ) and !isset( $answersFromFrontend[$questionId]['options'] )) {
                unset( $answersFromFrontend[$questionId] );
            }
        }
        return $answersFromFrontend;
    }

    /**
     * Create (if not exists) or update the questionnaire participation.
     */
    private function createOrUpdateQuestionnaireParticipation() {

        $participationId = QuestionnaireParticipation::getParticipationId_byReference( $this->args['parentType'], $this->args['parentId'] );

        $questionnaireParticipation = BeanFactory::getBean('QuestionnaireParticipations');

        if ( !$participationId ) {
            # No particpation yet? Create it now:

            $questionnaireParticipation->starttime = $this->currentDatetime;
            $questionnaireParticipation->parent_type = $this->args['parentType'];
            $questionnaireParticipation->parent_id = $this->args['parentId'];
            $questionnaireParticipation->questionnaire_id = $this->participationParent->questionnaire_id;
            $questionnaireParticipation->contact_id = $this->participationParent->contact_id;
            if ( $this->req->getParam('setCompleted')) {
                $questionnaireParticipation->completed = true;
                $questionnaireParticipation->endtime = $this->currentDatetime;
            }
            $questionnaireParticipation->save();

        } else {
            # Participation already exists. So retrieve it:

            $questionnaireParticipation->retrieve( $participationId );
            # Set to completed (and set the end time, if not already set):
            if ( $this->req->getParam('setCompleted')) {
                if ( empty( $questionnaireParticipation->endtime )) $questionnaireParticipation->endtime = $this->currentDatetime;
                $questionnaireParticipation->completed = true;
                $questionnaireParticipation->save();
            }
        }

        return $questionnaireParticipation;
    }

    /**
     * Retrieve the related questionnaire.
     */
    private function retrieveQuestionnaire() {
        $this->questionnaireParticipation->load_relationship('questionnaire');
        $dummy = $this->questionnaireParticipation->get_linked_beans('questionnaire');
        if ( !count( $dummy )) {
            $this->db->transactionRollback();
            throw new Exception('Missing questionnaire for Questionnaire Participation.');
        }
        $this->questionnaire = $dummy[0];
    }

    /**
     * Retrieve questionnaire by ID.
     */
    private function retrieveQuestionnaireByID( $id ) {
        $questionnaire = BeanFactory::getBean('Questionnaires');
        $questionnaire->retrieve( $id );
        if ( $questionnaire ) $this->questionnaire = $questionnaire;
        else {
            $this->db->transactionRollback();
            throw new Exception('Missing questionnaire.');
        }
    }

    /**
     * Retrieve the related questions for every question set of the questionnaire.
     * And collect them in _one_ array:
     */
    private function retrieveQuestionsOfQuestionnaire() {
        $questionsOfQuestionnaire = [];
        foreach ( $this->questionsetsOfQuestionnaire as $questionset ) {
            $dummy = $questionset->get_linked_beans('questions');
            if ( count( $dummy )) $questionsOfQuestionnaire = array_merge( $dummy, $questionsOfQuestionnaire );
        }
        $this->questionsOfQuestionnaire = [];
        foreach ( $questionsOfQuestionnaire as $question ) $this->questionsOfQuestionnaire[$question->id] = $question;
    }

    /**
     * Retrieve all question set participations related to the questionnaire participation.
     */
    private function getQuestionSetParticipationsFromDB() {
        $dummy = $this->questionnaireParticipation->get_linked_beans('questionsetparticipations');
        $questionSetParticipationsInDB = [];
        foreach ( $dummy as $participation ) {
            $questionSetParticipationsInDB[$participation->questionset_id] = $participation;
        }
        return $questionSetParticipationsInDB;
    }

    /**
     * Create or update the question set participations. That means:
     * Iterate over all given answers
     * and check if there is already a question set participation (for the question set of the related question). Then only set the end time to current time.
     * If there is no question set participation, create it.
     */
    private function createOrUpdateQuestionSetParticipations() {
        $affectedQuestionsets = [];
        foreach ( $this->answersFromFrontend as $questionId => $dummy ) {
            $questionsetId = $this->questionsOfQuestionnaire[$questionId]->questionset_id;
            $affectedQuestionsets[$questionsetId] = true;
            $questionSetParticipation = BeanFactory::getBean( 'QuestionSetParticipations' );
            if ( !isset( $this->questionSetParticipationsInDB[$questionsetId] )) {

                $questionSetParticipation->starttime = $this->currentDatetime;
                $questionSetParticipation->endtime = $this->currentDatetime;
                $questionSetParticipation->questionset_id = $questionsetId;
                $questionSetParticipation->referencetype = $this->args['parentType']; # to remove in future
                $questionSetParticipation->referenceid = $this->args['parentId']; # to remove in future
                $questionSetParticipation->questionnaireparticipation_id = $this->questionnaireParticipation->id;
                if ( $this->req->getParam('setCompleted')) $questionSetParticipation->endtime->endtime = $this->currentDatetime;
                $questionSetParticipation->save();
                $this->questionSetParticipationsInDB[$questionsetId] = $questionSetParticipation;

            } else {

                if ( $this->req->getParam('setCompleted')) {
                    if ( empty( $this->questionSetParticipationsInDB[$questionsetId] )) $this->questionSetParticipationsInDB[$questionsetId]->endtime = $this->currentDatetime;
                    $this->questionSetParticipationsInDB[$questionsetId]->save();
                }

            }
        }
        return $affectedQuestionsets;
    }

    /**
     * Get the parent record ...
     * ... to determine the attached questionnaire and the attached contact.
     * ... to make sure the reference record exists.
     */
    private function getParticipationParent() {
        $participationParent = BeanFactory::getBean( $this->args['parentType'] );
        $participationParent->retrieve( $this->args['parentId'] );
        if ( !isset( $participationParent->id )) {
            $this->db->transactionRollback();
            throw ( new NotFoundException('Parent for Questionnaire Participation not found.'))->setLookedFor(['id' => $this->args['parentId'], 'module' => $this->args['parentType']]);
        }
        return $participationParent;
    }

    /**
     * Delete from the database earlier created question set participations (and their answers) with (NOW) no answers.
     */
    private function deleteEmptyQuestionSetParticipations() {
        foreach ( $this->questionSetParticipationsInDB as $questionsetId => $questionSetParticipation ) {
            if ( !isset( $this->affectedQuestionsets[$questionsetId] )) {
                $existingUnaffectedAnswers = $questionSetParticipation->get_linked_beans('questionanswers');
                foreach( $existingUnaffectedAnswers as $answer ) {
                    $answer->mark_deleted( $answer->id );
                    $answer->save();
                }
                $questionSetParticipation->mark_deleted( $questionSetParticipation->id );
                $questionSetParticipation->save();
            }
        }
    }

    /**
     * Retrieve the answers from DB.
     */
    private function retrieveAnswersFromDB() {
        $this->answersInDB = [];
        foreach ( $this->questionSetParticipationsInDB as $questionsetId => $qsParticipation ) {
            foreach ( $qsParticipation->get_linked_beans('questionanswers') as $answer ) {
                if ( empty( $answer->questionoption_id )) {
                    $this->answersInDB[$answer->question_id] = $answer;
                } else {
                    $this->answersInDB[$answer->question_id][$answer->questionoption_id] = $answer;
                }
            }
        }
    }

    /**
     * Delete answers from DB.
     */
    private function deleteAnswersFromDB() {
        foreach ( $this->answersInDB as $questionId => $answerOrAnswers ) {

            if ( !is_array( $answerOrAnswers )) {
                # Answer without options but with optionlessAnswerValue:
                if ( !isset( $this->answersFromFrontend[$questionId]['optionlessAnswerValue'][0] )) {
                    $answerOrAnswers->mark_deleted( $answerOrAnswers->id );
                    $answerOrAnswers->save();
                }
                continue;
            } else {
                # Answer with options:
                foreach ( $answerOrAnswers as $answer ) {
                    if (
                        !isset( $this->answersFromFrontend[$questionId] )
                        or count( $this->answersFromFrontend[$questionId]['options'] ) === 0
                        or !isset( $this->answersFromFrontend[$questionId]['options'][$answer->questionoption_id] )
                        or $this->answersFromFrontend[$questionId]['options'][$answer->questionoption_id] === false
                    ) {
                        # Delete answer from db and save it:
                        $answer->mark_deleted( $answer->id );
                        $answer->save();
                    }
                }
            }
        }
    }

    /**
     * Save the answers to DB.
     */
    private function saveAnswersToDB() {
        foreach ( $this->answersFromFrontend as $questionId => $answerValueOrOptions ) {

            if ( !isset( $this->questionsOfQuestionnaire[$questionId] )) {
                $this->db->transactionRollback();
                throw ( new BadRequestException( 'Answer for a not existing question.' ) )->setErrorCode('answerForUnknownQuestion')->setDetails([ 'questionId' => $questionId ]);
            }
            $questionType = $this->questionsetsOfQuestionnaire[$this->questionsOfQuestionnaire[$questionId]->questionset_id]->questiontype;
            if ( isset( $answerValueOrOptions['options'] ) and !$this->questiontypeWithOptions( $questionType )) {
                $this->db->transactionRollback();
                throw ( new BadRequestException( 'Question type "'.$questionType.'" has no options.' ) )->setErrorCode('noOptionsAllowed');
                if ( isset( $answerValueOrOptions['optionlessAnswerValue'] )) {
                    $this->db->transactionRollback();
                    throw ( new BadRequestException('Answer value not allowed.'))->setErrorCode('noAnswerValue');
                }
            }

            if ( isset( $answerValueOrOptions['options'] ) ) {

                foreach ( $answerValueOrOptions['options'] as $optionId => $trueOrValue ) {
                    if ( !isset( $this->answersInDB[$questionId] ) or !isset( $this->answersInDB[$questionId][$optionId] )) {
                        $questionAnswer = BeanFactory::getBean('QuestionAnswers');
                        $questionAnswer->question_id = $questionId;
                        $questionAnswer->questionsetparticipation_id = $this->questionSetParticipationsInDB[$this->questionsOfQuestionnaire[$questionId]->questionset_id]->id;
                        $questionAnswer->questionoption_id = $optionId;
                        if ( $trueOrValue !== true ) $questionAnswer->optionlessAnswerValue = $trueOrValue;
                        $questionAnswer->save();
                    }
                }

            } elseif ( isset( $answerValueOrOptions['optionlessAnswerValue'] ) ) {

                if ( !isset( $this->answersInDB[$questionId] )) {
                    $this->answersInDB[$questionId] = BeanFactory::getBean('QuestionAnswers');
                    $this->answersInDB[$questionId]->question_id = $questionId;
                    $this->answersInDB[$questionId]->questionsetparticipation_id = $this->questionSetParticipationsInDB[$this->questionsOfQuestionnaire[$questionId]->questionset_id]->id;
                }
                $this->answersInDB[$questionId]->optionlessAnswerValue = $answerValueOrOptions['optionlessAnswerValue'];
                $this->answersInDB[$questionId]->save();

            }
        }
    }

    /**
     * Retrieve the related question sets.
     */
    private function retrieveQuestionsetsOfQuestionnaire() {
        $this->questionsetsOfQuestionnaire = [];
        foreach ( $this->questionnaire->get_linked_beans('questionsets') as $questionset ) {
            $this->questionsetsOfQuestionnaire[$questionset->id] = $questionset;
        }
    }

    /**
     * Save answers and create/update the associated questionnaire participation and associated question set participations.
     * The questionnaire participation is identified by parent (type and id).
     */
    public function saveAnswers_byParent( $req, $res, $args ) {

        $this->init( $req, $args );

        $this->answersFromFrontend = $this->prepareAnswersFromFrontend();

        $this->participationParent = $this->getParticipationParent();

        $this->db->transactionStart();

        $this->questionnaireParticipation = $this->createOrUpdateQuestionnaireParticipation();

        # Retrieve basics: questionnaire, question sets, questions.
        $this->retrieveQuestionnaire();
        $this->retrieveQuestionsetsOfQuestionnaire();
        $this->retrieveQuestionsOfQuestionnaire();

        # Do the question set participations: get the existing from db, create the new ones or update the existing, delete the not longer necessary.
        $this->questionSetParticipationsInDB = $this->getQuestionSetParticipationsFromDB();
        $this->affectedQuestionsets = $this->createOrUpdateQuestionSetParticipations();
        $this->deleteEmptyQuestionSetParticipations();

        $this->retrieveAnswersFromDB();
        $this->saveAnswersToDB();
        $this->deleteAnswersFromDB();

        # No errors so far, so commit to the database.
        $this->db->transactionCommit();

        # Re-Do the evaluation.
        QuestionnaireParticipation::getEvaluation( $this->questionnaireParticipation->id );

        $response['questionnaireParticipationId'] = $this->questionnaireParticipation->id;

        return $res->withJson( $response );

    }

}
