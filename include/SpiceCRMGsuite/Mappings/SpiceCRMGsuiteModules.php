<?php
namespace SpiceCRM\includes\SpiceCRMGsuite\Mappings;

use SpiceCRM\includes\database\DBManagerFactory;

class SpiceCRMGsuiteModules
{
    /**
     * Returns the module handler for a given Spice module name.
     * The module handler is a class handling the conversion between a Bean and the corresponding Google object.
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
