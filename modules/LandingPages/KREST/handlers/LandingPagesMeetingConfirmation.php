<?php
namespace SpiceCRM\modules\LandingPages\KREST\handlers;

class LandingPagesMeetingConfirmation
{

    public function __construct() { }

    /**
     * Process and save the answer of the form of the landing page.
     *
     * @param $landingPage The landing page object.
     * @param $meeting The meeting object.
     * @param $args Arguments of the REST request.
     * @param $req The REST request.
     * @return array Answer for the frontend.
     */
    public function saveAnswer( $landingPage, $meeting, $args, $req ) {

        $parsedBody = $req->getParsedBody();
        if ( isset( $parsedBody['feedback'] )) $parsedBody['feedback'] = trim( $parsedBody['feedback'] );

        $validity = self::checkLandingPageValidity( $landingPage, $meeting );

        $doSave = false;
        if ( $validity === true ) {
            if ( $parsedBody['confirmation'] === 'yes' ) {
                $meeting->status = 'agreed';
                $doSave = true;
            } elseif ( $parsedBody['confirmation'] === 'no' ) {
                $meeting->status = empty( $parsedBody['feedback'] ) ? 'cancelled' : 'cancelled_with_reason';
                $doSave = true;
            }
            if ( !empty( $parsedBody['feedback'] )) {
                $meeting->feedback = $parsedBody['feedback'];
                $doSave = true;
            }
            if ( $doSave ) $meeting->save();
            return ['success' => true, 'html' => $landingPage->answer_content ];

        } else {

            return ['success' => false, 'message' => $validity ];

        }

    }

    /**
     * Check if the landing page might be displayed or the answer of the landing page might be processed.
     *
     * @param $landingPage The landing page object.
     * @param $meeting The meeting object.
     * @return bool|string True in case of validity or a string describing the error.
     */
    public function checkLandingPageValidity( $landingPage, $meeting ) {

        if ( $meeting->status === 'agreed' ) {
            return 'Der Termin wurde bereits bestätigt.';
        } elseif ( $meeting->status === 'cancelled' or $meeting->status === 'cancelled_with_reason' ) {
            return 'Der Termin wurde bereits abgelehnt.';
        } elseif ( $meeting->status !== 'notified' ) {
            return 'Der Termin steht nicht (mehr) zur Bestätigung an.';
        } else {
            $now = gmDate( 'Y-m-d H:i:s' );
            if ( $meeting->date_start < $now ) {
                return 'Eine Bestätigung/Ablehnung des Termins ist nicht mehr möglich.';
            }
        }

        # Displaying the landing page or processing the answer is OK.
        return true;

    }

}
