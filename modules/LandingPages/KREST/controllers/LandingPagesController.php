<?php
namespace SpiceCRM\modules\LandingPages\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\SpiceTemplateCompiler\Compiler;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

class LandingPagesController
{

    private $landingPage; # The landing page bean.
    private $landingpageType = ''; # Type of the landing page ('html' or 'questionnaire').

    # In case the landing page type is 'html':
    private $relatedBean; # The related bean for the landing page.
    private $landingpageHandler; # The handler class for the landing page.

    # In case the landing page type is 'questionnaire':
    private $questionnaire; # Questionnaire bean for the landing page.
    private $serviceFeedback; # ServiceFeedback bean for the landing page.

    /**
     * Do initial tasks. Retrieve the landing page bean and depending on the landing page type some other necessary beans.
     * @param $args The arguments of the route.
     */
    public function init( $args ) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $current_user->retrieve('1');

        $this->landingPage = BeanFactory::getBean('LandingPages', $args['id'], ['encode' => false]);
        if ( $this->landingPage === false ) {
            throw ( new NotFoundException('LandingPage not found.'))
                ->setErrorCode('landingpageNotFound')
                ->setLookedFor(['id' => $args['id'], 'module' => 'LandingPages']);
        }

        $this->landingpageType = $this->landingPage->content_type;

        if ( $this->landingpageType === 'html' ) {

            $this->relatedBean = BeanFactory::getBean( $this->landingPage->module_name, $args['beanId'] );
            if ( $this->relatedBean === false ) {
                throw ( new NotFoundException($this->landingPage->module_name.' record not found.'))
                    ->setErrorCode('relatedRecordNotFound')
                    ->setLookedFor(['id' => $args['beanId'], 'module' => $this->landingPage->module_name]);
            }
            $this->landingpageHandler = 'SpiceCRM\modules\LandingPages\KREST\handlers\\' . $this->landingPage->handlerclass;

        } elseif ( $this->landingpageType === 'questionnaire' ) {

            $this->serviceFeedback = BeanFactory::getBean( 'ServiceFeedbacks', $args['beanId'] );
            if ( $this->serviceFeedback === false ) {
                throw ( new NotFoundException('ServiceFeedback record not found.'))
                    ->setErrorCode('relatedRecordNotFound')
                    ->setLookedFor(['id' => $args['beanId'], 'module' => 'ServiceFeedbacks']);
            }

            $dummy = $this->serviceFeedback->get_linked_beans('questionnaires','Questionnaire', []);
            if ( count( $dummy )) {
                $this->questionnaire = $dummy[0];
            } else {
                throw ( new Exception('No Questionnaire for Service Feedback '.$args['beanId'].'.'))->setErrorCode('noQuestionnaire');
            }

        }

    }

    /**
     * Deliver the content for the landing page.
     */
    public function getPageContent( $req, $res, $args ) {
        

        $this->init( $args );

        $error = false;
        $response = [ 'config' => SpiceConfig::getInstance()->config['landingpage'] ];

        if ( $this->landingpageType === 'html' ) {

            // Check if the landing page may be delivered:
            $errorText = ( $this->landingpageHandler )::checkLandingPageValidity( $this->landingPage, $this->relatedBean );
            if ( $errorText !== true ) {
                $response['html'] = $errorText;
                $response['error'] = true;
            } else {
                $templateCompiler = new Compiler();
                $response['html'] = $templateCompiler->compile( $this->landingPage->content, $this->relatedBean );
            }

        } elseif ( $this->landingpageType === 'questionnaire' ) {

            $errorText = $this->checkQuestionnaireValidity();
            if ( $errorText !== true ) {
                $response['html'] = $errorText;
                $response['error'] = true;
            } else {
                $this->questionnaire->textbefore = html_entity_decode( $this->questionnaire->textbefore );
                $this->questionnaire->textafter = html_entity_decode( $this->questionnaire->textafter );
                $response['questionnaire'] = $this->questionnaire->getQuestionnaireArray();
            }

        }

        return $res->withJson( $response );
    }

    /**
     * Save the answer given on the landing page.
     */
    public function saveAnswer( $req, $res, $args ) {

        $this->init( $args );

        if ( $this->landingpageType === 'html' ) {

            return $res->withJson(
                ( $this->landingpageHandler )::saveAnswer( $this->landingPage, $this->relatedBean, $args, $req )
            );

        } elseif ( $this->landingpageType === 'questionnaire' ) {

            $postBody = $req->getParsedBody();

            $participationId = $this->questionnaire->saveAnswers( $postBody['questionsets'], $args['beanId'], 'ServiceFeedbacks' );

            $this->serviceFeedback->servicefeedback_status = 'completed';
            $this->serviceFeedback->save();

            return $res->withJson(['success' => true, 'participationId' => $participationId, 'html' => html_entity_decode( $this->landingPage->answer_content ) ]);

        }

    }

    /**
     * Check if the questionnaire might be displayed or the answers of the landing page might be stored.
     * Returns true or - in case the questionnaire has already got answered - an error message.
     *
     * @param $landingPage The landing page bean.
     * @param $serviceFeedback The ServiceFeedback bean.
     * @return bool|string True in case of validity or a string describing the error.
     */
    public function checkQuestionnaireValidity() {

        $participation = BeanFactory::getBean('QuestionnaireParticipations');
        $participation->retrieve_by_string_fields([ 'parent_type' => 'ServiceFeedbacks', 'parent_id' => $this->serviceFeedback->id ]);
        if ( !empty( $participation->id )) return 'Der Fragebogen wurde bereits ausgefüllt.';

        # Displaying the landing page or processing the answer is OK.
        return true;

    }

}
