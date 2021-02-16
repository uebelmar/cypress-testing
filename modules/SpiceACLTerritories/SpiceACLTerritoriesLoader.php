<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SpiceACLTerritories;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class SpiceACLTerritoriesLoader{
    public function loadModuleTypes(){
        $db = DBManagerFactory::getInstance();

        $retArray = [];
        $moduleDatas = $db->query("SELECT * FROM spiceaclterritories_modules");
        while ($moduleData = $db->fetchByAssoc($moduleDatas)){

            $elements = $db->query("select spiceaclterritoryelements.* from spiceaclttypes_spiceacltelem, spiceaclterritoryelements where spiceaclterritoryelements.id = spiceaclttypes_spiceacltelem.spiceaclterritoryelement_id and spiceaclttypes_spiceacltelem.spiceaclterritorytype_id = '{$moduleData['spiceaclterritorytype_id']}'");
            while ($element = $db->fetchByAssoc($elements))
                $moduleData['elements'][] = $element;

            $retArray[] = $moduleData;
        }
        return $retArray;

    }

    public function loadUserTerritories(){
        /*
        $territory = \SpiceCRM\data\BeanFactory::getBean('SpiceACLTerritories');
        $userTerritories = $territory->getUserTerritories();
        return $userTerritories;
        */

        global $beanList;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $territorries = Array();

        $seed = BeanFactory::getBean('SpiceACLTerritories');
        $modulesObjects = $db->query("SELECT * FROM spiceaclterritories_modules");

        while ($modulesObject = $db->fetchByAssoc($modulesObjects)) {
            $territorries[$modulesObject['module']] = $seed->getUserTerritories($modulesObject['module'], true);
        }

        return $territorries;
    }
}
