<?php
namespace SpiceCRM\includes\ErrorHandlers;

class UnauthorizedException extends Exception {

    protected $isFatal = false;
    protected $httpCode = 401;

    /**
     * UnauthorizedException constructor.
     * @param null $message
     * @param null $errorCode
     *
     * errorCodes:
     * 1= invalid username/password combination
     * 2= password expired
     * 3= user is blocked
     * 4= user is inactive
     * 5= user status is unknown
     * 6= no authentication infos
     * 7=invalid sessionid
     */
    function __construct( $message = null, $errorCode = null ) {
        if ( !isset( $message )) $this->lbl = 'ERR_HTTP_NO_AUTHORIZATION';
        parent::__construct( isset( $message ) ? $message : 'No Authorization', $errorCode );
    }

}
