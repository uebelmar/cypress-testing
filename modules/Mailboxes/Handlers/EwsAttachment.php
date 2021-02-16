<?php
namespace SpiceCRM\modules\Mailboxes\Handlers;

use jamesiarmes\PhpEws\Type\FileAttachmentType;

class EwsAttachment extends AbstractAttachment
{
    public function __construct(FileAttachmentType $attachment) {
        $this->content = $attachment->Content;
        $this->mime_type = $attachment->ContentType;
        $this->filename = $attachment->Name;
//        $attachment->ContentId;
//        $attachment->IsInline;
    }
}
