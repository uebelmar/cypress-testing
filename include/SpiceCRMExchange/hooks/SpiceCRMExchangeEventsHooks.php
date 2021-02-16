<?php
namespace SpiceCRM\includes\SpiceCRMExchange\hooks;

use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeModules;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

abstract class SpiceCRMExchangeEventsHooks extends SpiceCRMExchangeBeansHooks
{
    protected $folderId = 'calendar';

    public function updateExchangeParticipants(&$bean) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['hooks_enabled'] == false) {
            return;
        }

        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['participant_policy'] == 'all'
            || SpiceConfig::getInstance()->config['SpiceCRMExchange']['participant_policy'] == 'users_only') {
            $userId = $bean->assigned_user_id ?? $current_user->id;
            if (SpiceCRMExchangeModules::isModuleSyncable($this->getModuleId($this->moduleName), $userId)) {
                $this->initializeBeanSync($bean);

                // overwrite general setting participant policy  with module related setting
                $this->beanSync->overwriteParticipantsPolicy();

                if (!empty($bean->external_id)) {
                    $this->beanSync->updateParticipants();
                }
            }
        }
    }

    /**
     * Override for the updateExchange function.
     * In case of cancelling an event, delete it from EWS.
     *
     * @param $bean
     * @return void|null
     */
    public function updateExchange(&$bean) {
        if(!$GLOBALS['isKRESTExchange']) {
            if ($bean->status == 'Cancelled') {
                $this->deleteExchange($bean);
            } else {
                parent::updateExchange($bean);
            }
        }
    }
}
