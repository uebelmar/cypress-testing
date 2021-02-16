<?php
namespace SpiceCRM\includes\SpiceCRMExchange\Mappings;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\Connectivity\SpiceCRMExchangeUserSyncConfig;
use SpiceCRM\includes\ErrorHandlers\Exception;

class SpiceCRMExchangeModules
{

    public function __construct()
    {

    }

    /**
     * returns the mapped modules that can be used with the exchange integration
     *
     * @return array
     */
    public function getExchangeMappedModules(){
        $db = DBManagerFactory::getInstance();

        $modules = [];

        $modulesObj = $db->query("SELECT * FROM sysexchangemappingmodules");
        while($module = $db->fetchByAssoc($modulesObj)){
            $modules[] = $module;
        }

        return $modules;
    }

    /**
     * Returns the name of the EWS folder with which a Spice Module is synchronized.
     *
     * @param $moduleId
     * @return mixed
     * @throws Exception
     */
    public static function getEwsFolderForModule($moduleId) {
        $db = DBManagerFactory::getInstance();

        $sql = "SELECT exchange_object FROM sysexchangemappingmodules WHERE `sysmodule_id` = '" . $moduleId . "'";
        $result = $db->query($sql);
        $row =  $db->fetchRow($result);
        if ($row == false) {
            throw new Exception('Module not synchronizable with Exchange.', 403);
        }
        return $row['exchange_object'];
    }

    /**
     * Checks if a module is available for EWS synchronization.
     *
     * @param $moduleId
     * @return bool
     */
    public static function isModuleSyncable($moduleId, $userId) {
//        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
//        $sql = "SELECT exchangesubscription FROM sysexchangemappingmodules WHERE `sysmodule_id`='" . $moduleId . "'";
//        $query = $db->query($sql);
//        $row = $db->fetchRow($query);
//        return (bool) $row['exchangesubscription'];
        $userConfig = new SpiceCRMExchangeUserSyncConfig($userId);
        return $userConfig->isModuleSyncedInExchange($moduleId);
    }

    /**
     * Returns the module handler for a given Spice module name.
     * The module handler is a class handling the conversion between a Bean and the corresponding EWS object.
     *
     * @param $moduleName
     * @return mixed
     */
    public static function getModuleHandler($moduleName) {
        $db = DBManagerFactory::getInstance();

        $sql = "SELECT semm.module_handler 
                FROM sysexchangemappingmodules semm
                JOIN sysmodules sm ON sm.id=semm.sysmodule_id
                WHERE sm.module='" . $moduleName . "'";

        $query = $db->query($sql);
        $row = $db->fetchRow($query);

        return $row['module_handler'];
    }

    /**
     * Returns the name of a Spice module for a given module ID.
     *
     * @param $moduleId
     * @return mixed
     */
    public static function getModuleName($moduleId) {
        $db = DBManagerFactory::getInstance();

        $sql = "SELECT sm.module 
                FROM sysmodules sm
                WHERE sm.id='" . $moduleId . "'";

        $query = $db->query($sql);
        $row = $db->fetchRow($query);

        return $row['module'];
    }
}
