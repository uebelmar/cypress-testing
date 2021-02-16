<?php
namespace SpiceCRM\modules\Questionnaires\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\modules\Questionnaires\Questionnaire;

class QuestionnairesKRESTController
{
    public function questionnaireIdOfInstance($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $questionnaireId = $db->getOne( sprintf(
            'SELECT DISTINCT qs.questionnaire_id FROM questionsetparticipations qsp'
            . ' INNER JOIN questionsets qs ON qsp.questionset_id = qs.id WHERE qsp.deleted = 0 AND qs.deleted = 0'
            . ' AND qsp.referenceid = "%s"', $args['referenceId']
        ));
        return $res->withJson( [ 'questionnaireId' => $questionnaireId ] );
    }

    public function evaluation($req, $res, $args) {
        $questionnaire = BeanFactory::getBean('Questionnaires');
        if ( $evaluation = $questionnaire->getEvaluation( $args['referenceId'] )) {
            list( $evaluationType, $questionnaireId, $allAffectedCategories ) = $evaluation;
            return $res->withJson( [ 'participated' => true, 'evaluationType' => $evaluationType ?: 'none', 'values' => $allAffectedCategories ] );
        } else
            return $res->withJson( [ 'participated' => false, 'evaluationType' => null, 'values' => null ] );
    }

    public function interpretationsOfInstance($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $assignedInterpretations = [];
        $dbResult  = $db->query( sprintf(
            'SELECT qi.* FROM supconsultingorderitems_questionnaireinterpretations sq'
            . ' INNER JOIN questionnaireinterpretations qi ON sq.questionnaireinterpretation_id = qi.id'
            . ' WHERE qi.deleted = 0 AND sq.deleted = 0 AND sq.supconsultingorderitem_id = "%s"', $args['referenceId']
        ));
        while ( $dummy = $db->fetchByAssoc( $dbResult, false ) ) $assignedInterpretations[] = $dummy;

        return $res->withJson( $assignedInterpretations );
    }

    public function interpretationsOfInstanceSuggested($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $questionnaire = BeanFactory::getBean('Questionnaires');
        list( $evaluationType, $questionnaireId, $allAffectedCategories ) = $questionnaire->getEvaluation( $args['referenceId'] );

        $questionnaireData = $db->fetchOne( sprintf(
            'SELECT evaluationtype, interpretationsuggestions FROM questionnaires WHERE deleted = 0 AND id = "%s"', $questionnaireId
        ));

        $suggestedInterpretations = [];

        if ( $evaluationType === 'mbti') {

            foreach ( $allAffectedCategories as $v ) $mbtiArray[strtoupper( $v['abbreviation'] )] = $v;

            $mbtiString = '';
            $pairs = [ [ 'E', 'I' ], [ 'S', 'N' ], [ 'T', 'F' ], [ 'J', 'P' ]];
            for ( $x=0; $x<4; $x++ ) {
                $diff =  $mbtiArray[$pairs[$x][0]][points] - $mbtiArray[$pairs[$x][1]][points];
                if ( $diff > 2 ) $mbtiString .= $pairs[$x][0];
                elseif ( $diff < -2 ) $mbtiString .= $pairs[$x][1];
                else $mbtiString .= '*';
            }


            for ( $x = 0; $x < 4; $x++ ) {
                if ( $mbtiString[$x] !== '*' ) $sql[] = 'categories LIKE "%' . $mbtiArray[$mbtiString[$x]]['id'] . '%"';
                else $sql[] = '( categories LIKE "%'.$mbtiArray[$pairs[$x][0]]['id'].'%" OR categories LIKE "%'.$mbtiArray[$pairs[$x][1]]['id'].'%" )';
            }
            $dbResult  = $db->query( $r="SELECT * FROM questionnaireinterpretations WHERE questionnaire_id='$questionnaireId' AND deleted = 0 AND " . implode( ' AND ', $sql ));
            while ( $dummy = $db->fetchByAssoc( $dbResult, false ) ) $suggestedInterpretations[] = $dummy;

        }  else {

            if ( $questionnaireData['interpretationsuggestions'] === 'all' ) {

                $dbResult  = $db->query( sprintf('SELECT * FROM questionnaireinterpretations qi WHERE qi.deleted = 0 AND qi.questionnaire_id = "%s"', $questionnaireId ));
                while ( $dummy = $db->fetchByAssoc( $dbResult, false ) ) $suggestedInterpretations[] = $dummy;

            } else {

                $selectedCategories = [];

                usort( $allAffectedCategories, function( $a, $b ) {
                    return ( $b['points'] - $a['points'] );
                } );

                $topX = $bottomX = $upFromX = false;
                if ( preg_match( '#^top(\d+)$#', $questionnaireData['interpretationsuggestions'], $found ) ) {
                    $topX = $found[1];
                } elseif ( preg_match( '#^top(\d+)_bottom(\d+)$#', $questionnaireData['interpretationsuggestions'], $found ) ) {
                    $topX = $found[1];
                    $bottomX = $found[2];
                } elseif ( preg_match( '#^upfrom(\d+)$#', $questionnaireData['interpretationsuggestions'], $found ) ) {
                    $upFromX = $found[1];
                } elseif ( preg_match( '#^top(\d+)_upfrom(\d+)$#', $questionnaireData['interpretationsuggestions'], $found ) ) {
                    $topX = $found[1];
                    $upFromX = $found[2];
                }

                if ( $topX !== false )
                    foreach ( array_slice( $allAffectedCategories, 0, $topX, true ) as $k => $v ) $selectedCategories[$k] = $v;

                if ( $bottomX !== false )
                    foreach ( array_slice( $allAffectedCategories, $bottomX * -1, $bottomX, true ) as $k => $v ) $selectedCategories[$k] = $v;

                if ( $upFromX !== false ) {
                    foreach ( $allAffectedCategories as $k => $v )
                        if ( $v['points'] >= $upFromX ) $selectedCategories[$k] = $v;
                }

                $sql = [];
                foreach ( $selectedCategories as $v )
                    $sql[] = 'categories LIKE "%'.$v['id'].'%"';

                $dbResult  = $db->query( "SELECT * FROM questionnaireinterpretations WHERE questionnaire_id='$questionnaireId' AND deleted = 0 AND (" . implode( ' OR ', $sql ) . ")");
                while ( $dummy = $db->fetchByAssoc( $dbResult, false ) ) $suggestedInterpretations[] = $dummy;

            }

        }

        return $res->withJson( $suggestedInterpretations );
    }

