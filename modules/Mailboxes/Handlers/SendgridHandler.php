<?php
namespace SpiceCRM\modules\Mailboxes\Handlers;

use Exception;
use SendGrid;
use SendGrid\Email;
use SendGrid\Mail;
use SendGrid\Content;
use SpiceCRM\modules\Mailboxes\Mailbox;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;

/**
 * Class SendgridHandler
 *
 * In case of problems on windows servers check:
 * https://snippets.webaware.com.au/howto/stop-turning-off-curlopt_ssl_verifypeer-and-fix-your-php-config/
 *
 * @package SpiceCRM\modules\Mailboxes
 */
class SendgridHandler extends TransportHandler
{
    protected $outgoing_settings = [
        'api_key',
        'imap_pop3_username',
        'reply_to',
    ];

    protected function initTransportHandler()
    {
        $this->transport_handler = new SendGrid($this->mailbox->api_key);
    }

    public function testConnection($testEmail)
    {
        $response = $this->sendMail(Email::getTestEmail($this->mailbox, $testEmail));

        return $response;
    }

    protected function composeEmail($email)
    {
        $this->checkEmailClass($email);

        $from = new Email($this->mailbox->imap_pop3_display_name, $this->mailbox->imap_pop3_username);

        if ($this->mailbox->catch_all_address == '') {
            foreach ($email->to() as $recipient) {
                // todo make sure it actually works for multiple recipients
                $to = new Email($recipient['name'], $recipient['email']);
            }
        } else { // send everything to the catch all address
            $to = new Email($this->mailbox->catch_all_address, $this->mailbox->catch_all_address);

            // add a message for whom this was intended for
            $intendedReciepients = [];
            foreach ($email->to() as $recipient) {
                $intendedReciepients[] = $recipient['email'];
            }
            $email->name .= ' [intended for ' . join(', ', $intendedReciepients) . ']';
        }


        $subject = $email->name;
        $body    = new Content(
            "text/html",
            $email->body
        );

        $mail = new Mail($from, $subject, $to, $body);

        if (!empty($email->cc())) {
            foreach ($email->cc() as $recipient) {
                $cc = new Email($recipient['name'], $recipient['email']);
                $mail->personalization[0]->addCc($cc);
            }
        }

        if (!empty($email->bcc())) {
            foreach ($email->bcc() as $recipient) {
                $bcc = new Email($recipient['name'], $recipient['email']);
                $mail->personalization[0]->addBcc($bcc);
            }
        }

        if ($this->mailbox->reply_to != '') {
            $reply_to = new Email('', $this->mailbox->reply_to);
            $mail->setReplyTo($reply_to);
        }

        if($email->id) {
            foreach (json_decode(SpiceAttachments::getAttachmentsForBean('Emails', $email->id)) as $att) {
                $attachment = new SendGrid\Attachment();
                $attachment->setType($att->file_mime_type);
                $attachment->setDisposition("attachment");
                $attachment->setContentPath("upload://" . $att->filemd5);
                $attachment->setFilename($att->filename);

                $mail->addAttachment($attachment);
            }
        }

        return $mail;
    }

    protected function dispatch($message)
    {
        try {
            $response = $this->transport_handler->client->mail()->send()->post($message);

            if ($response->statusCode() == 202) {
                $result['result'] = true;
                foreach($response->headers() as $header){
                    if(strpos($header, 'X-Message-Id') !== false){
                        $arrayparts = explode(':', $header);
                        $result['message_id'] = trim($arrayparts[1]);
                    }
                }
            } else {
                $result['result'] = false;
                $result['errors'] = json_decode($response->body())->errors[0]->message;
                $this->log(Mailbox::LOG_DEBUG, $this->mailbox->name . ': ' .
                    json_decode($response->body())->errors[0]->message);
            }
        } catch (Exception $exception) {
            $result = [
                'result' => false,
                'errors' => $exception->getMessage(),
            ];
            $this->log(Mailbox::LOG_DEBUG, $this->mailbox->name . ': ' . $exception->getMessage());
        }

        return $result;
    }
}
