<?php

namespace SpiceCRM\includes\SpiceCRMExchange\hooks;

use SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers\SpiceCRMExchangeMeetings;

class SpiceCRMExchangeMeetingsHooks extends SpiceCRMExchangeEventsHooks
{
    protected $syncClass = SpiceCRMExchangeMeetings::class;
    protected $moduleName = 'Meetings';
}

