<?php
namespace SpiceCRM\includes\Alcatel;

use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

class AlcatelConnector
{
    private $ssl_verifyhost = false;
    private $ssl_verifypeer = false;

    public $phoneusername = '';
    public $username      = '';
    public $userpass      = '';
    private $cookieString = '';
    private $deviceId     = '';

    public function __construct() {
        $prefs = $this->getPreferences();
        $this->phoneusername = ($prefs['phoneusername'] ? $prefs['phoneusername'] : $prefs['username']);
        $this->username = $prefs['username'];
        $this->userpass = $prefs['userpass'];
    }

    /**
     * Fetches the Alcatel user preferences for the current user.
     *
     * @return mixed
     */
    private function getPreferences() {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $prefs = $current_user->getPreference('alcatel', 'alcatel');
        return $prefs ?: [];
    }

    /**
     * Logs into the Alcatel OpenTouch.
     *
     * @return bool
     */
    public function login() {
        
        $url =  'https://' . SpiceConfig::getInstance()->config['alcatel']['host'] . '/api/rest/authenticatenosso?version=1.0';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => false,
            CURLOPT_HEADER         => 1,
            CURLOPT_HTTPHEADER     => [
                'Content-Type:application/json',
                'Authorization: Basic ' . base64_encode($this->username . ':' . $this->userpass),
            ],
        ]);
        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 200) {
            $header_size = $info['header_size'];
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
            $result = json_decode($body);
            $headerLines = explode("\r\n", $header);

            $cookieArray = [];
            foreach ($headerLines as $headerLine) {
                $headerItem = explode(': ', $headerLine);
                if ($headerItem[0] == 'Set-Cookie') {
                    $cookieValues = explode('; ', $headerItem[1]);
                    $cookieArray[] = $cookieValues[0];
                }
            }
            $this->cookieString = implode('; ', $cookieArray);

            return $this->fetchSessions($result->publicUrl);
        } else {
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' response '.print_r($response, true));
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' info '.print_r($info, true));
        }
        return false;
    }

    /**
     * Logs into the node.js server.
     *
     * @return mixed
     */
    public function loginToNode() {
        
        $url = SpiceConfig::getInstance()->config['alcatel']['socketurl_backend'];

        $body = [
            'phoneusername'  => $this->phoneusername,
            'username'  => $this->username,
            'password'  => $this->userpass,
            'hostname'  => SpiceConfig::getInstance()->config['alcatel']['host'],
            'sessionId' => session_id(),
        ];
        $payload = json_encode($body);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type:application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        $result = json_decode($response);
        return $result;
    }

    /**
     * Fetches the sessions from the Alcatel OpenTouch.
     *
     * @param $publicUrl
     * @return bool
     */
    private function fetchSessions($publicUrl) {
        $body = [
            'applicationName' => 'SpiceCRM',
        ];
        $payload = json_encode($body);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $publicUrl,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Cookie: ' . $this->cookieString,
                'cache-control: no-cache',
                'Connection: keep-alive',
                'accept-encoding: gzip, deflate',
                'Accept: */*',
            ],
        ]);

        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 200) {
            return $this->fetchUserInfo();
        } else {
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' response '.print_r($response, true));
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' info '.print_r($info, true));
        }
        return false;
    }

    /**
     * Fetches the user info from Alcatel OpenTouch.
     *
     * @return bool
     */
    private function fetchUserInfo() {
        
        $url = 'https://' . SpiceConfig::getInstance()->config['alcatel']['host'] . '/api/rest/1.0/users/' . $this->phoneusername;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Cookie: ' . $this->cookieString,
                'cache-control: no-cache',
                'Connection: keep-alive',
                'accept-encoding: gzip, deflate',
                'Accept: */*',
            ],
        ]);
        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 200) {
            $result = json_decode($response);
            $this->deviceId = $result->companyPhone;
            return true;
        } else {
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' response '.print_r($response, true));
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' info '.print_r($info, true));
        }

        return false;
    }

    /**
     * Starts an outgoing call directly on the Alcatel OpenTouch.
     *
     * @param $msisdn
     * @return false|mixed|string|string[]
     * @throws Exception
     */
    public function initiateCall($msisdn) {
        
        if ($this->login()) {
            $url = 'https://' . SpiceConfig::getInstance()->config['alcatel']['host'] . '/api/rest/1.0/telephony/calls';
            $calleeNumber = $this->getDialableNumber($msisdn);

            $body = [
                'deviceId'       => $this->deviceId, // todo find out where to get that
                'callee'         => $calleeNumber,
                'autoAnswer'     => true,
                'anonymous'      => false,
                'bypass'         => false,
                'associatedData' => null,
                'pin'            => null,
                'secretCode'     => null,
                'businessCode'   => null,
            ];
            $payload = json_encode($body);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
                CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL            => $url,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HEADER         => 1,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type:application/json',
                    'Cookie: ' . $this->cookieString,
                ],
            ]);
            $response = curl_exec($curl);
            $errors = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);

            if ($info['http_code'] < 300 && $info['http_code'] >= 200) {
                return $this->extractCallId($response, $info);
            }
            throw new Exception($response, $info['http_code']);
        }

        return false;
    }

    /**
     * Extracts the call ID from the response header.
     *
     * @param $response
     * @param $info
     * @return mixed|string|string[]
     * @throws Exception
     */
    private function extractCallId($response, $info) {
        $needle = 'Location: ' . $info['url'] . '/';
        $resp = explode("\n", $response);
        foreach ($resp as $line) {
            if (strpos($line, $needle) === 0) {
                $id = str_replace("\r", '', str_replace($needle, '', $line));
                return $id;
            }
        }
        throw new Exception('Cannot find Call ID');
    }

    /**
     * Returns an outgoing number usable by alcatel.
     *
     * @param $number
     * @return false|mixed
     */
    public function getDialableNumber($number) {
        
        $queryParams = [
            'phoneNumber' => $number,
            'loginName'   => $this->username,
        ];

        $url = 'https://' . SpiceConfig::getInstance()->config['alcatel']['host'] . '/api/rest/1.0/telephony/numbering/dialable?'
            . http_build_query($queryParams);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Cookie: ' . $this->cookieString,
                'cache-control: no-cache',
                'Connection: keep-alive',
                'accept-encoding: gzip, deflate',
                'Accept: */*',
            ],
        ]);
        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 200) {
            $result = json_decode($response);
            return $result->dialable;
        } else {
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' response '.print_r($response, true));
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' info '.print_r($info, true));
        }

        return false;
    }

    /**
     * Returns the call details for a given call ID.
     *
     * @param $callId
     * @return false|mixed
     */
    public function fetchCallInfo($callId) {
        
        $queryParams = [
            'loginName'   => $this->username,
        ];

        $url = 'https://' . SpiceConfig::getInstance()->config['alcatel']['host'] . '/api/rest/1.0/telephony/calls/' . $callId . '?'
            . http_build_query($queryParams);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Cookie: ' . $this->cookieString,
                'cache-control: no-cache',
                'Connection: keep-alive',
                'accept-encoding: gzip, deflate',
                'Accept: */*',
            ],
        ]);
        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 200) {
            $result = json_decode($response);
            return $result;
        } else {
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' response '.print_r($response, true));
            LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' info '.print_r($info, true));
        }

        return false;
    }

    /**
     * Hangs up a call.
     * Actually the curl request always returns a 403, so this function doesn't really do anything useful.
     *
     * @param $callId
     * @return bool
     */
    public function hangupcall($callId) {
        
        $queryParams = [
            'loginName'   => $this->username,
        ];

        $url = 'https://' . SpiceConfig::getInstance()->config['alcatel']['host'] . '/api/rest/1.0/telephony/calls/' . $callId . '?'
            . http_build_query($queryParams);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_HTTPHEADER     => [
                'Content-Type:application/json',
                'Cookie: ' . $this->cookieString,
            ],
        ]);

        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 204) {
            return true;
        }

        return false;
    }
}
