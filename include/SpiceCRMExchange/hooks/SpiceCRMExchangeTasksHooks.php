<?php
namespace SpiceCRM\includes\SpiceCRMExchange\hooks;

use SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers\SpiceCRMExchangeTasks;

class SpiceCRMExchangeTasksHooks extends SpiceCRMExchangeBeansHooks
{
    protected $syncClass = SpiceCRMExchangeTasks::class;
    protected $folderId  = "tasks";
    protected $moduleName = 'Tasks';
}
