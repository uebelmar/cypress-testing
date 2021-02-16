<?php

namespace SpiceCRM\includes\SpiceBCardReader\KREST\controllers;

use SpiceCRM\includes\SugarObjects\SpiceConfig;

class KRESTBCardReaderController
{
    public $applicationId = '';
    public $password = '';

    public function __construct()
    {

        if (SpiceConfig::getInstance()->config['bcard_reader']) {
            $this->applicationId = SpiceConfig::getInstance()->config['bcard_reader']['applicationId'];
            $this->password = SpiceConfig::getInstance()->config['bcard_reader']['password'];
        }
        $this->defineCURLFileCreateFunction();
    }

    /*
     * @define curl_file_create if not exist
     */
    private function defineCURLFileCreateFunction() {
        if (!function_exists('curl_file_create')) {
            function curl_file_create($filename, $mimetype = '', $postname = '')
            {
                return "@$filename;filename="
                    . ($postname ?: basename($filename))
                    . ($mimetype ? ";type=$mimetype" : '');
            }
        }
    }

    /*
     * @param $req
     * @param $res
     * @param $args
     * @return $vCard: string
     */
    public function processBusinessCard($req, $res, $args)
    {
        if (empty($this->applicationId) || empty($this->password)) return false;

        $postBody = $req->getParsedBody();
        $file = $postBody['card'];

        // 1. Send image to Cloud OCR SDK using processBusinessCard call
        $filePath = $this->uploadTempImage($file);
        $curlHandle = curl_init();

        // 2. Get response as xml
        $response = $this->postBCardToHandler($curlHandle, $filePath, $file['filemimetype']);

        unlink($filePath);

        $arr = $this->handleBCardHandlerResponse($curlHandle, $response);

        // 3. Read taskId from xml
        $arr = $this->getBCardHandlerTaskStatus($arr["id"]);

        // Result is ready. Download it
        $vCard = $this->downloadVCard($arr["resultUrl"]);

        // Let user download rtf result
        return $res->withJson(array('vcard' => $vCard));
    }

    /*
     * Download recognition result (text) and display it
     * @param $url
     * @return $response: curl_exec
     */
    private function downloadVCard($url)
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        // Warning! This is for easier out-of-the box usage of the sample only.
        // The URL to the result has https:// prefix, so SSL is required to
        // download from it. For whatever reason PHP runtime fails to perform
        // a request unless SSL certificate verification is off.
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    /*
     * @param $taskId
     * @return $response: curl_exec
     */
    private function getBCardHandlerTaskStatus($taskId)
    {
        $arr = [];
        // 4. Get task information in a loop until task processing finishes
        // 5. If response contains "Completed" status - extract url with result
        $url = 'http://cloud.ocrsdk.com/getTaskStatus';
        $qry_str = "?taskid=$taskId";
        // Check task status in a loop until it is finished
        // Note: it's recommended that your application waits
        // at least 2 seconds before making the first getTaskStatus request
        // and also between such requests for the same task.
        // Making requests more often will not improve your application performance.
        // Note: if your application queues several files and waits for them
        // it's recommended that you use listFinishedTasks instead (which is described
        // at http://ocrsdk.com/documentation/apireference/listFinishedTasks/).
        while (true) {
            sleep(5);
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url . $qry_str);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_USERPWD, "$this->applicationId:$this->password");
            curl_setopt($curlHandle, CURLOPT_USERAGENT, "PHP Cloud OCR SDK Sample");
            curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
            $response = curl_exec($curlHandle);
            $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            curl_close($curlHandle);

            // parse xml
            $xml = simplexml_load_string($response);
            if ($httpCode != 200) {
                if (property_exists($xml, "message")) {
                    die($xml->message);
                }
                die("Unexpected response " . $response);
            }
            $arr = $xml->task[0]->attributes();
            $taskStatus = $arr["status"];
            if ($taskStatus == "Queued" || $taskStatus == "InProgress") {
                // continue waiting
                continue;
            }
            if ($taskStatus == "Completed") {
                // exit this loop and proceed to handling the result
                break;
            }
            if ($taskStatus == "ProcessingFailed") {
                die("Task processing failed: " . $arr["error"]);
            }
            die("Unexpected task status " . $taskStatus);
        }
        return $arr;
    }

    private function uploadTempImage($file)
    {
        // change for windows or Linux
        $tmpfname = tempnam(sys_get_temp_dir(), '');
        file_put_contents($tmpfname, base64_decode($file['file']));
        return $tmpfname;
    }

    private function handleBCardHandlerResponse($curlHandle, $response)
    {
        if ($response == FALSE) {
            $errorText = curl_error($curlHandle);
            curl_close($curlHandle);
            die($errorText);
        }
        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);
        // Parse xml response
        $xml = simplexml_load_string($response);
        if ($httpCode != 200) {
            if (property_exists($xml, "message")) {
                die($xml->message);
            }
            die("unexpected response " . $response);
        }
        $arr = $xml->task[0]->attributes();
        $taskStatus = $arr["status"];
        if ($taskStatus != "Queued") {
            die("Unexpected task status " . $taskStatus);
        }
        return $arr;
    }

    private function postBCardToHandler($curlHandle, $filePath, $mimeType)
    {
        // Send HTTP POST request and ret xml response
        $url = 'http://cloud.ocrsdk.com/processBusinessCard';
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_USERPWD, "$this->applicationId:$this->password");
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_USERAGENT, "PHP Cloud OCR SDK Sample");
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);

        $post_array = array(
            "file" => curl_file_create($filePath, $mimeType, 'card')
        );
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_array);

        return curl_exec($curlHandle);
    }
}
