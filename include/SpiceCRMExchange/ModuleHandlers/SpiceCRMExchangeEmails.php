<?php
namespace SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers;

use jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAttachmentsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use jamesiarmes\PhpEws\Enumeration\MessageDispositionType;
use jamesiarmes\PhpEws\Request\CreateAttachmentType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\Request\SendItemType;
use jamesiarmes\PhpEws\Type\BodyType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\EmailAddressType;
use jamesiarmes\PhpEws\Type\FileAttachmentType;
use jamesiarmes\PhpEws\Type\MessageType;
use jamesiarmes\PhpEws\Type\SingleRecipientType;
use jamesiarmes\PhpEws\Type\TargetFolderIdType;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;
use SpiceCRM\modules\Emails\Email;
use SpiceCRM\modules\Mailboxes\Mailbox;

class SpiceCRMExchangeEmails extends SpiceCRMExchangeBeans
{
    protected $moduleName     = 'Emails';
//    protected $itemName       = 'CalendarItem'; //todo find it
    protected $tableName      = 'emails';
//    protected $pivotTableName = 'calls_users';
//    protected $pivotBeanId    = 'call_id';
    protected $mailbox;

    public function __construct($user, &$bean, Mailbox $mailbox) {
        parent::__construct($user, $bean);

        $this->mailbox = $mailbox;
    }

    /**
     * composeEmail
     *
     * Converts the Email bean object into the structure used by the transport handler
     *
     * @param Email $email
     * @return mixed
     */
    public function composeEmail(Email $email) {
        // Create the message.
        $message = new MessageType();
        $message->Subject = $email->name;
        $this->setSender($message);
        $this->setRecipients($message);
        $this->setMessageBody($message);

        // Build the request,
        $request = new CreateItemType();
        $request->Items = new NonEmptyArrayOfAllItemsType();

        // Save the message, but do not send it.
        $request->MessageDisposition = MessageDispositionType::SAVE_ONLY;

        // Add the message to the request.
        $request->Items->Message[] = $message;

        // deal with the responses
        $msgResponse = $this->connector->client->request('CreateItem', $request);
        $msgResponseItems = $msgResponse->ResponseMessages->CreateItemResponseMessage[0]->Items;

        // todo save the external id
        $emailItemId = $msgResponseItems->Message[0]->ItemId;
        $email->external_id = $emailItemId->Id;

        if ($email->id == '') {
            $email->id = create_guid();
        }

        $this->addAttachments($emailItemId);

        return $emailItemId;
    }

    /**
     * dispatch
     *
     * Sends the converted Email
     *
     * @param $emailItemId
     * @return array
     */
    public function dispatch($emailItemId) {
        // Send and save message
        $msgSendRequest = new SendItemType();
        $msgSendRequest->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $msgSendRequest->ItemIds->ItemId = $emailItemId;
        $msgSendRequest->SavedItemFolderId = new TargetFolderIdType();

        $sentItemsFolder = new DistinguishedFolderIdType();
        $sentItemsFolder->Id = 'sentitems';
        $msgSendRequest->SavedItemFolderId->DistinguishedFolderId = $sentItemsFolder;

        // set to the other mailbox
//        if ($this->mailbox->ews_email != '') {
//            $msgSendRequest->SavedItemFolderId->DistinguishedFolderId->Mailbox = new EmailAddressType();
//            $msgSendRequest->SavedItemFolderId->DistinguishedFolderId->Mailbox->EmailAddress = $this->mailbox->ews_email;
//        }

        $msgSendRequest->SaveItemToFolder = true;
        $msgSendResponse = $this->connector->client->request('SendItem', $msgSendRequest);

        return [
            'message_id' => $emailItemId->Id,
        ];
    }

    protected function createOnExchange() {
        // TODO: Implement createOnExchange() method.
    }

    protected function updateOnExchange() {
        // TODO: Implement updateOnExchange() method.
    }

    protected function addToDeleteRequest($request) {
        // TODO: Implement addToDeleteRequest() method.
    }

    protected function mapBeanToEWS($exchangeItem) {
        // TODO: Implement mapBeanToEWS() method.
    }

    protected function getSpiceCRMId($itemId) {
        // TODO: Implement getSpiceCRMId() method.
    }

    protected function createUpdateArray() {
        // TODO: Implement createUpdateArray() method.
    }

    protected function getExternalId() {
        // TODO: Implement getExternalId() method.
    }

    private function setRecipients(MessageType &$message) {
        if ($this->mailbox->catch_all_address != '') {
            foreach ($this->spiceBean->to() as $address) {
                $recipient = new EmailAddressType();
                $recipient->EmailAddress = $this->mailbox->catch_all_address;
                $message->ToRecipients->Mailbox[] = $recipient;
            }
        } else {
            $this->setToRecipients($message);
            $this->setCcRecipients($message);
            $this->setBccRecipients($message);
        }
    }

    private function setSender(MessageType &$message) {
        $message->From = new SingleRecipientType();
        $message->From->Mailbox = new EmailAddressType();
        $message->From->Mailbox->EmailAddress = $this->user->email1; // todo which email address?
    }

    private function setToRecipients(MessageType &$message) {
        $message->ToRecipients = new ArrayOfRecipientsType();
        foreach ($this->spiceBean->to() as $address) {
            $recipient = new EmailAddressType();
            $recipient->EmailAddress = $address['email'];
            $message->ToRecipients->Mailbox[] = $recipient;
        }
    }

    private function setCcRecipients(MessageType &$message) {
        foreach ($this->spiceBean->cc() as $address) {
            $recipient = new EmailAddressType();
            $recipient->EmailAddress = $address['email'];
            $message->CcRecipients->Mailbox[] = $recipient;
        }
    }

    private function setBccRecipients(MessageType &$message) {
        foreach ($this->spiceBean->bcc() as $address) {
            $recipient = new EmailAddressType();
            $recipient->EmailAddress = $address['email'];
            $message->BccRecipients->Mailbox[] = $recipient;
        }
    }

    private function setMessageBody(MessageType &$message) {
        $message->Body = new BodyType();
        $message->Body->BodyType = BodyTypeType::HTML;
        $message->Body->_ = $this->spiceBean->body;
    }

    private function addAttachments(&$emailItemId) {
        $attachments = [];
        foreach (json_decode(SpiceAttachments::getAttachmentsForBean('Emails', $this->spiceBean->id)) as $att) {
            $attachment              = new FileAttachmentType();
            $attachment->Content     = file_get_contents('upload://' . $att->filemd5);
            $attachment->Name        = $att->filename;
            $attachment->ContentType = $att->file_mime_type;

            $attachments[] = $attachment;
        }

        if (count($attachments) > 0) {
            // Attach files to message
            $attRequest = new CreateAttachmentType();
            $attRequest->ParentItemId = $emailItemId;
            $attRequest->Attachments = new NonEmptyArrayOfAttachmentsType();
            $attRequest->Attachments->FileAttachment = $attachments;

            $attResponse = $this->connector->client->request('CreateAttachment', $attRequest);
            $attResponseId = $attResponse->ResponseMessages->CreateAttachmentResponseMessage[0]->Attachments
                ->FileAttachment[0]->AttachmentId;

            // Save message id from create attachment response
            $emailItemId->ChangeKey = $attResponseId->RootItemChangeKey;
            $emailItemId->Id = $attResponseId->RootItemId;
        }
    }
}
