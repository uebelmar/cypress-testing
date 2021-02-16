<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\Five9VOIP;


use SpiceCRM\includes\authentication\AuthenticationController;

class Five9VOIPConnector
{

    var $ssl_verifyhost = false;
    var $ssl_verifypeer = false;

    var $username;
    var $userpass;

    var $tokenId;
    var $farmId;
    var $userId;

    var $url = 'https://app.five9.com/appsvcs/rs/svc';

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
    private function makeCall($route, $body)
    {
        $cURL = 'https://app.five9.com/appsvcs/rs/svc/' . $route;
        $ch = curl_init($cURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->ssl_verifyhost);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($body))
        ];
        if($this->tokenId){
            $headers[] = "Authorization: Bearer-{$this->tokenId}";
            $headers[] = "farmId: {$this->farmId}";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * do a login
     *
     * @return bool
     */
    public function login()
    {

        $result = $this->makeCall('auth/login', ['passwordCredentials' => ['username' => $this->username, 'password' => $this->userpass], 'policy' => 'AttachExisting']);

        // if we had a curl error return false
        if (!$result) return false;

        // format the result to an object
        $resObject = json_decode($result);

        // return if we had success or not - return true if the boolean value is '1'
        if (!empty($resObject->tokenId)) {
            $this->farmId = $resObject->context->farmId;
            $this->tokenId = $resObject->tokenId;
            $this->userId = $resObject->userId;
        }

        return !empty($resObject->tokenId);
    }

    /**
     * do a login
     *
     * @return bool
     */
    public function keepAlive()
    {
        $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.connection.keepAlive</methodName></methodCall>";
        $result = $this->makeCall($body);

        // if we had a curl error return false
        if (!$result) return false;

        // format the result to an object
        $resArray = json_decode(json_encode((array)simplexml_load_string($result)));

        // return if we had success or not - return true if the boolean value is '1'
        return $resArray->params->param->value->boolean == '1';
    }

    /**
     * initiate a call
     *
     * @param $msisdn
     * @return bool
     */
    public function initiateCall($msisdn)
    {
        if ($this->login()) {
            $body = [
                "campaignId" => "1137588",
                "autoResolveDialingRules" => true,
                "skipDNCCheck" => true,
                "number" => $msisdn,
                "checkMultipleContacts" => false
            ];

            $result = $this->makeCall("agents/{$this->userId}/interactions/make_test_call", $body);

            // if we get an empty string back we assume th ecall was successful
            if ($result == '') return ['success' => true];

            // format the result to an object
            $resArray = json_decode($result);

            // return if we had success or not - return true if the boolean value is '1'
            return $resArray;
        }

        return false;
    }

    /**
     * initiate a call
     *
     * @param $msisdn
     * @return bool
     */
    public function hangupcall($callid)
    {
        if ($this->login()) {
            $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.call.hangupCall</methodName><params><param><value><string>$callid</string></value></param></params></methodCall>";
            $result = $this->makeCall($body);

            // if we had a curl error return false
            if (!$result) return false;

            // format the result to an object
            $resArray = json_decode(json_encode((array)simplexml_load_string($result)));

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
    public function subscribeEvents()
    {
        if ($this->login()) {


            $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.service.subscribeEvents</methodName><params><param><value><string>ucp.v22.events.call</string></value></param></params></methodCall>";
            $result = $this->makeCall($body);

            // if we had a curl error return false
            if (!$result) return false;

            // format the result to an object
            $resArray = json_decode(json_encode((array)simplexml_load_string($result)));

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
    public function unsubscribeEvents()
    {
        $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><methodCall><methodName>ucp.v22.requests.service.unsubscribeEvents</methodName><params><param><value><string>ucp.v22.events.call</string></value></param></params></methodCall>";
        $result = $this->makeCall($body);

        // if we had a curl error return false
        if (!$result) return false;

        // format the result to an object
        $resArray = json_decode(json_encode((array)simplexml_load_string($result)));

        // return if we had success or not - return true if the boolean value is '1'
        return $resArray->params->param->value->boolean == '1';
    }

    /**
     * fethces the starface user preferences for the current user
     *
     * @return mixed
     */
    private function getPreferences()
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $prefs = $current_user->getPreference('five9', 'five9');
        return $prefs ?: [];
    }

    /**
     * builds the auth string for the request
     *
     * @return string
     */
    private function buildAuth()
    {
        return $this->username . ':' . hash('sha512', $this->username . '*' . $this->userpass);
    }

}
