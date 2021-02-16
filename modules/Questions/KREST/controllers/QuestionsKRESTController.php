<?php
namespace SpiceCRM\modules\Questions\KREST\controllers;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\BadRequestException;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\authentication\AuthenticationController;

class QuestionsKRESTController
{
    public function getAnswerValues($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $responseData = [];

        // determine question type
        if (( $questionType = $db->getOne( sprintf( 'SELECT questiontype FROM questions q INNER JOIN questionsets qs ON q.questionset_id = qs.id WHERE q.id = "%s" AND q.deleted = 0 AND qs.deleted = 0', $db->quote( $args['questionId'] )))) === false )
            throw ( new NotFoundException('Question (or related QuestionSet) not found.'))->setLookedFor(['id'=>$args['questionId'],'module'=>'Questions']);

        if (( $participation = $db->fetchOne( sprintf( 'SELECT *, UNIX_TIMESTAMP(starttime) AS starttime_unix FROM questionsetparticipations WHERE id = "%s" AND deleted = 0', $db->quote( $args['participationId'] )))) === false )
            throw ( new NotFoundException('QuestionSetParticipation not found.'))->setLookedFor(['id'=>$args['participationId'],'module'=>'QuestionSetParticipations']);

        if ( $participation['assigned_user_id'] !== $current_user->id )
            throw new ForbiddenException('Assigned user of participation is not current user.');

        if ( $questionType === 'text' ) {

            if (( $answer = $db->fetchOne( sprintf( 'SELECT * FROM questionanswers WHERE deleted = 0 AND question_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $args['questionId'] ), $db->quote( $args['participationId'] )))) === false )
                $responseData['optionlessAnswerValue'] = null;
            else $responseData['optionlessAnswerValue'] = $answer['optionlessAnswerValue'];

        } else { // question type is 'single', 'multi', 'binary' or 'rating'

            $dbResult = $db->query( sprintf( 'SELECT questionoption_id FROM questionanswers WHERE deleted = 0 AND question_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $args['questionId'] ), $db->quote( $args['participationId'] ))) ;
            while ( $dummy = $db->fetchByAssoc( $dbResult ) ) $allAnswers[] = $dummy;
            foreach ( $allAnswers as $a ) $responseData[$a['questionoption_id']] = true;

        }

        return $res->withJson( $responseData, 200, JSON_FORCE_OBJECT );
    }

    public function postAnswerValues($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $responseData = [];
        $now = time();

        $params = json_decode( $req->getBody(), true);

        // get question set (for question type)
        if (( $questionSet = $db->fetchOne( sprintf( 'SELECT questiontype, timelimit FROM questions q INNER JOIN questionsets qs ON q.questionset_id = qs.id WHERE q.id = "%s" AND q.deleted = 0 AND qs.deleted = 0', $db->quote( $args['questionId'] )))) === false )
            throw ( new NotFoundException('Question (or related QuestionSet) not found.'))->setLookedFor(['id'=>$args['questionId'],'module'=>'Questions']);

        if (( $participation = $db->fetchOne( sprintf( 'SELECT *, UNIX_TIMESTAMP(starttime) AS starttime_unix FROM questionsetparticipations WHERE id = "%s" AND deleted = 0', $db->quote( $args['participationId'] )))) === false )
            throw ( new NotFoundException('QuestionSetParticipation not found.'))->setLookedFor(['id'=>$args['participationId'],'module'=>'QuestionSetParticipations']);

        if ( $participation['assigned_user_id'] !== $current_user->id )
            throw new ForbiddenException("Assigned user of participation is not current user. ( ${participation['assigned_user_id']}, $current_user->id )");

        if ( $questionSet['timelimit'] and $participation['starttime_unix']+$questionSet['timelimit'] > $now )
            throw new ForbiddenException('End Time reached, Participation forbidden.');

        if ( $questionSet['questiontype'] === 'nps' ) {
            $params['optionlessAnswerValue'] = trim( $params['optionlessAnswerValue'] );
            if ( !ctype_digit( $params['optionlessAnswerValue'] ) || $params['optionlessAnswerValue'] < 0 || $params['optionlessAnswerValue'] > 10 )
                throw new BadRequestException('NPS answer is invalid. Must be a number from 0 to 10.');
        }

        if ( $questionSet['questiontype'] === 'text' or $questionSet['questiontype'] === 'nps' ) {

            if ( $db->getOne( sprintf( 'SELECT COUNT(*) FROM questionanswers WHERE deleted = 0 AND question_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $args['questionId'] ), $db->quote( $args['participationId'] ) ) ) )
                $db->query( sprintf( 'UPDATE questionanswers SET optionlessAnswerValue = "%s", date_modified = NOW(), modified_user_id = "%s" WHERE question_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $params['optionlessAnswerValue'] ), $db->quote( $current_user->id ), $db->quote( $args['questionId'] ), $db->quote( $args['participationId'] ) ) );
            else
                $db->query( sprintf( 'INSERT INTO questionanswers SET id = "%s", optionlessAnswerValue = "%s", question_id = "%s", date_entered = NOW(), date_modified = NOW(), modified_user_id = "%s", questionsetparticipation_id = "%s"',
                    $db->quote( create_guid() ), $db->quote( $params['optionlessAnswerValue'] ), $db->quote( $args['questionId'] ), $db->quote( $current_user->id ), $db->quote( $args['participationId'] ) ) );

            $responseData['optionlessAnswerValue'] = $db->getOne( sprintf( 'SELECT optionlessAnswerValue FROM questionanswers WHERE deleted = 0 AND question_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $args['questionId'] ), $db->quote( $args['participationId'] ) ) );

        } else {

            foreach ( @$params as $optionId => $answerValue ) {

                if ( $questionSet['questiontype'] === 'ist' ) {

                    if ( !is_bool( $answerValue )) { # we only store text answers
                        if ( $db->getOne( sprintf( 'SELECT COUNT(*) FROM questionanswers WHERE deleted = 0 AND questionoption_id ="%s" AND questionsetparticipation_id = "%s"', $db->quote( $optionId ), $db->quote( $args['participationId'] ) ) ) )
                            $db->query( sprintf( 'UPDATE questionanswers SET optionlessAnswerValue = "%s", date_modified = NOW(), modified_user_id = "%s" WHERE questionoption_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $answerValue ), $db->quote( $current_user->id ), $db->quote( $optionId ), $db->quote( $args['participationId'] ) ) );
                        else
                            $db->query( sprintf( 'INSERT INTO questionanswers SET id = "%s", optionlessAnswerValue = "%s", questionoption_id = "%s", date_entered = NOW(), date_modified = NOW(), modified_user_id = "%s", questionsetparticipation_id = "%s", question_id = "%s"',
                                $db->quote( create_guid() ), $db->quote( $answerValue ), $db->quote( $optionId ), $db->quote( $current_user->id ), $db->quote( $args['participationId'] ), $db->quote( $args['questionId'] ) ) );
                    }

                } else { // question type is 'single', 'multi', 'binary' or 'rating':

                    if ( $answerValue === true ) {

                        if ( ( $answer = $db->fetchOne( sprintf( 'SELECT * FROM questionanswers WHERE deleted = 0 AND questionoption_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $optionId ), $db->quote( $args['participationId'] ) ) ) ) === false )
                            $db->query( sprintf( 'INSERT INTO questionanswers SET id = "%s", questionoption_id = "%s", questionsetparticipation_id = "%s", question_id = "%s", date_entered = NOW(), date_modified = NOW(), modified_user_id = "%s"',
                                $db->quote( create_guid() ), $db->quote( $optionId ), $db->quote( $args['participationId'] ), $db->quote( $args['questionId'] ), $db->quote( $current_user->id ) ) );

                    } else { // $answerValue === false

                        $db->query( sprintf( 'DELETE FROM questionanswers WHERE questionoption_id ="%s" AND questionsetparticipation_id = "%s"', $db->quote( $optionId ), $db->quote( $args['participationId'] ) ) );

                    }
                }
            }
        }

        $dbResult = $db->query( sprintf( 'SELECT questionoption_id, optionlessAnswerValue FROM questionanswers WHERE deleted = 0 AND question_id = "%s" AND questionsetparticipation_id = "%s"', $db->quote( $args['questionId'] ), $db->quote( $args['participationId'] ) ) );
        while ( $dummy = $db->fetchByAssoc( $dbResult ) )
            $responseData[$dummy['questionoption_id']] = ( $questionSet['questiontype'] === 'ist' ? $dummy['optionlessAnswerValue'] : true );

        return $res->withJson( $responseData, 200, JSON_FORCE_OBJECT );
    }
}
