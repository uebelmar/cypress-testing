<?php
namespace SpiceCRM\includes\SpiceCRMExchange\Connectivity;

use SpiceCRM\includes\database\DBManager;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\modules\Users\User;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeModules;

/**
 * Class SpiceCRMExchangeUserSyncConfigController
 *
 * @package SpiceCRM\includes\SpiceCRMExchange
 */
class SpiceCRMExchangeUserSyncConfigController
{
    /**
     * @var DBManager $db Database manager
     */
    private $db;
    /**
     * @var array $subscriptions An array with EWS folders and their pending subscription actions. Used on per User basis.
     */
    private $subscriptions = [];

    /**
     * Action constants for the EWS folders in the $subscriptions attribute.
     */
    const ACTION_DO_NOTHING  = 'do_nothing';
    const ACTION_SUBSCRIBE   = 'subscribe';
    const ACTION_UNSUBSCRIBE = 'unsubscribe';
    const ACTION_EXISTING    = 'existing';

    /**
     * SpiceCRMExchangeUserSyncConfigController constructor.
     */
    public function __construct() {
        $db = DBManagerFactory::getInstance();
        $this->db = $db;
        $this->resetSubscriptions();
    }

    /**
     * The function processing adding and removing EWS synchronizations according to the data in the user synchronization
     * config, or the status of the User.
     */
    public function processSynchronizations() {
        $this->resyncExchange();
        $this->processInactiveUsers();
    }

    /**
     * Resets the $subscriptions attribute to contain an array of all EWS subscription folders with their actions
     * set to do nothing. Used in the constructor and after processing a user sync config.
     */
    private function resetSubscriptions() {
        foreach (SpiceCRMExchangeSubscriptions::getSubscriptionFolders() as $folder) {
            $this->subscriptions[$folder] = self::ACTION_DO_NOTHING;
        }
    }

    /**
     * Processes the user sync config and adds or removes EWS folder subscriptions as needed.
     */
    private function resyncExchange() {
        foreach ($this->getAllUserConfigs() as $syncConfig) {
            if ($syncConfig->isUserActive()) {
                $this->prepareSubscriptions($syncConfig);
                $this->processSubscriptions($syncConfig->user);
                $this->resetSubscriptions();
            } else {
                $this->removeOrphanedSubscriptions($syncConfig->user);
                $syncConfig->deleteConfig();
            }
        }
    }

    /**
     * Processes a list of inactive Spice Users and removes any EWS subscriptions they might have.
     */
    private function processInactiveUsers() {
        foreach ($this->getInactiveExchangeUsers() as $user) {
            $this->removeOrphanedSubscriptions($user);
        }
    }

    /**
     * Returns an array of all user sync configs from the DB.
     *
     * @return array An array of SpiceCRMExchangeUserSyncConfig objects
     */
    private function getAllUserConfigs() {
        $userConfigs = [];
        $sql = "SELECT DISTINCT `user_id` FROM sysexchangeuserconfig";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchRow($result)) {
            $userConfigs[] = new SpiceCRMExchangeUserSyncConfig($row['user_id']);
        }

