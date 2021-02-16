<?php
namespace SpiceCRM\modules\GoogleCalendar;

use Exception;
use ReflectionException;
use SpiceCRM\data\BeanFactory;
use DateTime;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\authentication\GoogleAuthenticate\GoogleAuthenticate;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMGsuite\Mappings\SpiceCRMGsuiteModules;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\GoogleOAuth\GoogleOAuthImpersonation;
use SpiceCRM\includes\authentication\AuthenticationController;


class GoogleCalendar
{

    public $userid;

    /**
     * the calendar ID
     *
     * @var string
     */
    public $calendarId;

    /**
     * the access token
     *
     * @var array|mixed
     */
    public $accesstoken;

    /**
     * GoogleCalendar constructor.
     *
     * @param string $calendarId
     */
    public function __construct($userid = null, $calendarId = 'primary') {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $this->userid = $userid ?: $current_user->id;

        $this->calendarId = $calendarId;

        if(SpiceConfig::getInstance()->config['googleapi']['serviceuserkey']) {
            if(!$_SESSION['google_oauth'] || $_SESSION['google_oauth']['user_id'] != $this->userid || $_SESSION['google_oauth']['expires_at'] < time()){
                $googleAuthController = new GoogleAuthenticate();
                $_SESSION['google_oauth'] = (array)$googleAuthController->getTokenByUserId($userid);
                $_SESSION['google_oauth']['expires_at'] = time() + (int) $_SESSION['google_oauth']['expires_in'];
                $_SESSION['google_oauth']['user_id'] = $this->userid;
                unset($googleAuthController);
            }
            $this->accesstoken = $_SESSION['google_oauth'];
        } else if($_SESSION['google_oauth']){
            $this->accesstoken = $_SESSION['google_oauth'];
        }
    }

