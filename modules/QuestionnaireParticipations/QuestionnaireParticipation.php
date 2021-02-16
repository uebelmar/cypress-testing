<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\QuestionnaireParticipations;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\modules\Questionnaires\Questionnaire;

class QuestionnaireParticipation extends SugarBean
{
    public $table_name = "questionnaireparticipations";
    public $object_name = "QuestionnaireParticipation";
    public $module_dir = 'QuestionnaireParticipations';

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
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $starttime = ( new \DateTime( $this->starttime, new \DateTimeZone('UTC')) )
            ->setTimezone( new \DateTimeZone( $current_user->getPreference('timezone', 'global' )))
            ->format( $current_user->getPreference('datef', 'global').', '.$current_user->getPreference('timef', 'global' ));
        return $starttime.', '.( empty( $this->contact_name ) ? '':$this->contact_name.', ' ).$this->questionnaire_name;
    }

    public function getAllQuestionAnswers( $participationId )
    {
        $db = DBManagerFactory::getInstance();
        $allQuestionAnswers = array();
        $dbResult = $db->query( sprintf('SELECT q.id as question_id, qa.* FROM questionsetparticipations qsp INNER JOIN questionsets qs ON qsp.questionnaireparticipation_id = "%s" AND qsp.questionset_id = qs.id INNER JOIN questions q ON q.questionset_id = qs.id INNER JOIN questionanswers qa ON q.id = qa.question_id AND qsp.id = qa.questionsetparticipation_id WHERE qsp.deleted = 0 AND qs.deleted = 0 AND qa.deleted = 0', $db->quote( $participationId )));
        while ($dummy = $db->fetchByAssoc($dbResult, false))
            $allQuestionAnswers[$dummy['question_id']][] = $dummy;
        return $allQuestionAnswers;
    }

    public static function getEvaluation( $participationId )
    {
        $db = DBManagerFactory::getInstance();
        if (($allQuestionSets = self::getAllQuestionSets( $participationId )) === false) return false;

        $allCategories = Questionnaire::getAllCategories();
        $allQuestionAnswers = QuestionnaireParticipation::getAllQuestionAnswers( $participationId );
        $allQuestions = self::getAllQuestions( $participationId );
        $allQuestionOptions = self::getAllQuestionOptions( $participationId );

        $allAffectedCategories = array();

        reset($allQuestionSets);
        $questionnaireId = $allQuestionSets[key($allQuestionSets)]['questionnaire_id'];

        $answeredQuestionsForOptions = [];

        $dbResult = $db->query(sprintf('SELECT qo.id, qo.categories FROM questionnaires qn INNER JOIN questionsets qs ON qn.id = qs.questionnaire_id INNER JOIN questions q ON qs.id = q.questionset_id INNER JOIN questionoptions qo ON q.id = qo.question_id WHERE qn.deleted = 0 AND qs.deleted = 0 AND q.deleted = 0 AND qo.deleted = 0 AND qn.id = "%s"', $db->quote($questionnaireId)));
#        $dbResult = $db->query(sprintf('SELECT qo.id, qo.categories FROM questionnaires qn INNER JOIN questionsets qs ON qn.id = qs.questionnaire_id INNER JOIN questions q ON qs.id = q.questionset_id INNER JOIN questionoptions qo ON q.id = qo.question_id WHERE qn.deleted = 0 AND qs.deleted = 0 AND q.deleted = 0 AND qo.deleted = 0 AND qn.id = "%s"', $db->quote($questionnaireId)));
        #       while ($dummy = $db->fetchByAssoc($dbResult, false)) {
        $categorypool = $db->getOne(sprintf('SELECT categorypool FROM questionnaires WHERE id = "%s"', $db->quote($questionnaireId)));
        #foreach (explode(',', $dummy['categories']) as $v) {
        foreach (explode(',', $categorypool ) as $v) {
            if (!isset($allAffectedCategories[$v])) {
                $allAffectedCategories[$v] = array(
                    'points' => 0,
                    'count' => 0,
                    'id' => $allCategories[$v]['id'],
                    'name' => $allCategories[$v]['name'],
                    'abbreviation' => $allCategories[$v]['abbreviation']
                );
            }
        }
        #}

        foreach ($allQuestionSets as $questionSet) {
            foreach ($allQuestions[$questionSet['id']] as $question) {

                if ($questionSet['questiontype'] !== 'text' and $questionSet['questiontype'] !== 'ist') {

                    $questionparameter = isset( $question['questionparameter'][0] ) ? json_decode( $question['questionparameter'], true ) : array();

                    $doCount = true;

                    if ( isset( $questionparameter['minAnswers'] ) and count( $allQuestionAnswers[$question['id']] ) < $questionparameter['minAnswers'] ) $doCount = false;
                    if ( $doCount and isset( $questionparameter['maxAnswers'] ) and count( $allQuestionAnswers[$question['id']] ) > $questionparameter['maxAnswers'] ) $doCount = false;

                    $hasInfosCorrectness = isset( $questionparameter['hasInfosCorrectness'] ) ? $questionparameter['hasInfosCorrectness'] : false;
                    if ( $doCount and $hasInfosCorrectness ) {
                        foreach ( $allQuestionAnswers[$question['id']] as $questionAnswer ) {
                            if ( $doCount and ! $allQuestionOptions[$questionAnswer['question_id']][$questionAnswer['questionoption_id']]['is_correct_option'] ) $doCount = false;
                        }
                    }

                    if ( $doCount ) {
                        foreach ( $allQuestionAnswers[$question['id']] as $questionAnswer ) {
                            foreach ( explode( ',', $allQuestionOptions[$questionAnswer['question_id']][$questionAnswer['questionoption_id']]['categories'] ) as $v ) {
                                $allAffectedCategories[$v]['points'] += $allQuestionOptions[$questionAnswer['question_id']][$questionAnswer['questionoption_id']]['points'];
                                $allAffectedCategories[$v]['count']++;
                                if ( !isset( $answeredQuestionsForOptions[$v] )) $answeredQuestionsForOptions[$v] = [];
                                $answeredQuestionsForOptions[$v][$questionAnswer['question_id']] = true;
                            }
                        }
                    }

                } elseif ($questionSet['questiontype'] === 'ist') {

                    $numCorrect = 0;
                    foreach ($allQuestionAnswers[$question['id']] as $questionAnswer)
                        if ( strcasecmp( trim( $allQuestionOptions[$question['id']][$questionAnswer['questionoption_id']]['answer'] ), trim( $questionAnswer['optionlessAnswerValue'] ) ) === 0 ) $numCorrect++;
                    if ($numCorrect === count($allQuestionOptions[$question['id']])) {
                        foreach ($allQuestionOptions[$question['id']] as $questionOption) {
                            foreach (explode(',', $questionOption['categories']) as $v) {
                                $allAffectedCategories[$v]['points'] += $questionOption['points'];
                                $allAffectedCategories[$v]['count']++;
                                if ( !isset( $answeredQuestionsForOptions[$v] )) $answeredQuestionsForOptions[$v] = [];
                                $answeredQuestionsForOptions[$v][$questionAnswer['question_id']] = true;
                            }
                        }
                    }

                } elseif ($questionSet['questiontype'] === 'text') {

                    if ( isset( $allQuestionAnswers[$question['id']][0] )) {
                        $questionAnswer = $allQuestionAnswers[$question['id']][0];
                        if ( isset( $allQuestionOptions[$questionAnswer['question_id']] )) {
                            $questionOption = current( $allQuestionOptions[$questionAnswer['question_id']] );
                            if ( $questionAnswer['optionlessAnswerValue'] == $questionOption['name'] ) {
                                foreach ( explode( ',', $questionOption['categories'] ) as $v ) {
                                    $allAffectedCategories[$v]['points'] += 1; # $questionOption['points']
                                }
                            }
                        }
                    }
                }

            }
        }



        $questionnaire = $db->fetchOne(sprintf('SELECT evaluationtype, evaluationsorting FROM questionnaires WHERE deleted = 0 AND id = "%s"', $questionnaireId));
        $evaluationType = $questionnaire['evaluationtype'];
        $evaluationSorting = $questionnaire['evaluationsorting'];

        # $allAffectedCategories = array_values($allAffectedCategories);

        if ($evaluationType === 'avg' || $evaluationType === 'motivatoren') {
            foreach ($allAffectedCategories as $k => $v) {
                $allAffectedCategories[$k]['points'] =
                    empty( $allAffectedCategories[$k]['count'] ) ? 0 : round($allAffectedCategories[$k]['points'] / $allAffectedCategories[$k]['count'], 1 );
            }
        } else if ( $evaluationType === 'avg_core' ) {
            foreach ( $allAffectedCategories as $k => $v ) {

                if ( !count( $answeredQuestionsForOptions[$k] )) $allAffectedCategories[$k]['points'] = 0;
                elseif ( $allAffectedCategories[$k]['count'] === 0 ) {
                    $allAffectedCategories[$k]['points'] = 0;
                } else {
                    $allAffectedCategories[$k]['points'] = $allAffectedCategories[$k]['points'] / count( $answeredQuestionsForOptions[$k] );
                }

                # $allAffectedCategories[$k]['points'] =
                #    (( empty( $allAffectedCategories[$k]['count'] ) or count( $answeredQuestionsForOptions[$k] ) === 0 ) ? 0 : ( $allAffectedCategories[$k]['points'] / count( $answeredQuestionsForOptions[$k] )));

            }
        }

        if ($evaluationType === 'lmi') {

            $total = 0;

            foreach ($allAffectedCategories as $k => $v) {
                $lmiRank = $db->fetchByAssoc($db->limitQuery("select sw, pr from sup_lmi_skalen where category = '{$v['abbreviation']}' and rw <= '{$v['points']}' order by rw desc", 0, 1));
                // if ( $dummy === false ) $allAffectedCategories[$k]['points'] = 0;
                $allAffectedCategories[$k]['sw'] = $lmiRank['sw'];
                $allAffectedCategories[$k]['pr'] = $lmiRank['pr'];
                $total += (int)$v['points'];
            }

            $lmiRank = $db->fetchByAssoc($db->limitQuery("select sw, pr from sup_lmi_skalen where category = '*' and rw <= '$total' order by rw desc", 0, 1));

            $allAffectedCategories[] = array(
                'abbreviation' => 'Total',
                'name' => 'Total',
                'points' => $total,
                'sw' => $lmiRank['sw'],
                'pr' => $lmiRank['pr'],
                'sortkey' => 100,
                'isTotal' => true
            );

        }

        if ($evaluationType !== 'mbti') {

            // handle the sorting
            foreach ($allAffectedCategories as $affectedCategoryKey => $allAffectedCategoryData) {
                if (isset($allCategories[$allAffectedCategoryData['id']]['sortkey']))
                    $allAffectedCategories[$affectedCategoryKey]['sortkey'] = $allCategories[$allAffectedCategoryData['id']]['sortkey'];
            }

            if ($evaluationSorting === 'points desc') {
                usort($allAffectedCategories, function ($a, $b) {
                    return ($a['points'] > $b['points'] ? -1 : 1);
                });
            } elseif ($evaluationSorting === 'points asc') {
                usort($allAffectedCategories, function ($a, $b) {
                    return ($a['points'] < $b['points'] ? -1 : 1);
                });
            } elseif ($evaluationSorting === 'categories') {
                usort($allAffectedCategories, function ($a, $b) {
                    if ($a['sortkey'] && $b['sortkey'])
                        return (int)$a['sortkey'] > (int)$b['sortkey'];
                    else
                        return strcasecmp($a['name'], $b['name']);
                });
            }

        } else {

            $mbtiOrder = array( 'E' => 0, 'I' => 1, 'S' => 2, 'N' => 3, 'T' => 4, 'F' => 5, 'J' => 6, 'P' => 7 );
            usort($allAffectedCategories, function ($a, $b) use ( $mbtiOrder ) {
                return $mbtiOrder[$a['abbreviation']] < $mbtiOrder[$b['abbreviation']] ? -1 : 1;
            });

        }

        # workaround temporary
        foreach ( $allAffectedCategories as $k => $v ) {
            if ( !isset( $v['id'] )) unset( $allAffectedCategories[$k] );
        }

        // return the values
        return array($evaluationType, $questionnaireId, $allAffectedCategories);

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

    /**
     * getResults
     *
     * Get the results of a participation.
     *
     * @param $participationId
     * @return array
     */
    public function getResults( $participationId ) {
        $db = DBManagerFactory::getInstance();

        $questionnaireId = $db->getOne( sprintf( 'SELECT questionnaire_id FROM questionnaireparticipations qp WHERE qp.deleted <> 1 AND qp.id = "%s"', $db->quote( $participationId )));

        if ( empty( $questionnaireId )) return [ 'participation' => false ];

        $questionnaire = BeanFactory::getBean('Questionnaires');
        $questionnaire->retrieve( $questionnaireId );
        if ( empty( $questionnaire->id ))
            throw ( new Exception('Linked Questionnaire ('.$questionnaireId.') not found.'))->setHttpCode(500);

        $responseData = array(
            'participation' => true,
            'textbefore' => $questionnaire->textbefore,
            'textafter' => $questionnaire->textafter,
            'fillCompletely' => (bool)$questionnaire->fill_completely,
            'questionsets' => array()
        );

        $questionsets = $questionnaire->get_linked_beans( 'questionsets', 'QuestionSets' );

        // sort by position
        usort( $questionsets, function( $a, $b ) { return $a->position - $b->position; });

        foreach ( $questionsets as $questionset ) {

            $participation = BeanFactory::getBean( 'QuestionSetParticipations' );
            $participation->retrieve_by_string_fields( array( 'questionnaireparticipation_id' => $participationId, 'questionset_id' => $questionset->id ), false );

            $responseData['questionsets'][] = array(
                'id' => $questionset->id,
                'name' => $questionset->name,
                'participationId' => $participation->id,
                'textbefore' => $questionset->textbefore,
                'textafter' => $questionset->textafter
            );

        }

        return $responseData;

    }

    public function getParticipationId_byReference( $referenceType, $referenceId ) {
        $db = DBManagerFactory::getInstance();
        return $db->getOne( sprintf( 'SELECT id FROM questionnaireparticipations qp WHERE qp.deleted <> 1 AND qp.parent_type = "%s" AND qp.parent_id = "%s"', $db->quote( $referenceType ), $db->quote( $referenceId )));
    }

    public function getReference_byParticipation( $participationId ) {
        $db = DBManagerFactory::getInstance();
        $row = $db->fetchOne( sprintf( 'SELECT parentType, parentId FROM questionnaireparticipations WHERE deleted <> 1 AND id = "%s"', $db->quote( $participationId )));
        if ( $row === false ) return [ false, false ];
        else return [ $row['parentType'], $row['parentId'] ];
    }

    public function getQuestionnaireId( $questionnaireParticipationId ) {
        $db = DBManagerFactory::getInstance();
        $questionnaireId = $db->getOne( sprintf('SELECT qp.questionnaire_id FROM questionnaireparticipations qp WHERE qp.deleted = 0 AND qp.id = "%s"', $db->quote( $questionnaireParticipationId )));
        return $questionnaireId;
    }

    public function getInterpretations( $referenceType, $referenceId ) {
        $db = DBManagerFactory::getInstance();

        $assignedInterpretations = [];
        $dbResult  = $db->query( sprintf(
            'SELECT qi.* FROM supconsultingorderitems_questionnaireinterpretations sq'
            . ' INNER JOIN questionnaireinterpretations qi ON sq.questionnaireinterpretation_id = qi.id'
            . ' WHERE sq.deleted = 0 AND qi.deleted = 0 AND sq.supconsultingorderitem_id = "%s"', $referenceId
        ));
        while ( $dummy = $db->fetchByAssoc( $dbResult, false ) ) $assignedInterpretations[] = $dummy;

        return $assignedInterpretations;
    }

    public static function getAllQuestionSets( $participationId )
    {
        $db = DBManagerFactory::getInstance();
        $allQuestionSets = array();
        $dbResult = $db->query( sprintf('SELECT qs.*, qsp.starttime FROM questionsetparticipations qsp INNER JOIN questionsets qs ON qsp.questionset_id = qs.id WHERE qsp.deleted = 0 AND qs.deleted = 0 AND qsp.questionnaireparticipation_id = "%s" ORDER BY starttime DESC', $db->quote( $participationId )));
        if ($dbResult->num_rows === 0) return false; # no participation yet
        $firstRow = true;
        while ($dummy = $db->fetchByAssoc($dbResult, false)) {
            /*
            if ( $firstRow ) {
                $firstRow = false;
                #var_dump($dummy);
                if ( !isset( $dummy['starttime'][0] )) return false; # the participation has not taken place
            }
            */
            $allQuestionSets[$dummy['id']] = $dummy;
        }
        return $allQuestionSets;
    }

    public static function getAllQuestions( $participationId )
    {
        $db = DBManagerFactory::getInstance();
        $allQuestions = array();
        $dbResult = $db->query(sprintf('SELECT qs.id as questionset_id, q.* FROM questionsetparticipations qsp INNER JOIN questionsets qs ON qsp.questionset_id = qs.id INNER JOIN questions q ON q.questionset_id = qs.id WHERE qsp.deleted = 0 AND qs.deleted = 0 AND q.deleted = 0 AND qsp.questionnaireparticipation_id = "%s"', $db->quote( $participationId )));
        while ($dummy = $db->fetchByAssoc($dbResult, false))
            $allQuestions[$dummy['questionset_id']][] = $dummy;
        return $allQuestions;
    }

    public static function getAllQuestionOptions( $participationId )
    {
        $db = DBManagerFactory::getInstance();
        $allQuestionOptions = array();
        $dbResult = $db->query(sprintf('SELECT q.id as question_id, qo.* FROM questionsetparticipations qsp INNER JOIN questionsets qs ON qsp.questionset_id = qs.id INNER JOIN questions q ON qs.id = q.questionset_id INNER JOIN questionoptions qo ON qo.question_id = q.id WHERE qsp.deleted = 0 AND qs.deleted = 0 AND q.deleted = 0 AND qo.deleted = 0 AND qsp.questionnaireparticipation_id = "%s"', $db->quote( $participationId )));
        while ($dummy = $db->fetchByAssoc($dbResult, false))
            $allQuestionOptions[$dummy['question_id']][$dummy['id']] = $dummy;
        return $allQuestionOptions;
    }

    public function getInterpretationsSuggested( $participationId ) {
        $db = DBManagerFactory::getInstance();

        #$questionnaire = \SpiceCRM\data\BeanFactory::getBean('Questionnaires');
        list( $evaluationType, $questionnaireId, $allAffectedCategories ) = self::getEvaluation( $participationId );

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

                $dbResult  = $db->query( sprintf('SELECT * FROM questionnaireinterpretations qi WHERE deleted = 0 AND qi.questionnaire_id = "%s"', $questionnaireId ));
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
                    $selectedCategories = array_merge( $selectedCategories, array_slice( $allAffectedCategories, 0, $topX, true ));

                if ( $bottomX !== false )
                    $selectedCategories = array_merge( $selectedCategories, array_slice( $allAffectedCategories, $bottomX * -1, $bottomX, true ));

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

        return $suggestedInterpretations;
    }

    public function mark_deleted( $dummy ) {
        $questionsetParticipations = $this->get_linked_beans('questionsetparticipations', 'QuestionSetParticipation' );
        foreach ( $questionsetParticipations as $qsp ) $qsp->mark_deleted( $qsp->id );
        return parent::mark_deleted( $this->id );
    }

    function ACLAccess( $view, $is_owner = 'not_set') {
        if ( $view == 'edit' ) return false; // it is generally not allowed to change questionnaire participation data
        return parent::ACLAccess( $view, $is_owner );
    }

}
