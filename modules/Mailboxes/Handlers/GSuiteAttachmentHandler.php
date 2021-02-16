<?php
namespace SpiceCRM\modules\Mailboxes\Handlers;
use SpiceCRM\modules\Emails\Email;
use Exception;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;

class GSuiteAttachmentHandler
{
    public $email;
    public $attachments = [];

    public function __construct(Email $email, $attachmentData) {
        if (!isset($email)) {
            throw new Exception('Email is missing!');
        }

        $this->email  = $email;

        foreach ($attachmentData['attachments'] as $item) {
            $attachment = new GSuiteAttachment($item, $this->email);
            $attachment->content = base64_decode($item['content']);
            $attachment->fileMd5 = md5($attachment->content);
            $attachment->fileSize = strlen($attachment->content);
            array_push($this->attachments, $attachment);
        }
    }

    public function saveAttachments() {
        $result = [];

        foreach ($this->attachments as $attachment) {
            // todo check if the attachment already exists
            $result[$attachment->id] = SpiceAttachments::saveEmailAttachmentFromGSuite($this->email, $attachment);
        }

        return $result;
    }
}
