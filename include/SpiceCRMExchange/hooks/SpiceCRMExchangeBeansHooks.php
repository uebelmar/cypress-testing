<?php
namespace SpiceCRM\includes\SpiceCRMExchange\hooks;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeModules;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeLogger;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

abstract class SpiceCRMExchangeBeansHooks {
    protected $syncClass;
    protected $beanSync;
    protected $moduleName;

    public function updateExchange(&$bean) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['hooks_enabled'] == false) {
            return;
        }

        $userId = $bean->assigned_user_id ?? $current_user->id;
        if (SpiceCRMExchangeModules::isModuleSyncable($this->getModuleId($this->moduleName), $userId)) {
            //if (SpiceCRMExchangeSubscriptions::folderHasSubscription($this->folderId, $current_user->id)) {
                $logger = new SpiceCRMExchangeLogger();
                $logger->logOutboundRecord($bean, SpiceCRMExchangeLogger::REQUEST_TYPE_REQUESTED);

                $this->initializeBeanSync($bean);
                if (empty($bean->external_id)) {
                    $this->beanSync->createOnExchange();
                } else {
                    $this->beanSync->updateOnExchange();
                }
            //}
        }
    }

    public function deleteExchange (&$bean) {
        
        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['hooks_enabled'] == false) {
            return;
        }

        $this->initializeBeanSync($bean);
        if (!empty($bean->external_id)) {
            $this->beanSync->deleteOnExchange();
        }
    }

    protected function initializeBeanSync(&$bean) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $this->beanSync = new $this->syncClass($current_user, $bean);
    }

    /**
     * Returns the ID of the Spice module with the given name.
     *
     * @param $moduleName
     * @return mixed
     */
    protected function getModuleId($moduleName) {
        $db = DBManagerFactory::getInstance();

        $sql = "SELECT id FROM sysmodules WHERE `module`='" . $moduleName . "'";
        $query = $db->query($sql);
        $row = $db->fetchRow($query);
        return $row['id'];
    }
}
