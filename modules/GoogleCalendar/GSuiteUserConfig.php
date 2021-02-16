<?php
namespace SpiceCRM\modules\GoogleCalendar;

use Exception;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class GSuiteUserConfig
{
    public $id;
    public $user_id;
    public $calendar_settings;
    public $beanMappings = [];
    public $fillable = ['id', 'user_id', 'calendar_settings'];
    protected $table = 'sysgsuiteuserconfig';

    public function __construct($user_id) {
        if ($user_id == '') {
            throw new Exception('No User ID given.');
        }

        $this->user_id = $user_id;

        $db = DBManagerFactory::getInstance();
        $query = "SELECT * FROM sysgsuiteuserconfig WHERE user_id = '"
                . filter_var($this->user_id, FILTER_SANITIZE_STRING) . "'";
        $q = $db->query($query);

        $result = $db->fetchByAssoc($q);

        if (!empty($result)) {
            foreach ($result as $attribute => $value) {
                if (in_array($attribute, $this->fillable)) {
                    $this->$attribute = $value;
                }
            }
        }

        if ($this->id == null) {
            $this->id = $this->generateUUID();
        }

        $this->initializeCalendarSettings();
    }

    /**
     * getCurrentUserConfig
     *
     * Returns the Gsuite configuration for the current user.
     *
     * @return GSuiteUserConfig
     * @throws Exception
     */
    public static function getCurrentUserConfig() {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        return new GSuiteUserConfig($current_user->id);
    }

    /**
     * savBeanMappings
     *
     * Saves the Bean to Google Calendar mappings into the Gsuite configuration.
     *
     * @param array $mappings
     */
    public function saveBeanMappings($mappings = []) {
        foreach ($mappings as $mapping) {
            if ($mapping['deleted']) {
                unset($this->beanMappings[$mapping['id']]);
            } else {
                $this->beanMappings[$mapping['id']] = $mapping;
            }
        }

        $this->save();
    }

    /**
     * save
     *
     * Saves the Gsuite configuration into the database.
     */
    public function save() {
        $this->serializeCalendarSettings();
        if ($this->exists()) {
            $this->update();
        } else {
            $this->insert();
        }

    }

    /**
     * getCalendarForBean
     *
     * Returns the Google Calendar that has been mapped to the given Bean.
     *
     * @param $beanClass
     * @return string
     */
    public function getCalendarForBean($beanClass) {
        foreach ($this->beanMappings as $mapping) {
            if ($mapping['bean'] == $beanClass) {
                return $mapping['calendar'];
            }
        }

        return 'primary';
    }

    /**
     * getBeanForCalendar
     *
     * Returns the Bean class that has been mapped to the given Google Calendar.
     *
     * @param $calendar
     * @return null
     */
    public function getBeanForCalendar($calendar) {
        foreach ($this->beanMappings as $mapping) {
            if ($mapping['calendar'] == $calendar) {
                return $mapping['bean'];
            }
        }

        return null;
    }

    /**
     * initializeCalendarSettings
     *
     * Decodes the Google Calendar configuration that is stored as JSON into object attributes.
     */
    private function initializeCalendarSettings() {
        $settings = json_decode(html_entity_decode($this->calendar_settings), true);

        if ($settings != null) {
            foreach ($settings as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * seriaizeCalendarSettings
     *
     * Serializes the Google Calendar settings into JSON for database storage.
     */
    private function serializeCalendarSettings() {
        $this->calendar_settings = json_encode([
            'beanMappings' => $this->beanMappings,
        ]);
    }

    /**
     * exists
     *
     * Checks if a Gsuite configuration for the current user exists.
     *
     * @return bool
     */
    private function exists() {
        $db = DBManagerFactory::getInstance();

        $query = "SELECT COUNT(*) as 'count' FROM sysgsuiteuserconfig WHERE user_id = '"
            . filter_var($this->user_id, FILTER_SANITIZE_STRING) . "'";
        $q = $db->query($query);

        $result = $db->fetchByAssoc($q);

        if ($result['count'] == 1) {
            return true;
        }

        return false;

    }

    /**
     * update
     *
     * Updates an existing Gsuite configuration.
     *
     * @return array
     */
    private function update() {
        $db = DBManagerFactory::getInstance();

        $values = [];

        foreach ($this->fillable as $attribute) {
            array_push($values, "`" . $attribute . "` = '" . $this->$attribute . "'");
        }

        $query = "UPDATE " . $this->table . " SET " . implode(',', $values)
                . " WHERE user_id = '" . $this->user_id . "'";

        $q = $db->query($query);
        $result = $db->fetchByAssoc($q);

        return $result;
    }

    /**
     * insert
     *
     * Inserts a new Gsuite configuration.
     *
     * @return array
     */
    private function insert() {
        $db = DBManagerFactory::getInstance();

        $values = [];

        foreach ($this->fillable as $attribute) {
            array_push($values, "'" . $this->$attribute . "'");
        }

        $query = "INSERT INTO " . $this->table . "(" . implode(',', $this->fillable) . ")"
                . " VALUES (" . implode(',', $values) . ")";

        $q = $db->query($query);
        $result = $db->fetchByAssoc($q);

        return $result;
    }

    /**
     * saveChannelInfo
     *
     * Saves the channel info for the Calendars that use push notifications from Google.
     *
     * @param $calendarId
     * @param $channelId
     * @return array
     */
    public function saveChannelInfo($calendarId, $channelId) {
        $this->beanMappings[$calendarId]['channelId'] = $channelId;
        $this->saveBeanMappings();

        return [
            'result' => true,
        ];
    }

    public function generateUUID() {
        $uuid = md5(uniqid(rand(), true));
        $guid =  substr($uuid,0,8)."-".
            substr($uuid,8,4)."-".
            substr($uuid,12,4)."-".
            substr($uuid,16,4)."-".
            substr($uuid,20,12);
        return $guid;
    }
}
