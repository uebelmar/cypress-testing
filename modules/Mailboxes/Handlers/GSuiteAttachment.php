<?php
namespace SpiceCRM\modules\Mailboxes\Handlers;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\modules\Emails\Email;
use SpiceCRM\includes\authentication\AuthenticationController;

class GSuiteAttachment
{
    public $id;
    public $fileName;
    public $fileMimeType;
    public $fileSize;
    public $attachmentType;
    public $isInline;
    public $externalId;
    public $content;
    public $emailId;
    public $beanType;
    public $trdate;
    public $fileMd5;
    public $thumbnail;
    public $deleted;
    public $userId;

    private $table = 'spiceattachments';

    public function __construct($rawData, Email $email) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $this->id             = create_guid();
        $this->fileName       = filter_var($rawData['name'], FILTER_SANITIZE_STRING);
        $this->fileMimeType   = filter_var($rawData['contentType'], FILTER_SANITIZE_STRING);
        $this->fileSize       = filter_var($rawData['size'], FILTER_SANITIZE_NUMBER_INT);
        $this->attachmentType = filter_var($rawData['attachmentType'], FILTER_SANITIZE_STRING);
        $this->isInline       = $rawData['isInline'];
        $this->externalId     = filter_var($rawData['id'], FILTER_SANITIZE_STRING);
        $this->emailId        = filter_var($email->id, FILTER_SANITIZE_STRING);
        $this->beanType       = 'Emails';
        $this->trdate         = date('Y-m-d H:i:s');
        $this->deleted        = 0;
        $this->userId         = $current_user->id;
    }

    public function save() {
        $db = DBManagerFactory::getInstance();

        $insertQuery = "INSERT INTO " . $this->table
            . " (`id`, `bean_type`, `bean_id`, `user_id`, `trdate`, `filename`, `filesize`, `filemd5`,"
            . " `file_mime_type`, `text`, `thumbnail`, `deleted`, `external_id`) VALUES ("
            . "'" . $this->id . "', "
            . "'" . $this->beanType . "', "
            . "'" . $this->emailId . "', "
            . "'" . $this->userId . "', "
            . "'" . $this->trdate . "', "
            . "'" . $this->fileName . "', "
            . "'" . $this->fileSize . "', "
            . "'" . $this->fileMd5 . "', "
            . "'" . $this->fileMimeType . "', "
            . "'', "
            . "'" . $this->thumbnail . "', "
            . "'" . $this->deleted . "', "
            . "'" . $this->externalId . "'"
            . ")";

        $db->query($insertQuery);

        // todo add some return value
    }
}
