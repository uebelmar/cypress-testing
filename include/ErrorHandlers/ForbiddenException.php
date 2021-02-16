<?php
namespace SpiceCRM\includes\ErrorHandlers;

use SpiceCRM\includes\authentication\AuthenticationController;

class ForbiddenException extends Exception {

    protected $isFatal = false;
    protected $httpCode = 403;

    function __construct( $message = null, $errorCode = null ) {
        if ( !isset( $message )) $this->lbl = 'ERR_HTTP_FORBIDDEN';
        parent::__construct( isset( $message ) ? $message : 'Forbidden', $errorCode );
    }

    protected function extendResponseData() {
        $this->responseData['currentUser'] = @AuthenticationController::getInstance()->getCurrentUser()->user_name;
    }

}