    public function getResults( $req, $res, $args ) {

        // get the reference bean
        $referenceBean = BeanFactory::getBean( $args['referenceType'] );
        $referenceBean->retrieve( $args['referenceId'], false, null, false );
        if ( empty( $referenceBean->id ))
            throw ( new NotFoundException( '"'.$args['referenceType'].'" not found.'))->setLookedFor( ['id' => $args['referenceId'], 'module'=> $args['referenceType'] ] );

        // todo: access restriction necessary?

        return $res->withJson( Questionnaire::getResults( $args['referenceType'], $args['referenceId'] ) );

    }

    public function getQuestionnaireEvaluation( $req, $res, $args ) {
        $db = DBManagerFactory::getInstance();

        $questions = [];
        $questionsAnswered = [];
        $optionlessAnswerValues = [];
        $options = [];

        /*
        $dbResult = $db->query( $sql = sprintf('
            SELECT qa.questionoption_id, qo.question_id, qsp.questionnaireparticipation_id, qa.optionlessAnswerValue FROM questionnaires qe 
            INNER JOIN questionsets qs ON qe.id = "%s" AND qe.id = qs.questionnaire_id AND qs.deleted = 0 AND qe.deleted = 0
            INNER JOIN questions qn ON qs.id = qn.questionset_id AND qn.deleted = 0 
            LEFT JOIN questionoptions qo ON qn.id = qo.question_id AND qo.deleted = 0 
            INNER JOIN questionanswers qa ON ( qo.id = qa.questionoption_id OR qa.questionoption_id IS NULL ) AND qa.deleted = 0 and questionsetparticipation_id IS NOT NULL
            INNER JOIN questionsetparticipations qsp ON qsp.id = qa.questionsetparticipation_id AND qsp.deleted = 0
            INNER JOIN questionnaireparticipations qep ON qep.id = qsp.questionnaireparticipation_id AND qep.deleted = 0
        ', $db->quote( $args['questionnaireId'] )));
        */

        $dbResult = $db->query( $sql = sprintf('
            SELECT question_id, qo.id, qs.questiontype, qo.name
            FROM questionnaires qe 
            INNER JOIN questionsets qs ON qe.id = "%s" AND qe.id = qs.questionnaire_id AND qs.deleted = 0 AND qe.deleted = 0
            INNER JOIN questions qn ON qs.id = qn.questionset_id AND qn.deleted = 0 
            INNER JOIN questionoptions qo ON qo.question_id = qn.id AND qo.deleted = 0
        ', $db->quote( $args['questionnaireId'] )));

        while ( $dbRow = $db->fetchByAssoc( $dbResult, false ) ) {
            $options[$dbRow['id']] = $dbRow;
            $optionsByQuestion[$dbRow['question_id']][$dbRow['id']] = $dbRow;
        }

        $dbResult = $db->query( $sql = sprintf('
            SELECT qsp.questionnaireparticipation_id, qa.optionlessAnswerValue, question_id, questionoption_id, qs.questiontype
            FROM questionnaires qe 
            INNER JOIN questionsets qs ON qe.id = "%s" AND qe.id = qs.questionnaire_id AND qs.deleted = 0 AND qe.deleted = 0
            INNER JOIN questions qn ON qs.id = qn.questionset_id AND qn.deleted = 0 
            INNER JOIN questionanswers qa ON qn.id = qa.question_id AND qa.deleted = 0
            INNER JOIN questionsetparticipations qsp ON qsp.id = qa.questionsetparticipation_id AND qsp.deleted = 0
            INNER JOIN questionnaireparticipations qep ON qep.id = qsp.questionnaireparticipation_id AND qep.deleted = 0
        ', $db->quote( $args['questionnaireId'] )));

        while ( $dbRow = $db->fetchByAssoc( $dbResult, false ) ) {

            if ( !isset( $questions[$dbRow['question_id']] )) $questions[$dbRow['question_id']] = [ 'countParticipations' => 0 ];

            if ( isset( $dbRow['questionoption_id'][0]) and isset( $options[$dbRow['questionoption_id']] )) {
                if ( !$questions[$dbRow['question_id']]['optionCounts'][$dbRow['questionoption_id']] ) $questions[$dbRow['question_id']]['optionCounts'][$dbRow['questionoption_id']] = 0;
                $questions[$dbRow['question_id']]['optionCounts'][$dbRow['questionoption_id']]++;
            }

            $questions[$dbRow['question_id']]['questiontype'] = $dbRow['questiontype'];

            if ( !isset( $dbRow['questionoption_id'][0]) and $dbRow['questiontype'] === 'nps' ) $optionlessAnswerValues[$dbRow['question_id']] += $dbRow['optionlessAnswerValue'];

            if ( $dbRow['questiontype'] === 'rating' and self::isRatingQuestionNumeric( $dbRow['question_id'] , $optionsByQuestion )) $optionlessAnswerValues[$dbRow['question_id']] += $options[$dbRow['questionoption_id']]['name'];

            $questionsAnswered[$dbRow['question_id']][$dbRow['questionnaireparticipation_id']] = true;
            $questionnairesAnswered[$dbRow['questionnaireparticipation_id']] = true;

        }

        foreach ( $questionsAnswered as $questionId => $question ) {
            foreach ( $question as $participationId => $v ) {
                $questions[$questionId]['countParticipations']++;
            }
        }

        foreach ( $questions as $k => $v ) {
            if ( isset( $optionlessAnswerValues[$k] )) {
                $questions[$k]['optionlessAnswerValue'] = $optionlessAnswerValues[$k];
            }
        }

        return $res->withJson(['countQuestionnaireParticipations'=> count( $questionnairesAnswered ), 'answers' => $questions ]);
    }

    public function isRatingQuestionNumeric( $questionId, $options ) {
        # static
        foreach ( $options[$questionId] as $k => $v ) {
            if ( !is_numeric( $v['name'])) return false;
        }
        return true;
    }

    public function getFullQuestionnaire( $req, $res, $args ) {

        $questionnaire = BeanFactory::getBean('Questionnaires', $args['questionnaireId'], ['encode'=>false]);
        if (!$questionnaire) {
            throw (new NotFoundException('Questionnaire not found.'))->setLookedFor(['module' => 'Questionnaires','id'=>$args['questionnaireId']])->setErrorCode('noQuestionnaire');
        }
        return $res->withJson($questionnaire->getQuestionnaireArray());
    }
}


