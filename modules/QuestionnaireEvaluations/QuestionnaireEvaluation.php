<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\QuestionnaireEvaluations;

use SpiceCRM\modules\QuestionnaireParticipations\QuestionnaireParticipation;
use SpiceCRM\modules\QuestionOptionCategories\QuestionOptionCategory;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;

class QuestionnaireEvaluation extends SugarBean {

    public $table_name = "questionnaireevaluations";
    public $object_name = "QuestionnaireEvaluation";
    public $module_dir = 'QuestionnaireEvaluations';
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

    /**
     * generateEvaluation()
     * Generates questionnaire evaluation when missing or forced.
     *
     * @param $referenceModule
     * @param $referenceId
     * @param bool $forceGeneration
     * @return array
     */
    public static function generateEvaluation( $referenceModule, $referenceId, $forceGeneration = false )
    {
        $db = DBManagerFactory::getInstance();
        $response = [ 'values' => [] ];

        // Retrieve the reference record (ServiceFeedbacks)
        $reference = BeanFactory::getBean( $referenceModule, $referenceId );
        if ( $reference === false )
            throw (new NotFoundException('Reference record not found.'))->setLookedFor([ 'id' => $referenceId, 'module' => $referenceModule ]);

        $allCategories = QuestionOptionCategory::getAll();

        // A (new) evaluation has to be created, if ...
        // • there is no QuestionnaireEvaluation linked to the reference record, or
        // • the parameter $forceGeneration is true (an existing evaluation will get overwritten)
        if ( empty( $reference->questionnaireevaluation_id ) or $forceGeneration ) {
            $responseFromGetEvaluation = QuestionnaireParticipation::getEvaluation( QuestionnaireParticipation::getParticipationId_byReference( $referenceModule, $referenceId )); // Create the evaluation.
            if ( $responseFromGetEvaluation !== false ) { // "false" would mean that there is no participation yet.

                if ( !empty( $reference->questionnaireevaluation_id ) ) {
                    $response['source'] = 'generated/renewed';
                    $evaluation = BeanFactory::getBean( 'QuestionnaireEvaluations', $reference->questionnaireevaluation_id );
                    if ( $evaluation === false )
                        throw ( new Exception( 'Linked QuestionnaireEvaluation (' . $reference->questionnaireevaluation_id . ') not found.' ) );
                    $evaluation->load_relationship( 'questionnaireevaluationitems' );
                    $evaluation->questionnaireevaluationitems->delete( $evaluation->id );
                } else {
                    $response['source'] = 'generated';
                    $evaluation = BeanFactory::newBean( 'QuestionnaireEvaluations' );
                }
                $evaluation->save();

                // Write the values of the evaluation (QuestionnaireEvaluationItems) to the DB:
                $allAffectedCategories = $responseFromGetEvaluation[2];
                foreach ( $allAffectedCategories as $data ) {
                    $evaluationItem = BeanFactory::newBean( 'QuestionnaireEvaluationItems' );
                    $evaluationItem->name = $data['abbreviation'];
                    $evaluationItem->value = $data['points'];
                    $evaluationItem->questionnaireevaluation_id = $evaluation->id;
                    $evaluationItem->save();
                    $response['values'][$data['abbreviation']] = [
                        'name' => $allCategories[$data['abbreviation']]['name'],
                        'points' => $data['points'],
                    ];
                }
                $reference->questionnaireevaluation_id = $evaluation->id;
                $reference->save();
                $response['questionnaireEvaluationId'] = $reference->questionnaireevaluation_id;

            } else { // No participation yet.
                $response['source'] = 'noParticipation';
            }

        } else {

            // Retrieve the evaluation items from the DB:
            $evaluation = BeanFactory::getBean( 'QuestionnaireEvaluations', $reference->questionnaireevaluation_id );
            if ( $evaluation === false )
                throw (new Exception('Linked QuestionnaireEvaluation ('.$reference->questionnaireevaluation_id.') not found.'));
            $evaluationItems = $evaluation->get_linked_beans('questionnaireevaluationitems','QuestionnaireEvaluationItem');
            if ( $evaluationItems ) foreach( $evaluationItems as $item ) {
                $response['values'][$item->name] = [
                    'name' => $allCategories[$item->name]['name'],
                    'points' => $item->value*1,
                ];
            }
            $response['source'] = 'retrieved';
            $response['questionnaireEvaluationId'] = $reference->questionnaireevaluation_id;

        }

        return $response;
    }

    public static function getEvaluationValues( $referenceModule, $referenceId )
    {
        $response = [ 'values' => [] ];

        $reference = BeanFactory::getBean( $referenceModule, $referenceId );
        if ( $reference === false )
            throw (new NotFoundException('Reference record not found.'))->setLookedFor([ 'id' => $referenceId, 'module' => $referenceModule ]);

        $evaluation = BeanFactory::getBean( 'QuestionnaireEvaluations', $reference->questionnaireevaluation_id );
        if ( $evaluation === false )
            throw (new Exception('Linked QuestionnaireEvaluation ('.$reference->questionnaireevaluation_id.') not found.'));
        $allCategories = QuestionOptionCategory::getAll();

        $evaluationItems = $evaluation->get_linked_beans('questionnaireevaluationitems','QuestionnaireEvaluationItem');
        if ( $evaluationItems ) foreach( $evaluationItems as $item ) {
            $response['values'][$item->name] = [
                'name' => $allCategories[$item->name]['name'],
                'points' => $item->value*1,
            ];
        }
        $response['source'] = 'retrieved';
        $response['questionnaireEvaluationId'] = $reference->questionnaireevaluation_id;

        return $response;
    }

}
