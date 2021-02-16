<?php
namespace SpiceCRM\modules\Mailboxes\Handlers;

use CURLFile;
use Exception;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;
use SpiceCRM\modules\Emails\Email;
use SpiceCRM\modules\Mailboxes\Mailbox;

class MailgunHandler extends TransportHandler
{
    const REGION_US = 'us';
    const REGION_EU = 'eu';

    private $apiKey;
    private $apiUrl;

    protected $outgoing_settings = [
        'api_key',
        'imap_pop3_username',
        'domain',
        'reply_to',
        'region',
    ];

    /**
     * @inheritdoc
     */
    protected function initTransportHandler() {
        $apiUrl = 'https://api.mailgun.net';
        if ($this->mailbox->region == self::REGION_EU) {
            $apiUrl = 'https://api.eu.mailgun.net';
        }

        $this->apiKey = $this->mailbox->api_key;
        $this->apiUrl = $apiUrl . '/v3/' . $this->mailbox->domain;
    }

    /**
     * @inheritdoc
     */
    public function testConnection($testEmail) {
        $result['result'] = false;

        try {
            $result = $this->sendMail(Email::getTestEmail($this->mailbox, $testEmail));
        } catch (Exception $e) {
            $result['errors'] = $e->getMessage();
            $this->log(Mailbox::LOG_DEBUG, $this->mailbox->name . ': ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function composeEmail($email) {
        $this->checkEmailClass($email);

        $message = [];
        $toAddresses  = [];
        $ccAddresses  = [];
        $bccAddresses = [];

        $message['from'] = $this->mailbox->imap_pop3_username;

        foreach ($email->to() as $recipient) {
            $toAddresses[] = $recipient['email'];
        }

        if ($this->mailbox->catch_all_address == '') {
            foreach ($email->cc() as $recipient) {
                $message['cc'] = $recipient['email'];
            }
            foreach ($email->bcc() as $recipient) {
                $message['bcc'] = $recipient['email'];
            }
            $message['to']  = implode(',', $toAddresses);
            $message['cc']  = implode(',', $ccAddresses);
            $message['bcc'] = implode(',', $bccAddresses);

            if (empty($message['cc'])) {
                unset($message['cc']);
            }
            if (empty($message['bcc'])) {
                unset($message['bcc']);
            }

        } else { // send everything to the catch all address
            $message['to'] = $this->mailbox->catch_all_address;

            // add a message for whom this was intended for
            $email->name .= ' [intended for ' . implode(', ', $toAddresses) . ']';
        }

        if ($this->mailbox->reply_to != '') {
            $message['h:Reply-To'] = $this->mailbox->reply_to;
        }

        $message['subject'] = $email->name;
        $message['html']    = $email->body;

        if($email->id) {
            $message['attachment'] = [];
            $i = 0;
            foreach (json_decode(SpiceAttachments::getAttachmentsForBean('Emails', $email->id)) as $att) {
                $message["attachment[{$i}]"] = new CURLFile('upload://' . $att->filemd5, $att->file_mime_type, $att->filename);
                ++$i;
            }
            if (empty($message['attachment'])) {
                unset($message['attachment']);
            }
        }

        return $message;
    }

    /**
     * @inheritdoc
     */
    protected function dispatch($message) {
        $url = $this->apiUrl . '/messages';
        $headers = [
            'Authorization: Basic ' . base64_encode('api:' . $this->apiKey),
            (!empty($message['attachment'])) ? 'Content-Type: multipart/form-data' : null,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST           => true,
//            CURLOPT_POSTFIELDS     => $message,
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
        $response = curl_exec($curl);
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] === 200) {
            $result = [
                'result'     => true,
                'message_id' => $this->extractMessageId($response),
            ];
        } else {
            $result = [
                'result' => false,
                'errors' => $response->http_response_body->message,
            ];
            $this->log(Mailbox::LOG_DEBUG, $this->mailbox->name . ': ' . $response->http_response_body->message);
        }

        return $result;
    }

    /**
     * Extracts the external message ID from the Mailgun response.
     *
     * @param $response
     * @return string
     */
    private function extractMessageId($response) {
        $jsonResponse = json_decode($response);
        if (isset($jsonResponse->id)) {
            return $jsonResponse->id;
        }

        return '';
    }
}
