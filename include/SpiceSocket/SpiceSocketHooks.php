<?php

namespace SpiceCRM\includes\SpiceSocket;

use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class SpiceSocketHooks
{
    private $ssl_verifyhost = false;
    private $ssl_verifypeer = false;

    public function updateSocket(&$bean)
    {

        if (SpiceConfig::getInstance()->config['core']['socket_id'] && SpiceConfig::getInstance()->config['core']['socket_backend']) {

            $body = ['sysid' => SpiceConfig::getInstance()->config['core']['socket_id'], 'room' => 'beanupdates', 'message' => ['i' => $bean->id, 'm' => $bean->module_dir, 's' => session_id()]];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
                CURLOPT_SSL_VERIFYHOST => $this->ssl_verifyhost,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => SpiceConfig::getInstance()->config['core']['socket_backend'],
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ]
            ]);

            $response = curl_exec($curl);
            $info = curl_getinfo($curl);

            LoggerManager::getLogger()->debug(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' url per POST '.print_r(SpiceConfig::getInstance()->config['core']['socket_backend'], true));
            LoggerManager::getLogger()->debug(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' response '.print_r($response, true));
            LoggerManager::getLogger()->debug(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' info '.print_r($info, true));

            if (!$response) {
                $error = curl_error($curl);
                LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' url per POST '.print_r(SpiceConfig::getInstance()->config['core']['socket_backend'], true));
                LoggerManager::getLogger()->fatal(__CLASS__.'::'.__FUNCTION__.' line '.__LINE__.' ERROR info '.print_r($info, true));
            }
            curl_close($curl);
        }

    }

}
