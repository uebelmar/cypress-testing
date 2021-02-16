<?php

namespace SpiceCRM\includes\SpiceCRMExchange\Connectivity;

use Exception;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfNotificationEventTypesType;
use jamesiarmes\PhpEws\Enumeration\NotificationEventTypeType;
use jamesiarmes\PhpEws\Request\SubscribeType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\PushSubscriptionRequestType;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\FolderHandlers\ExchangeCalendar;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeModules;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeClient;
use SpiceCRM\data\BeanFactory;
use DateTime;
use DateInterval;
use DateTimeZone;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Configurator\Configurator;

class SpiceCRMExchangeSubscriptions
{
    public $user;
    public $folderid;
    const STATUS_FREQUENCY = 15;
    public $subscriptionId;
    private $lastActive;

    public function __construct($user, $folderid)
    {
        // set the user
        $this->user = $user;
        $this->folderid = $folderid;

        // find out exchange user name
        $username = $user->user_name; // default
        if(isset(SpiceConfig::getInstance()->config['SpiceCRMExchange']['username_location'])){
            $username = $user->{SpiceConfig::getInstance()->config['SpiceCRMExchange']['username_location']};
        }
        if(isset($GLOBALS['spice_config']['SpiceCRMExchange']['username_location'])){
            $username = $user->{$GLOBALS['spice_config']['SpiceCRMExchange']['username_location']};
        }

        // create a client and impersonate the set user
        $this->client = new SpiceCRMExchangeClient($username);

        // check if we have a subscription ID
        $this->subscriptionId = $this->getSubscriptionId();

        $this->lastActive = $this->getLastActiveDate();
    }


    /**
     * checks if a susbcirpion is registered
     *
     * @param $subscriptionId
     * @return array
     */
    public static function checkSubscription($subscriptionId) {
        $db = DBManagerFactory::getInstance();
        $subscription = $db->fetchByAssoc($db->query("SELECT * FROM sysexchangeusersubscriptions WHERE subscriptionid='{$subscriptionId}'"));
        return $subscription;
    }

    /**
     * Checks if a folder is subscribed for EWS synchronization.
     *
     * @param $folderId
     * @param $userId
     * @return array
     */
    public static function folderHasSubscription($folderId, $userId) {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT * FROM sysexchangeusersubscriptions WHERE `folder_id`='{$folderId}' AND `user_id`='{$userId}'";
        $subscription = $db->fetchByAssoc($db->query($sql));
        return $subscription;
    }

    /**
     * subscribe
     */
    public function subscribe()
    {
        // 
        // get sugar_config this way.
        // Somehow parameter for ['SpiceCRMExchange']['callback_url'] written in config_override.php gets lost.
        $sysconfig = new Configurator();

        // Build the request.
        $request = new SubscribeType();

        $eventTypes = new NonEmptyArrayOfNotificationEventTypesType();
        $eventTypes->EventType = [
            NotificationEventTypeType::CREATED_EVENT,
            NotificationEventTypeType::DELETED_EVENT,
            NotificationEventTypeType::FREE_BUSY_CHANGED_EVENT,
            NotificationEventTypeType::MODIFIED_EVENT,
        ];
        $request->PushSubscriptionRequest = new PushSubscriptionRequestType();
        $request->PushSubscriptionRequest->EventTypes = $eventTypes;

        if (isset($sysconfig->config['SpiceCRMExchange']['callback_url'])) {
            $request->PushSubscriptionRequest->URL = "{$sysconfig->config['SpiceCRMExchange']['callback_url']}/KREST/ewswebhooks/handler";
        } else {
            $request->PushSubscriptionRequest->URL = "{$sysconfig->config['site_url']}/KREST/ewswebhooks/handler";
        }

        // Search in the user's inbox.
        $folder_ids = new NonEmptyArrayOfBaseFolderIdsType();
        $folder_ids->DistinguishedFolderId = new DistinguishedFolderIdType();
        $folder_ids->DistinguishedFolderId->Id = $this->folderid;
        $request->PushSubscriptionRequest->FolderIds = $folder_ids;
        $request->PushSubscriptionRequest->StatusFrequency = ($sysconfig->config['SpiceCRMExchange']['PushSubscriptionRequest']['StatusFrequency'] ?? self::STATUS_FREQUENCY);

        $response = $this->client->request('Subscribe', $request);
        $subResponse = $response->ResponseMessages->SubscribeResponseMessage[0];

        if ($subResponse->ResponseCode == 'NoError') {
            $this->deleteSubscription();
            $this->updateSubscription($subResponse->SubscriptionId, $subResponse->Watermark);
        }
        return $subResponse->ResponseCode;
    }

    public function unsubscribe()
    {
        if ($this->subscriptionId) {
            $this->deleteSubscription();
            return true;
        }

        return false;
    }

    /**
     * Returns a list of all EWS folders available for subscription.
     *
     * @return array
     */
    public static function getSubscriptionFolders() {
        return ['calendar', 'tasks', 'contacts'];
    }

    /**
     * checks if a subscripotion is active for the folder and user and returns the subscription ID
     *
     * @return mixed
     */
    private function getSubscriptionId()
    {
        $db = DBManagerFactory::getInstance();

        $res = $db->fetchByAssoc($db->query("SELECT subscriptionid FROM sysexchangeusersubscriptions WHERE user_id='{$this->user->id}' AND folder_id='{$this->folderid}'"));
        return $res['subscriptionid'];
    }

