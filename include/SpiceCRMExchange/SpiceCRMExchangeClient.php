<?php
namespace SpiceCRM\includes\SpiceCRMExchange;

use Exception;
use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Type\ExchangeImpersonationType;
use \jamesiarmes\PhpEws\Type\ConnectingSIDType;
use RuntimeException;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\EwsConnectionException;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\MissingEwsCredentialsException;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class SpiceCRMExchangeClient
{
    /**
     * @var the ews Client
     */
    public $client;

    private $host;
    private $username;
    private $password;

    function __construct($impersonateuser = null) {
        

        $this->host = SpiceConfig::getInstance()->config['SpiceCRMExchange']['host'];// 'outlook.office365.com';
        $this->username = SpiceConfig::getInstance()->config['SpiceCRMExchange']['username'];// 'christian@spicecrm.onmicrosoft.com';
        $this->password = self::getEwsPassword();
        $version = Client::VERSION_2016;

        $this->checkConfiguration();

        // create a new client
        $this->client = new Client($this->host, $this->username, $this->password, $version);

        // mainly to set CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER to false
        // CURLOPT_SSL_VERIFYPEER is hard coded to true in vendor/jamesiarmes/php-ntlm/src/SoapClient.php and
        // without a non self-signed SSL certificate curl would return an error
        // further usual flags CURLOPT_CAINFO => '/path/to/certificate.pem or .crt'
        if(isset(SpiceConfig::getInstance()->config['SpiceCRMExchange']['curl_options'])){
            $this->client->setCurlOptions(SpiceConfig::getInstance()->config['SpiceCRMExchange']['curl_options']);
        }

        // impersonate the account
        if($impersonateuser && $impersonateuser != $this->username){
            $this->impersonateAccount($impersonateuser);
        }
    }

    /**
     * called to impersonate an account
     *
     * @param $impersonateuser
     */
    public function impersonateAccount($impersonateuser){
        $ei = new ExchangeImpersonationType();
        $sid = new ConnectingSIDType();
        $sid->PrincipalName = $impersonateuser;
        $ei->ConnectingSID = $sid;
        $this->client->setImpersonation($ei);
    }

    /**
     * Calls a method on the Client
     *
     * ToDo: add Logging
     *
     * @param $method the name of the Method
     * @param $request the request data
     * @return mixed
     * @throws EwsConnectionException
     */
    public function request($method, $request) {
        try {
            return $this->client->$method($request);
        } catch (RuntimeException $e) {
            if ($e->getCode() == 6) {
                throw new EwsConnectionException('EWS Error: ' . $e->getMessage(), 404);
            }

            throw $e;
        } catch (Exception $e) {
            if (substr($e->getMessage(), 0, 31) == "SOAP client returned status of ") {
                throw new EwsConnectionException('EWS Error: ' . $e->getMessage(), $e->getCode());
            }

            throw $e;
        }
    }

    /**
     * Checks if a configuration for EWS exists.
     * It does NOT check if a connection can be established.
     *
     * @throws MissingEwsCredentialsException
     */
    public function checkConfiguration() {
        

        $errors = [];
        if (!isset($this->host) || $this->host == '') {
            $errors[] = 'Missing EWS Host';
        }
        if (!isset($this->username) || $this->username == '') {
            $errors[] = 'Missing EWS Username';
        }
        if (!isset($this->password) || $this->password == '') {
            $errors[] = 'Missing EWS Password';
        }

        if (!empty($errors)) {
            throw new MissingEwsCredentialsException(implode(', ', $errors),403);
        }
    }

    /**
     * Returns the Password for the Exchange server.
     * As default it is stored in the \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config global variable.
     * A custom password fetcher should be placed in the following static method:
     * \SpiceCRM\custom\includes\SpiceCRMExchange\PasswordHandler::getEwsPassword()
     *
     * @return mixed
     */
    private static function getEwsPassword() {
        

        $handlerClass = '\SpiceCRM\custom\\includes\\SpiceCRMExchange\\PasswordHandler';

        if (class_exists($handlerClass)) {
            return $handlerClass::getEwsPassword();
        }

        return SpiceConfig::getInstance()->config['SpiceCRMExchange']['password'];// '7*-t;}KRt:A8p\'q$';
    }
}
