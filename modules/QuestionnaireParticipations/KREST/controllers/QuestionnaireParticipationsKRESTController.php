<?php
namespace SpiceCRM\modules\QuestionnaireParticipations\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\QuestionnaireParticipations\QuestionnaireParticipation;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;

class QuestionnaireParticipationsKRESTController
{

    public function checkExistenceOfReferenceBean( $referenceType, $referenceId ) {
        $referenceBean = BeanFactory::getBean( $referenceType );
        $referenceBean->retrieve( $referenceId, false, null, false );
        if ( empty( $referenceBean->id ))
            throw ( new NotFoundException( '"'.$referenceType.'" not found.'))->setLookedFor( ['id' => $referenceId, 'module'=> $referenceType ] );
    }

    public function getParticipationId_byReference( $referenceType, $referenceId ) {
        $participationId = QuestionnaireParticipation::getParticipationId_byReference( $referenceType, $referenceId );
        if ( empty( $participationId )) {
            throw ( new NotFoundException( 'QuestionnaireParticipation attached to reference '.$referenceType.'/'.$referenceId.' not found.'));
        }
        return $participationId;
    }

    public function getReference_byParticipation( $participationId ) {
        list( $referenceType, $referenceId ) = QuestionnaireParticipation::getReference_byParticipation( $participationId );
        if ( empty( $referenceId )) {
            throw ( new NotFoundException( 'Reference '.$referenceType.'/'.$referenceId.' attached to QuestionnaireParticipation not found.'));
        }
        return [ $referenceType, $referenceId ];
    }

    public function getResults_byReference( $req, $res, $args ) {
        self::checkExistenceOfReferenceBean( $args['referenceType'], $args['referenceId'] );
        $participationId = self::getParticipationId_byReference( $args['referenceType'], $args['referenceId'] );
        // todo: access restriction necessary?
        return $res->withJson( QuestionnaireParticipation::getResults( $participationId ));
    }

    public function getResults( $req, $res, $args ) {

        // get the reference bean
        $participationBean = BeanFactory::getBean('QuestionnaireParticipation');
        $participationBean->retrieve( $args['participationId'], false, null, false );
        if ( empty( $participationBean->id ))
            throw ( new NotFoundException( 'QuestionnaireParticipation not found.'))->setLookedFor( ['id' => $args['participationId'], 'module'=> 'QuestionnaireParticipation' ] );

        // todo: access restriction necessary?

        return $res->withJson( QuestionnaireParticipation::getResults( $args['participationId'] ) );

    }

    public function getEvaluation_byReference( $req, $res, $args ) {
        self::checkExistenceOfReferenceBean( $args['referenceType'], $args['referenceId'] );
        $participationId = self::getParticipationId_byReference( $args['referenceType'], $args['referenceId'] );
        // todo: access restriction necessary?
        if ( $evaluation = QuestionnaireParticipation::getEvaluation( $participationId )) {
            list( $evaluationType, $questionnaireId, $allAffectedCategories ) = $evaluation;
            return $res->withJson( [ 'participated' => true, 'evaluationType' => $evaluationType ?: 'none', 'values' => $allAffectedCategories ] );
        } else
            return $res->withJson( [ 'participated' => false, 'evaluationType' => null, 'values' => null ] );
    }

    public function getEvaluation( $req, $res, $args ) {
        // todo: access restriction necessary?
        if ( $evaluation = QuestionnaireParticipation::getEvaluation( $args['participationId'] )) {
            list( $evaluationType, $questionnaireId, $allAffectedCategories ) = $evaluation;
            return $res->withJson( [ 'participated' => true, 'evaluationType' => $evaluationType ?: 'none', 'values' => $allAffectedCategories ] );
        } else
            return $res->withJson( [ 'participated' => false, 'evaluationType' => null, 'values' => null ] );
    }

    public function getQuestionnaireId_byReference( $req, $res, $args ) {
        self::checkExistenceOfReferenceBean( $args['referenceType'], $args['referenceId'] );
        $participationId = self::getParticipationId_byReference( $args['referenceType'], $args['referenceId'] );
        return $res->withJson([ 'questionnaireId' => QuestionnaireParticipation::getQuestionnaireId( $participationId ) ]);
    }

    public function getQuestionnaireId( $req, $res, $args ) {
        return $res->withJson([ 'questionnaireId' => QuestionnaireParticipation::getQuestionnaireId( $args['questionnaireParticipationId'] ) ]);
    }

    public function getInterpretations_byParticipation( $req, $res, $args ) {
        # self::checkExistenceOfReferenceBean( $args['referenceType'], $args['referenceId'] ); # todo: change to checkExistenceOfParticipation
        list( $referenceType, $referenceId ) = self::getReference_byParticipation( $args['participationId'] );
        return $res->withJson( QuestionnaireParticipation::getInterpretations( $referenceType, $referenceId ));
    }

    public function getInterpretations($req, $res, $args) {
        return $res->withJson( QuestionnaireParticipation::getInterpretations( $args['referenceType'], $args['referenceId'] ));
    }

    public function getInterpretationsSuggested_byReference($req, $res, $args) {
        self::checkExistenceOfReferenceBean( $args['referenceType'], $args['referenceId'] );
        $participationId = self::getParticipationId_byReference( $args['referenceType'], $args['referenceId'] );
        return $res->withJson( QuestionnaireParticipation::getInterpretationsSuggested( $participationId ));
    }

    public function getInterpretationsSuggested($req, $res, $args) {
        return $res->withJson( QuestionnaireParticipation::getInterpretationsSuggested( $args['participationId'] ));
    }

}