    /*
     * update subscription
     *
     * todo remove the old subscription
     */
    private function updateSubscription($subscriptionID, $watermark)
    {
        global $timedate;
$db = DBManagerFactory::getInstance();
        $db->query("INSERT INTO sysexchangeusersubscriptions (subscriptionid, watermark, user_id, folder_id, last_active) VALUES('$subscriptionID', '$watermark', '{$this->user->id}', '$this->folderid', '{$timedate->nowDb()}')");
    }

    /**
     * Updates the watermark and timestamp after receiving a heartbeat notification from EWS.
     *
     * @param $subscriptionID
     * @param $watermark
     */
    public static function updateHeartbeat($subscriptionID, $watermark) {
        global $timedate;
$db = DBManagerFactory::getInstance();
        $db->query("UPDATE sysexchangeusersubscriptions SET watermark='{$watermark}', last_active='{$timedate->nowDb()}'
                        WHERE subscriptionid='{$subscriptionID}'");
    }

    /*
     * delete subscription
     */
    private function deleteSubscription()
    {
        $db = DBManagerFactory::getInstance();
        $db->query("DELETE FROM sysexchangeusersubscriptions WHERE subscriptionid = '{$this->subscriptionId}'");
    }

    /**
     * Resynchronizes all subscriptions.
     *
     * @throws Exception
     */
    public static function resyncAll() {
        global $timedate;
$db = DBManagerFactory::getInstance();
        $frequency = (SpiceConfig::getInstance()->config['SpiceCRMExchange']['PushSubscriptionRequest']['StatusFrequency'] ?? self::STATUS_FREQUENCY);
        $flatlineTimeZone = new DateTimeZone('UTC');
        $flatlineDate = new DateTime();
        $flatlineDate->setTimezone($flatlineTimeZone);
        $flatlineInterval = new DateInterval('PT' . ($frequency - 1) . 'M');
        $flatlineDate->sub($flatlineInterval);
        $flatlineDateString = $flatlineDate->format($timedate->get_db_date_time_format());
        $sql = "SELECT * FROM sysexchangeusersubscriptions WHERE last_active<'{$flatlineDateString}'";
        $query = $db->query($sql);

        while($row = $db->fetchRow($query)) {
            $user = BeanFactory::getBean('Users', $row['user_id']);
            $subscription = new SpiceCRMExchangeSubscriptions($user, $row['folder_id']);
            $subscription->subscribe();
            $subscription->resyncEwsItems();
        }
    }

    /**
     * Resynchronizes the EWS objects.
     * All the objects since the last_active date are fetched and updated in SpiceCRM.
     *
     * @throws Exception
     */
    public function resyncEwsItems() {
        $lastActiveDate = $this->lastActive;

        if (isset($lastActiveDate)) {
            $startDate = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                date('Y-m-d 00:00:00', strtotime($lastActiveDate))
            );
        }

        $endDate = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            date('Y-m-t 23:59:59')
        );

        $calendar = new ExchangeCalendar($this->user);

        try {
            $results = $calendar->getMissedEwsEvents($startDate, $endDate);

            foreach ($results as $calendarItem) {
                try {
                    $ewsModule = $this->findModuleForEws($calendarItem);
                } catch (Exception $e) {
                    $ewsModule = $this->findModuleHandlerBySubcription($this, $calendarItem->itemId['id']);
                }

                $ewsBean = new $ewsModule($this->user);
                $bean = $ewsBean->mapEWSToBean($calendarItem);
                $bean->processed = true;
                $bean->save();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Forces the resynchronization of the current subscription.
     * First by subscribing again, followed by the resynchronization of the EWS objects.
     *
     * @throws Exception
     */
    public function forceResync() {
        $this->subscribe();
        $this->resyncEwsItems();
    }

    /**
     * Returns the last_active date for the current subscription.
     *
     * @return mixed
     */
    private function getLastActiveDate() {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT last_active FROM sysexchangeusersubscriptions WHERE subscriptionid='{$this->subscriptionId}'";
        $query = $db->query($sql);
        $result = $db->fetchByAssoc($query);

        return $result['last_active'];
    }

    /**
     * findModuleForEws
     *
     * Tries to find the module the incoming EWS object id belongs to.
     *
     * @param $itemId
     * @return mixed
     * @throws Exception
     */
    private function findModuleForEws($item) {
        if (!empty($item->ExtendedProperty)) {
            $extendedFields = json_decode($item->ExtendedProperty[0]->Value);

            if (isset($extendedFields->_module)) {
                return SpiceCRMExchangeModules::getModuleHandler($extendedFields->_module);
            }
        }
        throw new Exception('Module not found');
//        return $this->findBeanByExternalId($item->ItemId->Id);
    }

    private function findModuleHandlerBySubcription($subscription, $externalId) {
        $db = DBManagerFactory::getInstance();

        $sql = "SELECT `module_handler`
                FROM sysexchangeusersubscriptions `seus`
                JOIN sysexchangemappingmodules `semm` ON `semm`.`exchange_object`=`seus`.`folder_id`
                WHERE `seus`.`subscriptionid`='" . $subscription['subscriptionid'] . "'
                AND `seus`.`user_id`='" . $subscription['user_id'] . "'";
        $query = $db->query($sql);
        while ($row = $db->fetchRow($query)) {
            $user = BeanFactory::getBean('Users', $subscription['user_id']);
            if ($row['module_handler'] == "\SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers\SpiceCRMExchangeContacts") {
                $existsBeanWithExternalId = $row['module_handler']::staticExistsBeanWithExternalId($externalId);
            } else {
                $moduleHandler = new $row['module_handler']($user);
                $existsBeanWithExternalId = $moduleHandler->existsBeanWithExternalId($externalId);
            }

            if ($existsBeanWithExternalId) {
                return $row['module_handler'];
            }
        }
    }
}
