<?php
namespace SpiceCRM\modules\Mailboxes\Handlers;

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAttachmentsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfRequestAttachmentIdsType;
use jamesiarmes\PhpEws\Enumeration\FolderQueryTraversalType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Request\CreateAttachmentType;
use jamesiarmes\PhpEws\Request\FindFolderType;
use jamesiarmes\PhpEws\Request\GetAttachmentType;
use jamesiarmes\PhpEws\Type\AndType;
use jamesiarmes\PhpEws\Type\ConstantValueType;
use jamesiarmes\PhpEws\Type\FieldURIOrConstantType;
use jamesiarmes\PhpEws\Type\FileAttachmentType;
use jamesiarmes\PhpEws\Type\FolderIdType;
use jamesiarmes\PhpEws\Type\FolderResponseShapeType;
use jamesiarmes\PhpEws\Type\IsGreaterThanOrEqualToType;
use jamesiarmes\PhpEws\Type\IsLessThanOrEqualToType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\MessageType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\RequestAttachmentIdType;
use jamesiarmes\PhpEws\Type\RestrictionType;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Mailboxes\Mailbox;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\Emails\Email;
use DateTime;
use DateInterval;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;
use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\FindItemType;
use \jamesiarmes\PhpEws\Request\GetItemType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;

use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;

use \jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use \jamesiarmes\PhpEws\Enumeration\IndexBasePointType;
use \jamesiarmes\PhpEws\Enumeration\ItemQueryTraversalType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;

use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use \jamesiarmes\PhpEws\Type\IndexedPageViewType;
use \jamesiarmes\PhpEws\Type\ItemResponseShapeType;

use jamesiarmes\PhpEws\Type\BodyType;
use jamesiarmes\PhpEws\Enumeration\BodyTypeType;

use \jamesiarmes\PhpEws\Request\CreateItemType;

use \jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;

use \jamesiarmes\PhpEws\Enumeration\MessageDispositionType;

use jamesiarmes\PhpEws\Request\SendItemType;
use \jamesiarmes\PhpEws\Type\EmailAddressType;
use \jamesiarmes\PhpEws\Type\SingleRecipientType;
use jamesiarmes\PhpEws\Type\TargetFolderIdType;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\modules\Mailboxes\KREST\controllers\EwsController;
use Swift_Message;
use Swift_TransportException;

/**
 * Class ImapHandler
 */
class EwsHandler extends TransportHandler
{
    public $client;
    private $controller;

    protected $incoming_settings = [
        'ews_host',
        'ews_username',
        'ews_password',
//        'ews_email',
//        'ews_folder',
    ];
    protected $outgoing_settings = [
        'ews_host',
        'ews_username',
        'ews_password',
    ];
    public function __construct(Mailbox $mailbox) {
        parent::__construct($mailbox);

        $this->controller = new EwsController();
    }

    /**
     * initTransportHandler
     *
     * Initializes the transport handler.
     *
     * @throws Exception
     */
    protected function initTransportHandler() {
        // todo maybe also put them in the settings
        $timezone = 'UTC';
        $version = Client::VERSION_2016;

        if ($this->checkConfiguration($this->incoming_settings)['result']) {
            $this->client = new Client(
                $this->mailbox->ews_host,
                $this->mailbox->ews_username,
                $this->mailbox->ews_password,
                $version
            );
            $this->client->setTimezone($timezone);
        } else {
            throw new Exception('Cannot initialize the EWS Client.', 403);
        }
    }

    /**
     * testConnection
     *
     * Tests the EWS connection.
     *
     * @return mixed
     */
    public function testConnection($testEmail) {
        $status = $this->checkConfiguration($this->outgoing_settings);
        if (!$status['result']) {
            $response = [
                'result' => false,
                'errors' => 'No EWS connection set up. Missing values for: '
                    . implode(', ', $status['missing']),
            ];
            return $response;
        }

        try {
            $this->sendMail(Email::getTestEmail($this->mailbox, $testEmail));
            $response['result'] = true;
        } catch (Swift_TransportException $e) {
            $response['errors'] = $e->getMessage();
            LoggerManager::getLogger()->info($e->getMessage());
            $response['result'] = false;
        } catch (Exception $e) {
            $response['errors'] = $e->getMessage();
            LoggerManager::getLogger()->info($e->getMessage());
            $response['result'] = false;
        }

        return $response;
    }

