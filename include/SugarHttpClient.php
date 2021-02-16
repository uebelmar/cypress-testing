<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\Logger\LoggerManager;


/**
 * Very basic HTTP client
 * @api
 * Used in various places of the code and can be mocked out.
 * Presently does only one op - POST to url.
 * If you need more complex stuff, use Zend_Http_Client
 */
class SugarHttpClient
{
    protected $last_error = '';
    /**
     * sends POST request to REST service via CURL
     * @param string $url URL to call
     * @param string $postArgs POST args
     */
    public function callRest($url, $postArgs)
    {
        if(!function_exists("curl_init")) {
            $this->last_error = 'ERROR_NO_CURL';
            LoggerManager::getLogger()->fatal("REST call failed - no cURL!");
            return false;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        LoggerManager::getLogger()->debug("HTTP client call: $url -> $postArgs");
        $response = curl_exec($curl);
        if($response === false) {
            $this->last_error = 'ERROR_REQUEST_FAILED';
            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            LoggerManager::getLogger()->error("HTTP client: cURL call failed: error $curl_errno: $curl_error");
            return false;
        }
        LoggerManager::getLogger()->debug("HTTP client response: $response");
        curl_close($curl);
        return $response;
    }

    /**
     * Returns code of last error that happened to the client
     * @return string
     */
    public function getLastError()
    {
        return $this->last_error;
    }
}
