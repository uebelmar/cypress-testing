<?php
namespace SpiceCRM\modules\ServiceFeedbacks\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\ErrorHandlers\ConflictException;
use SpiceCRM\modules\QuestionnaireEvaluations\QuestionnaireEvaluation;

class ServiceFeedbacksKRESTController
{
    private $serviceFeedback;
    private $questionnaire;

    /**
     * saveAnswers
     *
     * Checks if there is a Questionnaire linked to the current ServiceFeedback and if so saves the answers to it.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function saveAnswers($req, $res, $args) {

        $this->initialize($args['identificationToken']);

        $postBody = $req->getParsedBody();

        $participationId = $this->questionnaire->saveAnswers( $postBody['questionsets'], $args['identificationToken'], 'ServiceFeedbacks' );

        QuestionnaireEvaluation::getEvaluationValues( 'ServiceFeedbacks', $args['identificationToken'] );

        return $res->withJson(['success' => true, 'participationId' => $participationId ]);
    }

    /**
     * getFullQuestionnaire
     *
     * Returns a json object of a Questionnaire including its QuestionSets and Questions.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function getFullQuestionnaire($req, $res, $args) {
        $this->initialize($args['identificationToken']);

        return $res->withJson($this->questionnaire->getQuestionnaireArray());
    }

    private function initialize($token) {
        $this->serviceFeedback = BeanFactory::getBean('ServiceFeedbacks', $token);

        if (!$this->serviceFeedback) {
            throw (new NotFoundException('ServiceFeedback not found'))->setLookedFor(['id' => $token, 'module' => 'ServiceFeedbacks']);
        }

        // check if we have a participation already
        $participations = $this->serviceFeedback->get_linked_beans('questionsetparticipations', "QuestionSetParticipation");
        if (count($participations) > 0) {
            throw (new ConflictException('Service Feedback already provided'));
        }


        $this->questionnaire = $this->serviceFeedback->getQuestionnaire();
    }
}