    /**
     * checks if the service user can access the users calendar
     *
     * @return bool
     */
    public function checkAccess(){
        $apiUrl = "https://www.googleapis.com/calendar/v3/calendars/primary";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token']
            ],
        ]);

        $response = json_decode(curl_exec($curl));
        return $response->kind == 'calendar#calendar' ? true : false;
    }

    /**
     * synchronize
     *
     * Synchronizes all Events to and from Google Calendar
     *
     * @return mixed
     * @throws Exception
     */
    public function synchronize() {
        $result['gcal2spice'] = $this->syncGcal2Spice();
        $result['spice2gcal'] = $this->syncSpice2Gcal();
        return $result;
    }

    /**
     * syncSpice2Gcal
     *
     * Synchronizes Events from SpiceCRM to Google Calendar.
     *
     * @return array
     */
    public function syncSpice2Gcal() {
        try {
            foreach (self::getEventImplementations() as $implementation) {
                foreach ($this->getAllInstances($implementation['class']) as $bean) {
                    if (empty($bean->external_id)) {
                        $this->createEvent($bean);
                    }
                }
            }

            return [
                'result' => true,
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }

    private function getUserSettings($userId){
        $db = DBManagerFactory::getInstance();

        $userSettings = $db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteuserconfig WHERE user_id ='$userId' AND scope='Calendar'"));
        return $userSettings;
    }

    private function setUserSyncToken($userId, $syncToken){
        $db = DBManagerFactory::getInstance();

        $userSettings = $db->fetchByAssoc($db->query("UPDATE sysgsuiteuserconfig SET sync_token='$syncToken' WHERE user_id ='$userId' AND scope='Calendar'"));
        return $userSettings;
    }

    /**
     * syncGcal2Spice
     *
     * Synchronizes Events from Google Calendar to SpiceCRM.
     *
     * @return mixed
     * @throws Exception
     */
    public function syncGcal2Spice($userid = null) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $optParams = [];

        //if no user id is passed in we assume the current user
        if(!$userid) $userid = $current_user->id;

        // load the user settings
        $userSettings = $this->getUserSettings($userid);
        if(!$userSettings) return false;

        if ($userSettings['sync_token'] != null) {
            $optParams['syncToken'] = $userSettings['sync_token'];
        } else {
            // no sync token present -> limit the sync to the last 6 months
            $dueDate = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime('-1 months')));
            $optParams['timeMin'] = $dueDate->format(DateTime::RFC3339);
        }

        $request = $this->listEventsRequest('primary', $optParams);
        foreach ($request->items as $item) {
            try {
                $event = new GoogleCalendarEvent($item);
                $this->saveEvent($event);
            } catch (Exception $e) {
                // todo maybe log it?
                continue;
            }
        }

        if($request->nextSyncToken){
            $this->setUserSyncToken($userid, $request->nextSyncToken);
        }

        return $request;
    }

    /**
     * getAllInstances
     *
     * Returns all Instances of a class linked to the current user.
     *
     * @param $class
     * @return array
     */
    public function getAllInstances($class) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $instances = [];
        $table     = $class->table_name;
        $sql       = "SELECT id FROM " . $table . " WHERE assigned_user_id='" . $current_user->id . "'";
        $q         = $db->query($sql);
        $result    = $db->fetchByAssoc($q);
        foreach ($result as $item) {
            array_push($instances, BeanFactory::getBean($class::module_dir, $item['id']));
        }

        return $instances;
    }

    /**
     * getEventImplementations
     *
     * Returns an array of all Bean classes which implement the Google Calendar Event Interface
     *
     * @return array
     * @throws ReflectionException
     */
    public static function getEventImplementations() {
        return [
            [
                'module' => 'Calls',
                'class'  => 'Call',
            ],
            [
                'module' => 'Meetings',
                'class'  => 'Meeting',
            ],
        ];
    }

    /**
     * saveEvent
     *
     * Saves a Google Calendar Event as a Bean.
     *
     * @param GoogleCalendarEvent $event
     * @throws Exception
     */
    private function saveEvent(GoogleCalendarEvent $event) {
        $bean = $this->beanExists($event);

        if ($bean) {
            $handlerClass = SpiceCRMGsuiteModules::getModuleHandler($bean->module_name);
            $handler = new $handlerClass($bean);
            $handler->gsuiteToBean($event);
        }
    }

    /**
     * beanExists
     *
     * Checks if a Bean exists for a given Google Calendar Event
     * and returns that Bean if present.
     *
     * @param GoogleCalendarEvent $event
     * @return bool|SugarBean
     * @throws ReflectionException
     */
    private function beanExists(GoogleCalendarEvent $event) {
        foreach (self::getEventImplementations() as $implementation) {
            $bean      = BeanFactory::getBean($implementation['module']);
            $tableName = $bean->getTableName();

            $db = DBManagerFactory::getInstance();
            $query = "SELECT id FROM " . $tableName . " WHERE external_id = '" . $event->id . "'";
            $q = $db->query($query);
            $result = $db->fetchByAssoc($q);

            if ($result['id']) {
                return BeanFactory::getBean($implementation['module'], $result['id']);
            }
        }

        return false;
    }

    /**
     * createEvent
     *
     * Creates or updates an event in Google Calendar
     *
     * @param $bean
     * @return Exception|GoogleCalendarEvent
     * @throws Exception
     */
    public function createEvent($bean) {
        $handlerClass = SpiceCRMGsuiteModules::getModuleHandler($bean->module_name);
        $gsuiteHandler = new $handlerClass($bean);
        $event = $gsuiteHandler->beanToGsuite();

        if ($event) {
            $config = GSuiteUserConfig::getCurrentUserConfig();
            $this->calendarId = $config->getCalendarForBean(get_class($bean));

            try {
                if ($bean->external_id) {
                    $event = $this->updateRequest($event);
                } else {
                    $event = $this->insertRequest($event);
                }

                return $event;
            } catch (Exception $e) {
                return $e;
            }
        }

        return null;
    }

    /**
     * removeEvent
     *
     * Removes an event from Google Calendar
     *
     * @param $bean
     * @throws Exception
     */
    public function removeEvent($bean) {
        if ($bean->external_id == null) {
            return true;
            // throw new \Exception('Missing Event ID.');
        }

        $config = GSuiteUserConfig::getCurrentUserConfig();
        $this->calendarId = $config->getCalendarForBean(get_class($bean));

        try {
            $this->deleteRequest($bean->external_id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * cURL Requests
     */

    /**
     * insertRequest
     *
     * Insert request for Google Calendar Events using cURL
     *
     * @param GoogleCalendarEvent $event
     * @return GoogleCalendarEvent
     * @throws Exception
     */
    private function insertRequest(GoogleCalendarEvent $event) {
        $apiUrl  = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId . '/events';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $event->serialize(),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token']
            ]
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);

        if (!$result) {
            throw new Exception('Cannot insert a new Google Calendar Event');
        }

        if (isset($result->error)) {
            throw new Exception($result->error->code . ': ' . $result->error->message);
        }

        $event = new GoogleCalendarEvent((array) $result);

        return $event;
    }

    /**
     * updateRequest
     *
     * Update request for Google Calendar Events using cURL
     *
     * @param GoogleCalendarEvent $event
     * @return GoogleCalendarEvent
     * @throws Exception
     */
    private function updateRequest(GoogleCalendarEvent $event) {
        $apiUrl  = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId . '/events/';
        $apiUrl .= $event->id;

        $payload = $event->serialize();

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token']
            ]
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);

        if (!$result) {
            throw new Exception('Cannot update a new Google Calendar Event');
        }

        if (isset($result->error)) {
            throw new Exception($result->error->code . ': ' . $result->error->message);
        }

        $event = new GoogleCalendarEvent((array) $result);

        return $event;
    }

    /**
     * deleteRequest
     *
     * Delete request for Google Calendar Events using cURL
     *
     * @param $eventId
     */
    private function deleteRequest($eventId) {
        $apiUrl  = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId . '/events/';
        $apiUrl .= $eventId;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token']
            ],
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);
    }

    /**
     * getAllCalendars
     *
     * Returns a list of all calendars for the current user's Google Calendar Account.
     *
     * @param bool $is_owner
     * @return array
     * @throws Exception
     */
    public function getAllCalendars($username, $is_owner = true) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $results = [];

        if ($is_owner) {
            $params = '?' . http_build_query([
                    'minAccessRole' => 'owner',
                ]);
        }
        $apiUrl = "https://www.googleapis.com/calendar/v3/users/me/calendarList" . $params;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token']
            ],
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);

        if (isset($result->error)) {
            throw new Exception($result->error->code . ': ' . $result->error->message);
        }

        if (!$result) {
            throw new Exception('Cannot retrieve the list of Calendars');
        }

        $current_user->saveGcalSyncToken($result->nextSyncToken);

        foreach ($result->items as $calendar) {
            $results[] = [
                'id'   => $calendar->id,
                'name' => $calendar->summary,
            ];
        }

        return $results;
    }

    /**
     * listEventsRequest
     *
     * Returns a list of events for the currently set Calendar
     *
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function listEventsRequest($calendarid, $params) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $apiUrl = "https://www.googleapis.com/calendar/v3/calendars/$calendarid/events";

        if($params) $apiUrl = $apiUrl . '?' . http_build_query($params);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token']
            ],
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);

        if (isset($result->error)) {
            throw new Exception($result->error->code . ': ' . $result->error->message);
        }

        if (!$result) {
            throw new Exception('Cannot retrieve the list of Calendars');
        }

        //$current_user->saveGcalSyncToken($result->nextSyncToken);

        return $result;
    }

    /**
     * beforeSaveHook
     *
     * Saves a Bean as a Google Calendar Event.
     *
     * @param $bean
     * @param $event
     * @param $arguments
     */
    public function beforeSaveHook(&$bean, $event, $arguments) {
        

        if($GLOBALS['gsuiteinbound']) return true;

        //
        if(SpiceConfig::getInstance()->config['googleapi']['serviceuserkey']) {
            $googleAuthController = new GoogleAuthenticate();
            $_SESSION['google_oauth'] = (array)$googleAuthController->getToken();
        }

        if (isset($_SESSION['google_oauth'])) {
            try {
                if ($bean->status == 'Cancelled') {
                    $this->removeEvent($bean);
                } else {
                    $calendarEvent     = $this->createEvent($bean);
                    $bean->external_id = $calendarEvent->id;
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * beforeDeleteHook
     *
     * Remove the Google Calendar Event and remove the external_id from Bean.
     *
     * @param $bean
     * @param $event
     * @param $arguments
     * @throws Exception
     */
    public function beforeDeleteHook(&$bean, $event, $arguments) {
        if($GLOBALS['gsuiteinbound']) return true;

        // Remove the GCal event
        if (isset($_SESSION['google_oauth'])) {
            try {
                $this->removeEvent($bean);
            } catch (Exception $e) {
                throw $e;
            }

            $bean->removeGcalId();
        }
    }

    /**
     * startSubscription
     *
     * starts a calendar subscription for the given User identified by the User Name
     *
     * @throws Exception
     */
    public function startSubscription() {
        

        $apiUrl = "https://www.googleapis.com/calendar/v3/calendars/primary/events/watch";
        $params = [
            'id'         => create_guid(),
            'type'       => 'web_hook',
            'address'    => SpiceConfig::getInstance()->config['googleapi']['notificationhost']
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_HTTPHEADER     => [
                "Content-Type: application/json",
                "Authorization: Bearer {$this->accesstoken['access_token']}"
            ]
        ]);

        $response = json_decode(curl_exec($curl));

        if (isset($response->error)) {
            return $response->error->code . ': ' . $response->error->message;
        }

        // save the subscription
        $this->saveSubscription($response);

        // close the cURL
        curl_close($curl);

        return true;
    }


    /**
     * saves the subscription information
     *
     * @param $params
     * @throws Exception
     */
    private function saveSubscription( $params) {
        global $timedate;
$db = DBManagerFactory::getInstance();

        $expirationDate = date($timedate->get_db_date_time_format(), ((int) $params->expiration) / 1000);

        $db->query("INSERT INTO sysgsuiteusersubscriptions (subscriptionid, resourceid, user_id, expiration) VALUES('{$params->id}', '{$params->resourceId}', '{$this->userid}', '$expirationDate')");
    }

    /**
     * startSubscription
     *
     * starts a calendar subscription for the given User identified by the User Name
     *
     * @throws Exception
     */
    public function stopSubscription() {
        $db = DBManagerFactory::getInstance();

        // get the user config
        $userConfig = $db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteusersubscriptions WHERE user_id='{$this->userid}'"));

        $apiUrl = "https://www.googleapis.com/calendar/v3/channels/stop";
        $params = [
            'id'         => $userConfig['subscriptionid'],
            'resourceId'       => $userConfig['resourceid']
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_HTTPHEADER     => [
                "Content-Type: application/json",
                "Authorization: Bearer {$this->accesstoken['access_token']}"
            ]
        ]);

        $response = json_decode(curl_exec($curl));

        if (isset($response->error)) {
            return $response->error->code . ': ' . $response->error->message;
        }

        // save the subscription
        $db->query("DELETE FROM sysgsuiteusersubscriptions WHERE subscriptionid = '{$userConfig['subscriptionid']}'");

        // close the cURL
        curl_close($curl);

        return true;
    }

    /**
     * startSync
     *
     * Enables push notifications from Google Calendar for all calendars of the current user
     *
     * @throws Exception
     */
    public function startSync() {
        try {
            $calendars = $this->getAllCalendars();
        } catch (Exception $e) {
            throw $e;
        }

        $result = [
            'result' => true,
            'error'  => '',
        ];

        foreach ($calendars as $calendar) {
            $this->calendarId = $calendar['id'];
            $response = $this->enableNotifications();

            if ($response['result'] == false) {
                $result['result']  = false;
                $result['error']  .= $response['error'];
            }
        }

        return $result;
    }

    /**
     * enableNotifications
     *
     * Enables push notifications from Google Calendar for the currently set calendar ID.
     *
     * @return array
     * @throws Exception
     */
    public function enableNotifications() {
        

        $apiUrl = "https://www.googleapis.com/calendar/v3/calendars/" . $this->calendarId . "/events/watch";

        $params = [
            'id'         => create_guid(),
            'type'       => 'web_hook',
            'address'    => SpiceConfig::getInstance()->config['googleapi']['notificationhost']
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $_SESSION['google_oauth']['access_token'],
            ],
        ]);

        $response = json_decode(curl_exec($curl));

        if (isset($response->error)) {
            $result['error']  = $response->error->code . ': ' . $response->error->message;
            $result['result'] = false;
            return $result;
        }

        $result = $this->saveChannelInfo($response);

        curl_close($curl);

        return $result;
    }

    /**
     * getGoogleEvents
     *
     * Returns all Events for the current Calendar in the given time frame.
     * By default it returns all of them, but using $onlyGoogle parameter the Events can be filtered.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param $onlyGoogle
     * @return array
     * @throws ReflectionException
     */
    public function getGoogleEvents(DateTime $startDate, DateTime $endDate, $onlyGoogle = false) {
        $params = [
            'timeMin'      => $startDate->format(DateTime::RFC3339),
            'timeMax'      => $endDate->format(DateTime::RFC3339),
        ];

        $apiUrl  = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId . '/events?';
        $apiUrl .= http_build_query($params);


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token'],
            ],
        ]);

        $response = json_decode(curl_exec($curl));

        if (isset($response->error)) {
            throw new Exception($response->error->code . ': ' . $response->error->message);
        }

        curl_close($curl);
        $events = array_filter($response->items, function ($item) {return !isset($item->recurrence);});
        $events = $this->removeCancelledEvents($events);
        if ($onlyGoogle) {
            $events = $this->onlyGoogleEvents($events);
        }

        return $events;
    }

    /**
     * Removes cancelled events from the event list.
     * todo make sure "cancelled" is the correct status
     *
     * @param $events
     * @return mixed
     */
    private function removeCancelledEvents($events) {
        foreach ($events as $key  => $event) {
            if ($event->status == 'cancelled') {
                unset($events[$key]);
            }
        }
        return $events;
    }

    /**
     * Checks which events from the list are recurring events and replaces them with actual event intances.
     *
     * @param $events
     * @param $params
     * @return array
     * @throws Exception
     */
    private function handleRecurringEvents($events, $params) {
        foreach ($events as $key => $event) {
            if (isset($event->recurrence)) {
                $instances = $this->fetchRecurringInstances($event, $params);
                unset($events[$key]);
                $events = array_merge($events, $instances);
            }
        }
        return $events;
    }

    /**
     * Fetches all instances for a recurring Google Calendar event in a given time period.
     * The instances contain actual start/end dates and not just the first start/ends date and a recurrence pattern.
     *
     * @param $event
     * @param $params
     * @return mixed
     * @throws Exception
     */
    private function fetchRecurringInstances($event, $params) {
        $apiUrl  = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId . '/events/'
            . $event->id . '/instances?';
        $apiUrl .= http_build_query($params);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accesstoken['access_token'], //$_SESSION['google_oauth']['access_token'],
            ],
        ]);

        $response = json_decode(curl_exec($curl));

        if (isset($response->error)) {
            throw new Exception($response->error->code . ': ' . $response->error->message);
        }

        curl_close($curl);

        return $response->items;
    }

    /**
     * removeDuplicates
     *
     * Removes Events that are already present in the Spice CRM from a given array of Google Calendar Events.
     *
     * @param $items
     * @return mixed
     * @throws ReflectionException
     */
    public function removeDuplicates($items) {
        foreach ($items as $index => $item) {
            $event = new GoogleCalendarEvent($item);
            if ($this->beanExists($event)) {
                unset($items[$index]);
            }
        }

        // reindex the array
        $items = array_values($items);

        return $items;
    }

    /**
     * saveChannelInfo
     *
     * Saves the information about the notification channel into the gsuite user config.
     *
     * @param $params
     * @return array
     * @throws Exception
     */
    private function saveChannelInfo($params) {
        $result = [
            'result' => false,
            'error'  => 'Missing API Channel information',
        ];

        if ($params['kind'] == 'api#channel' && isset($params['id'])) {
            $config = GSuiteUserConfig::getCurrentUserConfig();
            $result = $config->saveChannelInfo($this->calendarId, $params['id']);
        }

        return $result;
    }

    /**
     * onlyGoogleEvents
     *
     * Filters the Events received from Google Calendar and returns only those Events,
     * that aren't stored in the SpiceCRM database.
     *
     * @param $events
     * @return array
     * @throws ReflectionException
     */
    private function onlyGoogleEvents($events) {
        $filteredEvents = [];

        foreach ($events as $event) {
            $calendarEvent = new GoogleCalendarEvent($event);
            if (!$this->beanExists($calendarEvent)) {
                array_push($filteredEvents, $event);
            }
        }

        return $filteredEvents;
    }

}
