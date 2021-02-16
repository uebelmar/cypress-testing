<?php
namespace SpiceCRM\modules\GoogleOAuth;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/**
 * Class GoogleOAuthImpersonation handles the impoersonation requests if a global service account is defined for GSuite and thus the access to Calendar is granted
 *
 * @package SpiceCRM\modules\GoogleOAuth
 */
class GoogleOAuthImpersonation
{

    /**
     * helper function
     *
     * @param $data
     * @return string
     */
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * requests the token
     *
     * @return mixed
     */
    function getToken($userid = null)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        if($userid && $userid != $current_user->id){
            $impersonationuser = BeanFactory::getBean('Users', $userid);
        } else {
            $impersonationuser = $current_user;
        }

        $apiUrl = "https://www.googleapis.com/oauth2/v4/token";
        $params = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->createJWTAssertion($impersonationuser->user_name)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $apiUrl,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params
        ]);

        $response = json_decode(curl_exec($curl));

        if (isset($response->error)) {
            $result['error'] = $response->error . ': ' . $response->error_description;
            $result['result'] = false;
            return $result;
        }

        curl_close($curl);

        return $response;
    }

    /**
     * requests the token with a given username
     *
     * @return mixed
     */
    function getTokenByUserName($username)
    {

        $apiUrl = "https://www.googleapis.com/oauth2/v4/token";
        $params = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->createJWTAssertion($username)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $apiUrl,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params
        ]);

        $response = json_decode(curl_exec($curl));

        if (isset($response->error)) {
            $result['error'] = $response->error . ': ' . $response->error_description;
            $result['result'] = false;
            return $result;
        }

        curl_close($curl);

        return $response;
    }



    /**
     * creates the JWT Assertion from the json string
     *
     * @return string
     */
    private function createJWTAssertion($username, $scope = ['https://www.googleapis.com/auth/calendar'])
    {


        $serviceuserkey = SpiceConfig::getInstance()->config['googleapi']['serviceuserkey'];
        $serviceuserdetails = json_decode($serviceuserkey);
        $private_key = $serviceuserdetails->{'private_key'};

        //{Base64url encoded JSON header}
        $jwtHeader = $this->base64url_encode(json_encode(array(
            "alg" => "RS256",
            "typ" => "JWT"
        )));

        //{Base64url encoded JSON claim set}
        $now = time();
        $jwtClaim = $this->base64url_encode(json_encode(array(
            "iss" => $serviceuserdetails->{'client_email'},
            "scope" => SpiceConfig::getInstance()->config['googleapi']['serviceuserscope'],
            "aud" => "https://www.googleapis.com/oauth2/v4/token",
            "exp" => $now + 3600,
            "iat" => $now,
            "sub" => $username
        )));

        $data = $jwtHeader . "." . $jwtClaim;

        // Signature
        $Sig = '';
        openssl_sign($data, $Sig, $private_key, 'SHA256');
        $jwtSign = $this->base64url_encode($Sig);

        $jwtAssertion = $data . "." . $jwtSign;

        return $jwtAssertion;
    }
}
