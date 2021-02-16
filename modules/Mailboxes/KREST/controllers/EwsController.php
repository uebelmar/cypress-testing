<?php
namespace SpiceCRM\modules\Mailboxes\KREST\controllers;

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfNotificationEventTypesType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Enumeration\NotificationEventTypeType;
use jamesiarmes\PhpEws\Request\SubscribeType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\FolderIdType;
use jamesiarmes\PhpEws\Type\PushSubscriptionRequestType;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\Mailboxes\Mailbox;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Mailboxes\Handlers\EwsHandler;
use DateTime;
use DateInterval;

class EwsController
{

    /**
     * The interval after which EWS email push notifications are sent from the Exchange server.
     *
     * @var int
     */
    private $statusFrequency = 10;

    /**
     * getMailboxFolders
     *
     * Returns the mailbox folders
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getMailboxFolders($req, $res, $args) {
        $params = $req->getParsedBody();

        $mailbox = BeanFactory::getBean('Mailboxes');
        foreach ($params['data'] as $name => $value) {
            if (isset($mailbox->field_name_map[$name])) {
                $mailbox->$name = $value;
            }
        }

        $mailbox->initTransportHandler();

        $result = $mailbox->transport_handler->getMailboxes();

        return $res->withJson($result);
    }

    /**
     * Handles the incoming push notification from EWS containing the new emails
     *
     * todo update last_checked date in the mailbox
     *
     * @param $req
     * @param $res
     * @param $args
     * @throws Exception
     */
    public function handle($req, $res, $args) {
        $mailbox = BeanFactory::getBean('Mailboxes', $args['mailboxId']);
        if (!$mailbox) {
            throw new Exception('Cannot find the Mailbox with the ID: ' . $args['mailboxId'], 404);
        }
        $mailbox->initTransportHandler();

        // get the body and reformat to get the real XML
        $body = $req->getBody()->__toString();

        // ugly but seemingly required to make it readable
        // ToDo: check if this can be done better with namespaces registered
        $xml = str_replace(["\n", "soap11:", "m:", "t:"], '', $body);
        $newXML = simplexml_load_string($xml);
        $notification = $newXML->Body->SendNotification->ResponseMessages->SendNotificationResponseMessage->Notification;
        $subscriptionId = $notification->SubscriptionId->__toString();
        $previousWatermark = $notification->PreviousWatermark->__toString();

        $subscription = false;
        if (self::checkMailboxSubscription($mailbox, $subscriptionId)) {
            $subscription = true;

            if ($notification->NewMailEvent) {
                $itemId = (string) $notification->NewMailEvent->ItemId['Id'];
                $changeKey = (string) $notification->NewMailEvent->ItemId['ChangeKey'];

                EwsHandler::convertToBean($itemId, $changeKey, $mailbox);
            }
        } else {
            // remove subscriptionId and the watermark
            // todo try to add a new subscription
        }

        $responseOK = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope"><soap:Body><SendNotificationResult xmlns="http://schemas.microsoft.com/exchange/services/2006/messages"><SubscriptionStatus>' . ($subscription ? 'OK' : 'Unsubscribe') . '</SubscriptionStatus></SendNotificationResult></soap:Body></soap:Envelope>';
        echo $responseOK;
    }


    /**
     * Checks if a susbcription is registered for a mailbox.
     *
     * @param $subscriptionId
     * @return array
     */
    static function checkMailboxSubscription(Mailbox $mailbox, $subscriptionId) {
        if ($mailbox->ews_subscriptionid == $subscriptionId && $mailbox->ews_push == true) {
            $mailbox->last_checked = date('Y-m-d H:i:s');
            $mailbox->save();
            return true;
        }

        return false;
    }

    /**
     * Starts a subscription for email push notifications at the exchange server.
     *
     * @param Mailbox $mailbox
     * @throws \Exception
     */
    public function subscribe(Mailbox &$mailbox) {
        $mailbox->initTransportHandler();
        if (!$mailbox->ews_push) {
            // todo remove the subscription data from the settings
            return;
        }

        

        // Build the request.
        $request = new SubscribeType();

        $eventTypes = new NonEmptyArrayOfNotificationEventTypesType();
        $eventTypes->EventType = [NotificationEventTypeType::NEW_MAIL_EVENT];
        $request->PushSubscriptionRequest = new PushSubscriptionRequestType();
        $request->PushSubscriptionRequest->EventTypes = $eventTypes;
        $request->PushSubscriptionRequest->URL = SpiceConfig::getInstance()->config['site_url']."/KREST/ewswebhooks/mailbox/" . $mailbox->id;

        // Search in the user's inbox.
        $folder_ids = new NonEmptyArrayOfBaseFolderIdsType();
        if ($mailbox->ews_folder) {
            $folder_ids->FolderId = new FolderIdType();
            $folder_ids->FolderId->Id = $mailbox->ews_folder->id;
        } else {
            $folder_ids->DistinguishedFolderId = new DistinguishedFolderIdType();
            $folder_ids->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::INBOX;
        }
        $request->PushSubscriptionRequest->FolderIds = $folder_ids;
        $request->PushSubscriptionRequest->StatusFrequency = $this->statusFrequency; // todo renew subscription

        $response = $mailbox->transport_handler->client->Subscribe($request);
        $subResponse = $response->ResponseMessages->SubscribeResponseMessage[0];

        $mailboxSettings = json_decode(html_entity_decode($mailbox->settings, ENT_QUOTES));
        $mailboxSettings->ews_subscriptionid = $subResponse->SubscriptionId;
        $mailboxSettings->ews_watermark = $subResponse->Watermark;
        $mailbox->settings = json_encode($mailboxSettings);

        return $subResponse->ResponseCode;
    }

    /**
     * Checks if the mailbox has a EWS subscription and if it needs a renewal of the subscription.
     *
     * @param Mailbox $mailbox
     * @throws \Exception
     */
    public function handleSubscription(Mailbox $mailbox) {
        // todo if there's a subscription check if last_checked date of the mailbox older as the notification interval(fetchFrequency) is
        if ($mailbox->hasEwsSubscription()) {
            if ($this->mailboxNeedsCheck($mailbox)) {
                // todo in that case renew the subscription and do a fetch
                $this->subscribe($mailbox);
            }
        }
    }

    /**
     * Checks if last_checked date of the mailbox older as the notification interval(StatusFrequency) is.
     *
     * @param Mailbox $mailbox
     * @return bool
     * @throws \Exception
     */
    private function mailboxNeedsCheck(Mailbox $mailbox) {
        $frequencyDate = new DateTime();
        $frequencyDate->sub(new DateInterval('PT' . $this->statusFrequency . 'M'));
        $lastCheckedDate = new DateTime($mailbox->last_checked);

        if ($lastCheckedDate >= $frequencyDate) {
            return false;
        }

        return true;
    }
}
