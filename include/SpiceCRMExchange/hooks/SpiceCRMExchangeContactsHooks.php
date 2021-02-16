<?php

namespace SpiceCRM\includes\SpiceCRMExchange\hooks;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\EwsConnectionException;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeModules;
use SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers\SpiceCRMExchangeContacts;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

class SpiceCRMExchangeContactsHooks
{
    protected $folderId = 'contacts';
    protected $moduleName = 'Contacts';

    /**
     * updates a users record for all subscribed users on Exchange
     *
     * INSERT INTO `syshooks` (`id`, `module`, `event`, `hook_index`, `hook_include`, `hook_class`, `hook_method`, `hook_active`, `description`, `version`, `package`) VALUES ('5CF00EDD-EC8D-4145-BBEC-71E97E0E4825', 'Contacts', 'before_save', 0, 'include/SpiceCRMExchange/hooks/SpiceCRMExchangeContactsHooks.php', '\SpiceCRM\includes\\SpiceCRMExchange\\hooks\\SpiceCRMExchangeContactsHooks', 'updateExchange', 1, NULL, '2019.12.001', 'exchange');
     *
     * @param $bean
     * @throws Exception
     * @throws EwsConnectionException
     */
    function updateExchange($bean) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['hooks_enabled'] == false) {
            return;
        }

//        $userId = $bean->assigned_user_id ?? $current_user->id;
        $userId = $current_user->id;

        if (SpiceCRMExchangeModules::isModuleSyncable($this->getModuleId($this->moduleName), $userId)) {
            $linkedUsers = $bean->get_linked_beans('user_sync', 'User');
            foreach($linkedUsers as $linkedUser){
                $contactSync = new SpiceCRMExchangeContacts($linkedUser, $bean);
                if (!empty($contactSync->external_id)) {
                    $contactSync->updateOnExchange();
                } else { // needed for sugar backward compatibility
                    $contactSync->createOnExchange();
                }
            }
        }
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

