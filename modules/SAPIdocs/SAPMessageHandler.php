<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/***** SPICE-HEADER-SPACEHOLDER *****/
class SAPMessageHandler {

    const custom_functions_path = "modules/SAPIdocs/custom_functions/custom_message_functions";

    private $sapidoc = null;
    private $namespaces = array(
        'soap-env' => 'http://schemas.xmlsoap.org/soap/envelope/',
        'urn' => 'urn:sap-com:document:sap:soap:functions:mc-style',
        'n0' => 'urn:sap-com:document:sap:soap:functions:mc-style'
    );
    private $root_node = "//soap-env:Envelope/soap-env:Body/n0:Z01bcIdocStatusResponse";

    public function __construct() {
        
    }

    private function registerNS(DOMXPath $path) {
        foreach ($this->namespaces as $ns => $url) {
            $path->registerNamespace($ns, $url);
        }
    }

    private function saveMessage(DOMXPath $path, DOMNode $node) {
        global $timedate;
$db = DBManagerFactory::getInstance();

        $message_type = $path->query("Type", $node)->item(0)->nodeValue;
        $message_id = $path->query("Id", $node)->item(0)->nodeValue;
        $message_number = $path->query("Number", $node)->item(0)->nodeValue;
        $message = implode(array_map([$node->ownerDocument, "saveHTML"], iterator_to_array($node->childNodes)));
        $setup = $this->loadMessageSetup($message_number, $message_type, $message_id);
        if ($setup) {
            $id = create_guid();
            $sql = "SELECT id FROM sapidocreceivedmessages "
                    . "WHERE sapidoc_id = '" . $this->sapidoc->id . "' "
                    . "AND message_id = '" . $setup['id'] . "'";
            $result = $db->query($sql);
            $row = $db->fetchByAssoc($result);
            if (empty($row['id'])) {
                $insert = "INSERT INTO sapidocreceivedmessages SET "
                        . "id = '" . $id . "', "
                        . "sapidoc_id = '" . $this->sapidoc->id . "', "
                        . "message = '" . $message . "', "
                        . "message_id = '" . $setup['id'] . "', "
                        . "date_entered = '" . $timedate->nowDB() . "' ";
                $db->query($insert);
            }
            $params = array($path, $node, $this->sapidoc, $id);
            $function = $setup['message_function'];
            if (file_exists(get_custom_file_if_exists(self::custom_functions_path . '/' . $function . '.php'))) {
                include_once(get_custom_file_if_exists(self::custom_functions_path . '/' . $function . '.php'));
                if (function_exists($function)) {
                    $response = call_user_func_array($function, $params);
                }
                return $response;
            }
            if (method_exists($this, $function)) {
                // maybe a stupid backup...
                $response = call_user_func_array(array($this . $function), $params);
            }
        } else {
            LoggerManager::getLogger()->error("no message defined for type: " . $message_type . " id: " . $message_id . " number: " . $message_number);
        }
        return null;
    }

    private function handleMessages($response) {

        $dom = new DOMDocument('1.0');
        try {
            //loads XML to the Document
            $dom->loadXML($response);
            $path = new DOMXPath($dom);
            $this->registerNS($path);
            $nodes = $path->query($this->root_node . "/EtReturn/item");
            for ($i = 0; $i < $nodes->length; $i++) {
                $node = $nodes->item($i);
                $id = $this->saveMessage($path, $node);
                if ($id) {
                    
                }
            }
            $sap_status_code = $path->query($this->root_node . "/EvStatus")->item(0)->nodeValue;
            $this->sapidoc->sap_status_code = $sap_status_code;
            $this->sapidoc->save(false);
        } catch (DOMException $e) {
            
        }
    }

    public function setSAPIdoc($sapidoc) {
        if ($sapidoc instanceof SAPIdoc) {
            $this->sapidoc = $sapidoc;
        } else {
            if (empty($sapidoc)) {
                $this->sapidoc = BeanFactory::newBean("SAPIdocs");
            } else {
                $this->sapidoc = BeanFactory::getBean("SAPIdocs", $sapidoc);
            }
        }
    }

