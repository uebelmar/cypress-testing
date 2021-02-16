<?php

namespace SpiceCRM\includes\SpiceCRMExchange\hooks;

use SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers\SpiceCRMExchangeCalls;

class SpiceCRMExchangeCallsHooks extends SpiceCRMExchangeEventsHooks
{
    protected $syncClass = SpiceCRMExchangeCalls::class;
    protected $moduleName = 'Calls';

    /**
     * Override for the updateExchange function.
     * In case of a new Call, do not send it to gsuite if the status is "Held".
     *
     * @param $bean
     * @return void|null
     */
    public function updateExchange(&$bean) {
        if ($bean->status == 'Held' && empty($bean->external_id)) {
            return null;
        }

        parent::updateExchange($bean);
    }
}

