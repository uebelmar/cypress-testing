<?php

namespace SpiceCRM\includes\StarFaceVOIP;

use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

class StarFaceVOIPConnector{

    var $ssl_verifyhost = false;
    var $ssl_verifypeer = false;

    var $username;
    var $userpass;

    public function __construct()
    {
        $prefs = $this->getPreferences();
        $this->username = $prefs['username'];
        $this->userpass = $prefs['userpass'];

    }

    /**
     * calls the pbx
     *
     * @param $body
     * @return bool|string
     */
    private function makeCall($body){
        

        $params = [
            'de.vertico.starface.callback.type' => SpiceConfig::getInstance()->config['starface']['callback_type'],
            'de.vertico.starface.callback.host' => SpiceConfig::getInstance()->config['starface']['callback_host'],
            'de.vertico.starface.callback.port' => SpiceConfig::getInstance()->config['starface']['callback_port'],
            'de.vertico.starface.callback.path' => SpiceConfig::getInstance()->config['starface']['callback_path']
        ];

        $cURL = 'https://' . SpiceConfig::getInstance()->config['starface']['host'].'/xml-rpc?de.vertico.starface.auth='.$this->buildAuth();
        if(count($params) > 0){
           foreach($params as $paramname => $paramvalue) {
               $cURL .= '&' . $paramname . '=' . $paramvalue;
           }
        }
        $ch = curl_init($cURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->ssl_verifyhost);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/xml',
                'Content-Length: ' . strlen($body))
        );
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * do a login
     *
     * @return bool
     */
    public function login(){
        $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.connection.login</methodName></methodCall>";
        $result = $this->makeCall($body);

        // if we had a curl error return false
        if(!$result) return false;

        // format the result to an object
        $resArray = json_decode(json_encode((array) simplexml_load_string($result)));

        // return if we had success or not - return true if the boolean value is '1'
        return $resArray->params->param->value->boolean == '1';
    }

    /**
     * do a login
     *
     * @return bool
     */
    public function keepAlive(){
        $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.connection.keepAlive</methodName></methodCall>";
        $result = $this->makeCall($body);

        // if we had a curl error return false
        if(!$result) return false;

        // format the result to an object
        $resArray = json_decode(json_encode((array) simplexml_load_string($result)));

        // return if we had success or not - return true if the boolean value is '1'
        return $resArray->params->param->value->boolean == '1';
    }

    /**
     * initiate a call
     *
     * @param $msisdn
     * @return bool
     */
    public function initiateCall($msisdn){
        if($this->login()) {
            $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.call.placeCallWithPhone</methodName><params><param><value><string>$msisdn</string></value></param><param><value><string></string></value></param><param><value><string></string></value></param></params></methodCall>";
            $result = $this->makeCall($body);

            // if we had a curl error return false
            if (!$result) return false;

            // format the result to an object
            $resArray = json_decode(json_encode((array)simplexml_load_string($result)));

            // return if we had success or not - return true if the boolean value is '1'
            return $resArray->params->param->value->string;
        }

        return false;
    }

    /**
     * initiate a call
     *
     * @param $msisdn
     * @return bool
     */
    public function hangupcall($callid){
        if($this->login()) {
            $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.call.hangupCall</methodName><params><param><value><string>$callid</string></value></param></params></methodCall>";
            $result = $this->makeCall($body);

            // if we had a curl error return false
            if(!$result) return false;

            // format the result to an object
            $resArray = json_decode(json_encode((array) simplexml_load_string($result)));

            // return if we had success or not - return true if the boolean value is '1'
            return $resArray->params->param->value->boolean == '1';
        }

        return false;
    }

    /**
     * initiate a call
     *
     * @param $msisdn
     * @return bool
     */
    public function subscribeEvents(){
        if($this->login()) {


            $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.service.subscribeEvents</methodName><params><param><value><string>ucp.v22.events.call</string></value></param></params></methodCall>";
            $result = $this->makeCall($body);

            // if we had a curl error return false
            if(!$result) return false;

            // format the result to an object
            $resArray = json_decode(json_encode((array) simplexml_load_string($result)));

            // return if we had success or not - return true if the boolean value is '1'
            return $resArray->params->param->value->boolean == '1';
        }

        return false;
    }

    /**
     * do a login
     *
     * @return bool
     */
    public function unsubscribeEvents(){
        $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.service.unsubscribeEvents</methodName><params><param><value><string>ucp.v22.events.call</string></value></param></params></methodCall>";
        $result = $this->makeCall($body);

        // if we had a curl error return false
        if(!$result) return false;

        // format the result to an object
        $resArray = json_decode(json_encode((array) simplexml_load_string($result)));

        // return if we had success or not - return true if the boolean value is '1'
        return $resArray->params->param->value->boolean == '1';
    }

    /**
     * fethces the starface user preferences for the current user
     *
     * @return mixed
     */
    private function getPreferences(){
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $prefs = $current_user->getPreference('starface', 'starface');
        return $prefs ?: [];
    }

    /**
     * builds the auth string for the request
     *
     * @return string
     */
    private function buildAuth(){
        return $this->username . ':' . hash('sha512', $this->username . '*'. $this->userpass);
    }

}