        return $userConfigs;
    }

    /**
     * Iterates over all Module synchronizations for a User and marks the EWS folder subscriptions for
     * adding or removal.
     *
     * @param SpiceCRMExchangeUserSyncConfig $syncConfig
     */
    private function prepareSubscriptions(SpiceCRMExchangeUserSyncConfig $syncConfig) {
        foreach (SpiceCRMExchangeUserSyncConfig::getSyncableModules() as $moduleId => $folder) {
            if ($syncConfig->isModuleSyncedInExchange($moduleId)) {
                $this->markSubscribed($folder, $syncConfig);
            } else {
                $this->markUnsubscribed($folder, $syncConfig);
            }
        }
    }

    /**
     * Iterates over the $subscriptions attribute and performs the adding or removal of EWS folder subscriptions.
     *
     * @param User $user
     */
    private function processSubscriptions(User $user) {
        foreach ($this->subscriptions as $folder => $action) {
            switch ($action) {
                case self::ACTION_SUBSCRIBE:
                    $this->addSubscription($user, $folder);
                    break;
                case self::ACTION_UNSUBSCRIBE:
                    $this->removeSubscription($user, $folder);
                    break;
                case self::ACTION_EXISTING:
                case self::ACTION_DO_NOTHING:
                default:
                    break;
            }
        }
    }

    /**
     * Iterates over all available subscribable EWS folders and removes all existing subscriptions for the given User.
     *
     * @param User $user
     */
    private function removeOrphanedSubscriptions(User $user) {
        foreach (SpiceCRMExchangeSubscriptions::getSubscriptionFolders() as $folder) {
            if (SpiceCRMExchangeSubscriptions::folderHasSubscription($folder, $user->id)) {
                $this->removeSubscription($user, $folder);
            }
        }
    }

    /**
     * Returns an array of all inactive Users who have either EWS folder subscriptions or a user sync config.
     *
     * @return array An array of User objects
     */
    private function getInactiveExchangeUsers() {
        $inactiveUsers = [];
        $sql = "SELECT DISTINCT(u.id) FROM users u 
                JOIN sysexchangeusersubscriptions seus ON u.id=seus.user_id 
                WHERE u.deleted='1' OR u.status='Inactive'
                UNION
                SELECT u.id FROM users u 
                JOIN sysexchangeuserconfig seuc ON u.id=seuc.user_id
                WHERE u.deleted='1' OR u.status='Inactive'";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchRow($result)) {
            $inactiveUsers[] = BeanFactory::getBean('Users', $row['id']);
        }

        return $inactiveUsers;
    }

    /**
     * Marks an EWS folder in the $subscriptions attribute for subscribing if such a subscription does not exist.
     *
     * @param $folder
     * @param SpiceCRMExchangeUserSyncConfig $syncConfig
     */
    private function markSubscribed($folder, SpiceCRMExchangeUserSyncConfig $syncConfig) {
        if (!SpiceCRMExchangeSubscriptions::folderHasSubscription($folder, $syncConfig->user->id)) {
            $this->subscriptions[$folder] = self::ACTION_SUBSCRIBE;
        } else {
            $this->subscriptions[$folder] = self::ACTION_EXISTING;
        }
    }

    /**
     * Marks an EWS folder in the $subscriptions attribute for unsubscribing if such a subscriptions does exist
     * unless it was marked for subscribing for another Module.
     * Example: the Calls and Meetings Modules use the same calendar EWS folder
     *
     * @param $folder
     * @param SpiceCRMExchangeUserSyncConfig $syncConfig
     */
    private function markUnsubscribed($folder, SpiceCRMExchangeUserSyncConfig $syncConfig) {
        if (SpiceCRMExchangeSubscriptions::folderHasSubscription($folder, $syncConfig->user->id)) {
            if ($this->subscriptions[$folder] == self::ACTION_DO_NOTHING) {
                $this->subscriptions[$folder] = self::ACTION_UNSUBSCRIBE;
            }
        }
    }

    /**
     * Adds a subscription for a given User and an EWS folder if it has not existed yet.
     *
     * @param $user
     * @param $folder
     */
    private function addSubscription(User $user, $folder) {
        if (!SpiceCRMExchangeSubscriptions::folderHasSubscription($folder, $user->id)) {
            $subscription = new SpiceCRMExchangeSubscriptions($user, $folder);
            $subscription->subscribe();
        }
    }

    /**
     * Adds an EWS subscription for a given User and Spice Module ID.
     *
     * @param User $user
     * @param $moduleId
     * @throws Exception
     */
    public function addSubscriptionForModule(User $user, $moduleId) {
        $folder = SpiceCRMExchangeModules::getEwsFolderForModule($moduleId);
        $this->addSubscription($user, $folder);
    }

    /**
     * Removes a subscription for a given User and an EWS folder.
     *
     * @param $user
     * @param $folder
     */
    private function removeSubscription(User $user, $folder) {
        $subscription = new SpiceCRMExchangeSubscriptions($user, $folder);
        $subscription->unsubscribe();
    }

    /**
     * Removes an EWS subscription for a given User and Spice Module ID.
     *
     * @param User $user
     * @param $moduleId
     * @throws Exception
     */
    public function removeSubscriptionForModule(User $user, $moduleId) {
        $ewsFolder = SpiceCRMExchangeModules::getEwsFolderForModule($moduleId);

        if ($this->isSubscriptionRemovable($user, $moduleId, $ewsFolder)) {
            $this->removeSubscription($user, $ewsFolder);
        }
    }

    /**
     * Checks if a subscription is removable.
     * Uses in cases when multiple Spice Modules are subscribed with the same EWS folder.
     * eg. Calls and Meetings.
     *
     * @param User $user
     * @param $moduleId
     * @param $ewsFolder
     * @return bool
     * @throws Exception
     */
    private function isSubscriptionRemovable(User $user, $moduleId, $ewsFolder) {
        // todo make a check for the calls/meeting case
        $isRemovable = true;
        $userSyncConfig = new SpiceCRMExchangeUserSyncConfig($user->id);
        $syncedModules  = $userSyncConfig->getConfig();
        foreach ($syncedModules as $syncedModule) {
            if ($syncedModule['sysmodule_id'] == $moduleId) {
                continue;
            }

            try {
                if ($ewsFolder == SpiceCRMExchangeModules::getEwsFolderForModule($syncedModule['sysmodule_id'])) {
                    $isRemovable = false;
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return $isRemovable;
    }
}

