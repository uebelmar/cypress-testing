<?php
namespace SpiceCRM\includes\SpiceCRMExchange\Connectivity;

use DBManager;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeModules;
use SpiceCRM\includes\authentication\AuthenticationController;
use User;

/**
 * Class SpiceCRMExchangeUserSyncConfig
 *
 * A class for reading and writing into the Exchange sync configuration.
 *
 * @package SpiceCRM\includes\SpiceCRMExchange
 */
class SpiceCRMExchangeUserSyncConfig
{
    /**
     * @var User $user The SpiceCRM  User.
     */
    public $user;
    /**
     * @var array $config An array with the Exchange sync configuration for the current user.
     */
    private $config = [];

    /**
     * @var DBManager $db An instance of the DB Manager.
     */
    private $db;

    /**
     * SpiceCRMExchangeUserSyncConfig constructor.
     * Sets the current User ID and preloads their Exchange sync configuration.
     *
     * @param $userId
     */
    public function __construct($userId = null) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $this->db = $db;
        $this->user = BeanFactory::getBean('Users', $userId ?: $current_user->id);
        $this->config = $this->getExchangeSyncConfig();
    }

    /**
     * Checks if a Spice Module is synced with Exchange for the current User.
     *
     * @param $moduleId
     * @return bool
     */
    public function isModuleSyncedInExchange($moduleId) {
        $isSynced = false;

        foreach ($this->config as $configItem) {
            if ($configItem['sysmodule_id'] == $moduleId) {
                $isSynced = true;
                break;
            }
        }

        return $isSynced;
    }

    /**
     * Sets a Spice module to be synced with Exchange.
     *
     * @param $sysmoduleid
     * @throws Exception
     */
    public function syncModuleInExchange($sysmoduleid) {
        $this->setModuleSyncInExchange($sysmoduleid, true);
        $this->config = $this->getExchangeSyncConfig();
    }

    /**
     * Sets a Spice module to be unsynced with Exchange.
     *
     * @param $moduleName
     * @throws Exception
     */
    public function unsyncModuleInExchange($moduleName) {
        $this->setModuleSyncInExchange($moduleName, false);
        $this->config = $this->getExchangeSyncConfig();
    }

    /**
     * Returns the Exchange sync config for the current User.
     *
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Deletes the EWS sync configuration for the current User.
     *
     * @return false|string
     */
    public function deleteConfig() {
        $sql = "DELETE FROM sysexchangeuserconfig WHERE `user_id`='" . $this->user->id . "'";
        $this->db->query($sql);
        return $this->db->lastDbError();
    }

    /**
     * Checks if the current User hasn't been deleted or set as inactive.
     *
     * @return bool
     */
    public function isUserActive() {
        if ($this->user->deleted == 1 || $this->user->status == 'Inactive') {
            return false;
        }

        return true;
    }

    /**
     * Returns an array with Spice modules mapped to the EWS folders.
     *
     * @return array
     */
    public static function getSyncableModules() {
        $db = DBManagerFactory::getInstance();

        $modules = [];
        $modulesObjects = $db->query("SELECT sm.id AS module_id, sm.module, sem.exchange_object FROM sysexchangemappingmodules sem, (select * from sysmodules UNION select * from syscustommodules) sm WHERE sem.sysmodule_id = sm.id");
        while($modulesObject = $db->fetchByAssoc($modulesObjects)){
            $modules[$modulesObject['module_id']] = $modulesObject['exchange_object'];
        }
        return $modules;

    }

    /**
     * Reads the Exchange sync config for the current User from the DB.
     * sysmodulename is needed in integration for old sugar 6.5
     * also planed to be used in setstatus KREST controller (see Meetings KREST for draft)
     * @return array
     */
    public function getExchangeSyncConfig() {
        $config = [];
        $sql = "SELECT uc.*, mm.exchange_object, mm.exchangesubscription, mm.outlookaddenabled, sm.sm_module sysmodulename 
FROM sysexchangeuserconfig uc
LEFT JOIN sysexchangemappingmodules mm ON uc.sysmodule_id = mm.sysmodule_id
LEFT JOIN sysexchangeusersubscriptions us ON us.user_id = uc.user_id
LEFT JOIN (SELECT id AS sm_id, module sm_module FROM sysmodules UNION SELECT id AS sm_id, module sm_module FROM syscustommodules ) sm ON sm.sm_id = uc.sysmodule_id
WHERE  uc.user_id='{$this->user->id}' GROUP BY uc.sysmodule_id, uc.user_id";

        $result = $this->db->query($sql);
        while($cRecord = $this->db->fetchByAssoc($result)){
            $config[] = $cRecord;
        }
        return $config;
    }

    /**
     * Sets a boolean value for a sync flag for a Spice module.
     * Used for syncing or unsyncing a module.
     *
     * @param $sysmoduleid
     * @param $syncValue
     * @return bool
     * @throws Exception
     */
    private function setModuleSyncInExchange($sysmoduleid, $syncValue) {
        $configController = new SpiceCRMExchangeUserSyncConfigController();
        if ($syncValue) {
            $sql = "INSERT INTO sysexchangeuserconfig (`id`, `user_id`, `sysmodule_id`) VALUES ('".create_guid()."', '{$this->user->id}', '$sysmoduleid')";
            $this->db->query($sql);

            if (SpiceCRMExchangeModules::isModuleSyncable($sysmoduleid, $this->user->id)) {
                $configController->addSubscriptionForModule($this->user, $sysmoduleid);
            }
        } else {
            $sql = "DELETE FROM sysexchangeuserconfig WHERE user_id='{$this->user->id}' AND sysmodule_id = '$sysmoduleid'";
            $this->db->query($sql);
            $configController->removeSubscriptionForModule($this->user, $sysmoduleid);
        }

        return true;
    }

    /**
     * Returns the DB field name for a sync flag for a specific module.
     *
     * @param $moduleName
     * @return string
     */
    private function getSyncFlag($moduleName) {
        return 'sync' . strtolower($moduleName);
    }
}
