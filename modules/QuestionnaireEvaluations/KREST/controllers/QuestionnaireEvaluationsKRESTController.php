<?php
namespace SpiceCRM\modules\QuestionnaireEvaluations\KREST\controllers;

use SpiceCRM\modules\Questionnaires\Questionnaire;
use SpiceCRM\modules\QuestionnaireEvaluations\QuestionnaireEvaluation;

class QuestionnaireEvaluationsKRESTController
{

    public function getEvaluationValues( $req, $res, $args )
    {
        return $res->withJson( QuestionnaireEvaluation::getEvaluationValues( $args['referenceModule'], $args['referenceId'] ));
    }

    public function generateEvaluation( $req, $res, $args )
    {
        return $res->withJson( QuestionnaireEvaluation::generateEvaluation( $args['referenceModule'], $args['referenceId'], $req->getParsedBodyParam('force') ));
    }

}