    /**
     * fetchEmails
     *
     * Fetches Emails and saves them in the internal DB
     * It also fetches the attachments
     *
     * @throws \Exception
     */
    public function fetchEmails() {
        

        $this->controller->handleSubscription($this->mailbox);

        // Replace this with the number of items you would like returned for each page.
        $page_size = SpiceConfig::getInstance()->config['mailboxes']['delta_t'] ? (int) SpiceConfig::getInstance()->config['mailboxes']['delta_t'] : 10;

        // Build the request.
        $request = new FindItemType();
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $request->Traversal = ItemQueryTraversalType::SHALLOW;

        if (isset($this->mailbox->last_checked) && $this->mailbox->last_checked != '') {
            $startDate = new DateTime($this->mailbox->last_checked);
            $startDate = $startDate->sub(new DateInterval('P1D'));
        } else {
            $startDate = new DateTime('-2 weeks');
        }

        $this->buildFetchRestriction($request, $startDate);

        // Return all message properties.
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        // Search in the user's inbox.
        if ($this->mailbox->ews_folder != '') {
            $folderId = new FolderIdType();
            $folderId->Id = $this->mailbox->ews_folder->id;
            $request->ParentFolderIds->FolderId[] = $folderId;
        } else {
            $folderId = new DistinguishedFolderIdType();
            $folderId->Id = DistinguishedFolderIdNameType::INBOX;
            $request->ParentFolderIds->DistinguishedFolderId[] = $folderId;
        }

        $ewsEmail = $this->mailbox->ews_email ?? $this->mailbox->ews_username;

        $folderId->Mailbox = new EmailAddressType();
        $folderId->Mailbox->EmailAddress = $ewsEmail;

        // Limits the number of items retrieved
        $request->IndexedPageItemView = new IndexedPageViewType();
        $request->IndexedPageItemView->BasePoint = IndexBasePointType::BEGINNING;
        $request->IndexedPageItemView->Offset = 0;
        $request->IndexedPageItemView->MaxEntriesReturned = $page_size;

        $response = $this->client->FindItem($request);

        // Iterate over the results, printing any error messages or message subjects.
        $new_mail_count = 0;
        $response_messages = $response->ResponseMessages->FindItemResponseMessage;
        foreach ($response_messages as $response_message) {
            // Make sure the request succeeded.
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;

                $this->log(Mailbox::LOG_DEBUG,
                    $this->mailbox->name . ": Failed to search for messages with \"$code: $message\"\n");
                continue;
            }

            // Set the base values from the first page of results.
            $messages = $response_message->RootFolder->Items->Message;
            $last_page = $response_message->RootFolder->IncludesLastItemInRange;

            // Until we have the last page, keep requesting the next page of messages.
            for ($page_number = 1; !$last_page; ++$page_number) {
                // Request the next page.
                $request->IndexedPageItemView->Offset = $page_size * $page_number;
                $response = $this->client->FindItem($request);

                // Add the messages to the list of messages retrieved. If the total
                // number of messages is large, you could easily run out of memory here.
                // It is advised that you perform you operations on messages when you
                // retrieve them rather than keeping a list of them in memory.
                $response_message = $response->ResponseMessages
                    ->FindItemResponseMessage[0];
                $messages = array_merge(
                    $messages,
                    $response_message->RootFolder->Items->Message
                );

                // Store the updated last page value.
                $last_page = $response_message->RootFolder->IncludesLastItemInRange;
            }

            // Iterate over the messages that were found, printing the subject for each.
            foreach ($messages as $message) {
                EwsHandler::convertToBean($message->ItemId->Id, $message->ItemId->ChangeKey, $this->mailbox);
                ++$new_mail_count;

                $dateSent = new DateTime($message->DateTimeSent);
                $this->mailbox->last_checked  = $dateSent->format('Y-m-d H:i:s');
            }
        }

        $this->mailbox->save();