    public function callSAPIdocStatusMessageService() {
        
        // specify the REST web service to interact with
        $url = SpiceConfig::getInstance()->config['SAPIdoc']['status_service'];
        // Open a curl session for making the call
        $curl = curl_init($url);
        // Tell curl to use HTTP POST
        curl_setopt($curl, CURLOPT_POST, true);
        // basic authentifaction headers
        curl_setopt($curl, CURLOPT_USERPWD, trim(SpiceConfig::getInstance()->config['SAPIdoc']['user']) . ":" . trim(SpiceConfig::getInstance()->config['SAPIdoc']['password']));
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        // Tell curl not to return headers, but do return the response
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Set the POST arguments to pass to the Sugar server
        $payload = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:urn=\"urn:sap-com:document:sap:soap:functions:mc-style\">
        <soapenv:Header/>
        <soapenv:Body>
            <urn:Z01bcIdocStatus>
                <IvMestyp>" . $this->sapidoc->mestyp . "</IvMestyp>
                <IvRefmes>" . $this->sapidoc->refmes . "</IvRefmes>
                <IvSndprn>" . SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRN'] . "</IvSndprn>
                <IvSndprt>" . SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRT'] . "</IvSndprt>
           </urn:Z01bcIdocStatus>
        </soapenv:Body>
        </soapenv:Envelope>";
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-type: text/xml;charset=UTF-8',
            'Content-length: ' . strlen($payload)
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        // Make the REST call, returning the result
        //$response = curl_exec($curl);
        $response = '<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/"><soap-env:Header></soap-env:Header><soap-env:Body><n0:Z01bcIdocStatusResponse xmlns:n0="urn:sap-com:document:sap:soap:functions:mc-style"><EtReturn><item><Type>I</Type><Id>ZICA</Id><Number>013</Number><Message>Status-Request zu Nachrichtentyp DEBMAS mit ID 00150332453618 (Sender LS SUGARNEU).</Message><LogNo></LogNo><LogMsgNo>000000</LogMsgNo><MessageV1>DEBMAS</MessageV1><MessageV2>00150332453618</MessageV2><MessageV3>LS</MessageV3><MessageV4>SUGARNEU</MessageV4><Parameter></Parameter><Row>0</Row><Field></Field><System></System></item><item><Type>E</Type><Id>F2</Id><Number>042</Number><Message>Das Konto 100193 wird zur Zeit vom Benutzer KNOLLCHR bearbeitet.</Message><LogNo></LogNo><LogMsgNo>000000</LogMsgNo><MessageV1>100193</MessageV1><MessageV2>KNOLLCHR</MessageV2><MessageV3></MessageV3><MessageV4></MessageV4><Parameter></Parameter><Row>0</Row><Field></Field><System></System></item><item><Type>I</Type><Id>B1</Id><Number>042</Number><Message>Direktaufruf gestartet</Message><LogNo></LogNo><LogMsgNo>000000</LogMsgNo><MessageV1></MessageV1><MessageV2></MessageV2><MessageV3></MessageV3><MessageV4></MessageV4><Parameter></Parameter><Row>0</Row><Field></Field><System></System></item><item><Type>S</Type><Id>F2</Id><Number>056</Number><Message>Änderungen wurden durchgeführt</Message><LogNo></LogNo><LogMsgNo>000000</LogMsgNo><MessageV1></MessageV1><MessageV2></MessageV2><MessageV3></MessageV3><MessageV4></MessageV4><Parameter></Parameter><Row>0</Row><Field></Field><System></System></item></EtReturn><EvDocnum>0000000000225045</EvDocnum><EvReturnCode>12</EvReturnCode><EvStatus>53</EvStatus><EvStatusText>Anwendungsbeleg gebucht</EvStatusText></n0:Z01bcIdocStatusResponse></soap-env:Body></soap-env:Envelope>';

        if (empty($response)) {
            LoggerManager::getLogger()->fatal("no request sent or arrived at host: " . SpiceConfig::getInstance()->config['SAPIdoc']['status_service']);
            return null;
        }
        $info = curl_getinfo($curl);
        curl_close($curl);
        switch ($info['http_code']) {
            case ($info['http_code'] >= 200 && $info['http_code'] < 300):
                LoggerManager::getLogger()->info("success: " . print_r($response, true));
                break;
            default:
                LoggerManager::getLogger()->error("failure: " . print_r($response, true));
                break;
        }
        return $response;
    }

    public static function querySAPIdocStatus() {

        $db = DBManagerFactory::getInstance();

        $handler = new SAPMessageHandler();
        $sql = "SELECT id FROM sapidocs "
                . "WHERE deleted = 0 "
                . "AND status = 'exported' "
                . "AND sap_status_code != 53";
        $result = $db->query($sql);
        while ($row = $db->fetchByAssoc($result)) {
            $handler->setSAPIdoc($row['id']);
            $response = $handler->callSAPIdocStatusMessageService();
            if ($response) {
                $handler->handleMessages($response);
            }
        }
        exit;
    }

    public function loadMessageSetup($number = "", $type = "", $id = "") {

        $db = DBManagerFactory::getInstance();

        $sql = "SELECT * FROM sapidocmessages "
                . "WHERE message_number = '" . $number . "' "
                . "AND message_type = '" . $type . "' "
                . "AND message_id = '" . $id . "' "
                . "AND deleted = 0 "
                . "LIMIT 1";
        $result = $db->query($sql);
        $row = $db->fetchByAssoc($result);
        if (empty($row['id'])) {
            return null;
        }
        return $row;
    }

}
