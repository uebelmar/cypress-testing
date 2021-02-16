<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\Logger;
/**
 * Generic logger
 * @api
 */
interface LoggerTemplate
{
    /**
     * Main method for handling logging a message to the logger
     *
     * @param $method
     * @param string $message
     * @param array $logparams
     */
    public function log(
        $method,
        $message,
        $logparams = array()
        );
}