        return ['new_mail_count' => $new_mail_count];
    }

    /**
     * composeEmail
     *
     * Converts the Email bean object into the structure used by the transport handler
     *
     * @param Email $email
     * @return Swift_Message
     * @throws \Exception
     */
    protected function composeEmail($email) {
        // Create the message.
        $message = new MessageType();
        $message->Subject = $email->name;
        $message->ToRecipients = new ArrayOfRecipientsType();

        // Set the sender.
        $message->From = new SingleRecipientType();
        $message->From->Mailbox = new EmailAddressType();

        // check if we have a username
        $message->From->Mailbox->EmailAddress = $this->mailbox->ews_email ?? $this->mailbox->ews_username;

        // Set the recipients.
        // todo test catch all address
        if ($this->mailbox->catch_all_address != '') {
            foreach ($email->to() as $address) {
                $recipient = new EmailAddressType();
                $recipient->EmailAddress = $this->mailbox->catch_all_address;
                $message->ToRecipients->Mailbox[] = $recipient;
            }
        } else {
            foreach ($email->to() as $address) {
                $recipient = new EmailAddressType();
                $recipient->EmailAddress = $address['email'];
                $message->ToRecipients->Mailbox[] = $recipient;
            }
            foreach ($email->cc() as $address) {
                $recipient = new EmailAddressType();
                $recipient->EmailAddress = $address['email'];
                $message->CcRecipients->Mailbox[] = $recipient;
            }
            foreach ($email->bcc() as $address) {
                $recipient = new EmailAddressType();
                $recipient->EmailAddress = $address['email'];
                $message->BccRecipients->Mailbox[] = $recipient;
            }
        }

        // todo test reply to address
        if ($this->mailbox->reply_to != '') {
            $recipient = new EmailAddressType();
            $recipient->EmailAddress = $this->mailbox->reply_to;
            $message->ReplyTo->Mailbox[] = $recipient;
        }

        // Set the message body.
        $message->Body = new BodyType();
        $message->Body->BodyType = BodyTypeType::HTML;
        $message->Body->_ = $email->body;


        // Build the request,
        $request = new CreateItemType();
        $request->Items = new NonEmptyArrayOfAllItemsType();

        // Save the message, but do not send it.
        $request->MessageDisposition = MessageDispositionType::SAVE_ONLY;

        // Add the message to the request.
        $request->Items->Message[] = $message;

        // deal with the responses
        $msgResponse = $this->client->CreateItem($request);
        $msgResponseItems = $msgResponse->ResponseMessages->CreateItemResponseMessage[0]->Items;

        // todo save the external id
        $emailItemId = $msgResponseItems->Message[0]->ItemId;
        $email->external_id = $emailItemId->Id;

        if ($email->id == '') {
//            throw new Exception('Email ID missing. Cannot send email.', 418);
            $email->id = create_guid();
        }

        $attachments = [];
        foreach (json_decode (SpiceAttachments::getAttachmentsForBean('Emails', $email->id)) as $att) {
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

            $attResponse = $this->client->CreateAttachment($attRequest);
            $attResponseId = $attResponse->ResponseMessages->CreateAttachmentResponseMessage[0]->Attachments
                ->FileAttachment[0]->AttachmentId;

            // Save message id from create attachment response
            $emailItemId->ChangeKey = $attResponseId->RootItemChangeKey;
            $emailItemId->Id = $attResponseId->RootItemId;
        }

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
    protected function dispatch($emailItemId) {
        // Send and save message
        $msgSendRequest = new SendItemType();
        $msgSendRequest->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $msgSendRequest->ItemIds->ItemId = $emailItemId;
        $msgSendRequest->SavedItemFolderId = new TargetFolderIdType();

        $sentItemsFolder = new DistinguishedFolderIdType();
        $sentItemsFolder->Id = 'sentitems';
        $msgSendRequest->SavedItemFolderId->DistinguishedFolderId = $sentItemsFolder;

        // set to the other mailbox
        if ($this->mailbox->ews_email != '') {
            $msgSendRequest->SavedItemFolderId->DistinguishedFolderId->Mailbox = new EmailAddressType();
            $msgSendRequest->SavedItemFolderId->DistinguishedFolderId->Mailbox->EmailAddress = $this->mailbox->ews_email;
        }

        $msgSendRequest->SaveItemToFolder = true;
        $msgSendResponse = $this->client->SendItem($msgSendRequest);

        return [
            'message_id' => $emailItemId->Id,
        ];
    }

    /**
     * convert2Spice
     *
     * Converts the EWS message into an Email bean.
     *
     * @param MessageType $message
     * @throws \Exception
     */
    public function convert2Spice(MessageType $message) {
        try {
            Email::findByMessageId($message->ItemId->Id);
        } catch (\Exception $e) {
            $email = BeanFactory::getBean('Emails');
            $email->mailbox_id = $this->mailbox->id;
            $email->message_id = $message->ItemId->Id;
            $email->name       = $message->Subject;
            $dateSent          = new DateTime($message->DateTimeSent);
            $email->date_sent  = $dateSent->format('Y-m-d H:i:s');
            $email->from_addr  = $message->From->Mailbox->EmailAddress;

            $toRecipients      = [];
            foreach ($message->ToRecipients->Mailbox as $toRecipient) {
                $toRecipients[] = $toRecipient->EmailAddress;
            }
            $email->to_addrs   = implode(';', $toRecipients);
            $ccRecipients      = [];
            foreach ($message->CcRecipients->Mailbox as $ccRecipient) {
                $ccRecipients[] = $ccRecipient->EmailAddress;
            }
            $email->cc_addrs   = implode(';', $ccRecipients);
            $email->type       = Email::TYPE_INBOUND;
            $email->status     = Email::STATUS_UNREAD;
            $email->openness   = Email::OPENNESS_OPEN;
            $email->body       = $this->extractBody($message);

            try {
                $email->save();
            } catch (\Exception $e) {
                LoggerManager::getLogger()->error('Could not save email: ' . $email->name);
                return;
            }

            $this->handleAttachments($message, $email);

            $email->processEmail();
        }
    }

    /**
     * extractBody
     *
     * Extracts the email body from the EWS message.
     *
     * @param MessageType $message
     * @return mixed
     */
    private function extractBody(MessageType $message) {
        $request = new GetItemType();
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->ItemId = $message->ItemId;
        $response = $this->client->GetItem($request);
        $dmessage = $response->ResponseMessages->GetItemResponseMessage[0]->Items->Message[0];

        return $dmessage->Body->_;
    }

    /**
     * handleAttachments
     *
     * Saves the attachments in the file system and creates all the necessary relationship DB entries.
     *
     * @param MessageType $message
     * @param Email $email
     * @throws \Exception
     */
    private function handleAttachments(MessageType $message, Email $email) {
        $request = new GetItemType();
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();

        $item = new ItemIdType();
        $item->Id = $message->ItemId->Id;
        $request->ItemIds->ItemId[] = $item;

        $response = $this->client->GetItem($request);

        $responseMessages = $response->ResponseMessages->GetItemResponseMessage;
        foreach ($responseMessages as $responseMessage) {
            // Make sure the request succeeded.
            if ($responseMessage->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $responseMessage->ResponseCode;
                $message = $responseMessage->MessageText;
                throw new Exception("Failed to get message with \"$code: $message\"\n");
                continue;
            }

            $attachments = [];

            foreach ($responseMessage->Items->Message as $item) {
                // If there are no attachments for the item, move on to the next
                // message.
                if (empty($item->Attachments)) {
                    continue;
                }
                // Iterate over the attachments for the message.
                foreach ($item->Attachments->FileAttachment as $attachment) {
                    $attachments[] = $attachment->AttachmentId->Id;
                }
            }

            if (empty($attachments)) {
                continue;
            }

            // Build the request to get the attachments.
            $request = new GetAttachmentType();
            $request->AttachmentIds = new NonEmptyArrayOfRequestAttachmentIdsType();
            // Iterate over the attachments for the message.
            foreach ($attachments as $attachment_id) {
                $id = new RequestAttachmentIdType();
                $id->Id = $attachment_id;
                $request->AttachmentIds->AttachmentId[] = $id;
            }
            $response = $this->client->GetAttachment($request);

            // Iterate over the response messages, printing any error messages or
            // saving the attachments.
            $attachmentResponseMessages = $response->ResponseMessages->GetAttachmentResponseMessage;
            foreach ($attachmentResponseMessages as $attachmentResponseMessage) {
                // Make sure the request succeeded.
                if ($attachmentResponseMessage->ResponseClass != ResponseClassType::SUCCESS) {
                    $code = $responseMessage->ResponseCode;
                    $message = $responseMessage->MessageText;
                    throw new Exception("Failed to get attachment with \"$code: $message\"\n");
                    continue;
                }

                foreach ($this->getAttachments($attachmentResponseMessage->Attachments->FileAttachment) as $attachment) {
                    SpiceAttachments::saveEmailAttachment('Emails', $email->id, $attachment);
                }
            }
        }
    }

    /**
     * getAttachments
     *
     * Converts the EWS attachments into a format that can be saved as SpiceAttachments.
     *
     * @param $attachments
     * @return array
     * @throws \Exception
     */
    private function getAttachments($attachments) {
        $attachmentArray = [];

        // Iterate over the file attachments, saving each one.
        foreach ($attachments as $attachment) {
            $ewsAttachment = new EwsAttachment($attachment);
            $ewsAttachment->saveFile();

            $attachmentArray[] = $ewsAttachment;
        }

        return $attachmentArray;
    }

    /**
     * getMailboxes
     *
     * Returns the mailbox folders
     *
     * @return array
     */
    public function getMailboxes()
    {
        $request = new FindFolderType();
        $request->Traversal = FolderQueryTraversalType::DEEP;
        $request->FolderShape = new FolderResponseShapeType();
        $request->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        $request->IndexedPageFolderView = new IndexedPageViewType();
        $request->IndexedPageFolderView->BasePoint = 'Beginning';
        $request->IndexedPageFolderView->Offset = 0;

        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

        $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
        $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::INBOX;

        if ($this->mailbox->ews_email != '') {
            $request->ParentFolderIds->DistinguishedFolderId->Mailbox = new EmailAddressType();
            $request->ParentFolderIds->DistinguishedFolderId->Mailbox->EmailAddress = $this->mailbox->ews_email;
        }

        // checken ob es zugriff gibt
        $response = $this->client->FindFolder($request);

        $mailboxes = $response->ResponseMessages->FindFolderResponseMessage[0]->RootFolder->Folders->Folder;

        $mailboxArray = [];
        foreach ($mailboxes as $mailbox) {
            $mailboxArray[] = [
                'id'   => $mailbox->FolderId->Id,
                'name' => $mailbox->DisplayName,
            ];
        }

        return [
            'result'    => true,
            'mailboxes' => $mailboxArray,
        ];
    }

    /**
     * buildFetchRestriction
     *
     * Builds the restriction with search parameters for fetching emails.
     * As of now it only accepts the start date. Might be changed later.
     *
     * @param FindItemType $request
     * @param DateTime $startDate
     * @throws \Exception
     */
    private function buildFetchRestriction(FindItemType &$request, DateTime $startDate) {
        $endDate = new DateTime('now');
        // Build the start date restriction.
        $greaterThan = new IsGreaterThanOrEqualToType();
        $greaterThan->FieldURI = new PathToUnindexedFieldType();
        $greaterThan->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
        $greaterThan->FieldURIOrConstant = new FieldURIOrConstantType();
        $greaterThan->FieldURIOrConstant->Constant = new ConstantValueType();
        $greaterThan->FieldURIOrConstant->Constant->Value = $startDate->format('c');

        // Build the end date restriction;
        $lessThan = new IsLessThanOrEqualToType();
        $lessThan->FieldURI = new PathToUnindexedFieldType();
        $lessThan->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
        $lessThan->FieldURIOrConstant = new FieldURIOrConstantType();
        $lessThan->FieldURIOrConstant->Constant = new ConstantValueType();
        $lessThan->FieldURIOrConstant->Constant->Value = $endDate->format('c');

        // Build the restriction.
        $request->Restriction = new RestrictionType();
        $request->Restriction->And = new AndType();
        $request->Restriction->And->IsGreaterThanOrEqualTo = $greaterThan;
        $request->Restriction->And->IsLessThanOrEqualTo = $lessThan;
    }

    /**
     * getItem
     *
     * Returns an email with the given IDs from EWS.
     *
     * @param $itemId
     * @param $changeKey
     * @return mixed
     */
    public function getItem($itemId, $changeKey) {
        $request = new GetItemType();
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->ItemId = new ItemIdType();
        $request->ItemIds->ItemId->Id = $itemId;
        $request->ItemIds->ItemId->ChangeKey = $changeKey;

        $response = $this->client->GetItem($request);

        return $response->ResponseMessages->GetItemResponseMessage[0]->Items->Message[0];
    }

    /**
     * convertToBean
     *
     * Converts the EWS object into an Email bean and saves it.
     *
     * @param $itemId
     * @param $changeKey
     * @param Mailbox $mailbox
     */
    public static function convertToBean($itemId, $changeKey, Mailbox $mailbox) {
        try {
            $mailbox->initTransportHandler();
            $ewsItem = $mailbox->transport_handler->getItem($itemId, $changeKey);
            $mailbox->transport_handler->convert2Spice($ewsItem);
        } catch (\Exception $e) {
            LoggerManager::getLogger()->debug($e->getMessage());
        }
    }
}
