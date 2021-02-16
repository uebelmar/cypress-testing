<?php
namespace SpiceCRM\modules\QuestionSets\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\includes\authentication\AuthenticationController;

// require_once( 'KREST/handlers/ModuleHandler.php' );

class QuestionSetsKRESTController
{
    public function getAnswerValues($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $responseData = [];

        // determine question type
        if (( $questionType = $db->getOne(sprintf('SELECT questiontype FROM questionsets WHERE id = "%s" AND deleted = 0', $db->quote( $args['questionsetId'] )))) === false )
            throw ( new NotFoundException('QuestionSet not found.'))->setLookedFor(['id'=>$args['questionsetId'],'module'=>'QuestionSets']);

        if (( $participation = $db->fetchOne(sprintf('SELECT *, UNIX_TIMESTAMP(starttime) AS starttime_unix FROM questionsetparticipations WHERE id = "%s" AND deleted = 0', $db->quote( $args['participationId'] )))) === false )
            throw ( new NotFoundException('QuestionSetParticipation not found.'))->setLookedFor(['id'=>$args['participationId'],'module'=>'QuestionSetParticipations']);

        $allAnswers = [];
        if (( $dbResult = $db->query(sprintf('SELECT q.id AS question_id, a.id AS answer_id, a.questionoption_id, a.optionlessAnswerValue FROM questionanswers a LEFT JOIN questions q ON a.question_id = q.id WHERE a.deleted = 0 AND q.deleted = 0 AND q.questionset_id = "%s" AND a.questionsetparticipation_id = "%s"', $db->quote( $args['questionsetId'] ), $db->quote( $args['participationId'] )))) !== false )
            while ($dummy = $db->fetchByAssoc($dbResult)) $allAnswers[$dummy['question_id']][] = $dummy;

        if ( $questionType === 'text' or $questionType === 'nps' )
            foreach ($allAnswers as $v)
                $responseData[$v[0]['question_id']]['optionlessAnswerValue'] = $v[0]['optionlessAnswerValue'];
        else // question type is 'single', 'multi', 'binary', 'rating' or 'ist'
            foreach ($allAnswers as $a)
                foreach ( $a as $o )
                    $responseData[$o['question_id']][$o['questionoption_id']] = ( $questionType === 'ist' ? $o['optionlessAnswerValue'] : true );

        return $res->withJson( $responseData, 200, JSON_FORCE_OBJECT );
    }

    public function renderer($req, $res, $args) {
        $db = DBManagerFactory::getInstance();
        $questionset = BeanFactory::getBean('QuestionSets', $args['questionsetId'] );
        if ( empty( $questionset->id ))
            throw ( new NotFoundException('QuestionSet not found.'))->setLookedFor(['id'=>$args['questionsetId'],'module'=>'QuestionSets']);

//        $beanHandler = new \SpiceCRMKREST\handlers\ModuleHandler($app);
        $beanHandler = new ModuleHandler();
        $beanData = $beanHandler->mapBeanToArray('QuestionSets', $questionset, [], false, false, false);

        // add the questions
        $beanData['questions']['beans'] = [];
        $questions = $db->query( sprintf('SELECT * FROM questions WHERE questionset_id = "%s" AND deleted = 0', $db->quote( $questionset->id )));
        while ($question = $db->fetchByAssoc( $questions )) {
            $beanData['questions']['beans'][$question['id']] = [
                'id'                => $question['id'],
                'name'              => $question['name'],
                'summary_text'      => $question['name'],
                'questionparameter' => $question['questionparameter'],
                'position'          => $question['position'],
                'questionset_id'    => $question['questionset_id'],
                'image_id'          => $question['image_id'],
                'position'          => $question['position'],
                'date_entered'      => $question['date_entered'],
                'showonlyimages'    => $question['showonlyimages']
            ];

            $questionoptions = $db->query("SELECT * FROM questionoptions WHERE question_id='{$question['id']}' AND deleted = 0");
            while ($questionoption = $db->fetchByAssoc($questionoptions)) {
                $beanData['questions']['beans'][$question['id']]['questionoptions']['beans'][$questionoption['id']] = [
                    'id'                            => $questionoption['id'],
                    'name'                          => $questionoption['name'],
                    'text'                          => $questionoption['text'],
                    'description'                   => $questionoption['description'],
                    'questionset_type_parameter_id' => $questionoption['questionset_type_parameter_id'],
                    'image_id'                      => $questionoption['image_id'],
                    'position'                      => $questionoption['position']
                ];
            }
        }

        return $res->withJson( $beanData );
    }
}
