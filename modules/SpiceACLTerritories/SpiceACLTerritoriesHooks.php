<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\VardefManager;

/***** SPICE-HEADER-SPACEHOLDER *****/

class SpiceACLTerritoriesHooks
{

    public function hook_after_retrieve(&$bean, $event, $arguments)
    {
        //$aclTerritory = \SpiceCRM\data\BeanFactory::getBean('SpiceACLTerritories');
        if ( isset($bean->field_name_map['spiceacl_territories_hash']) && empty($bean->spiceacl_secondary_territories)) {
            $territories = $bean->db->query("SELECT spiceaclterritories.id, spiceaclterritories.name FROM spiceaclterritories_hash, spiceaclterritories WHERE spiceaclterritories_hash.spiceaclterritory_id = spiceaclterritories.id AND hash_id = '$bean->spiceacl_territories_hash'");

            $secondaryTerritories = [];
            while ($territory = $bean->db->fetchByAssoc($territories)) {
                //$seed = \SpiceCRM\data\BeanFactory::getBean('SpiceACLTerritories', $territory['spiceaclterritory_id']);
                $secondaryTerritories[] = array(
                    'id' => $territory['id'],
                    'name' => $territory['name']
                );
            }
            $bean->spiceacl_secondary_territories = json_encode($secondaryTerritories);
        }
    }

    public function hook_before_save(&$bean, $event, $arguments)
    {
        $aclTerritory = BeanFactory::getBean('SpiceACLTerritories');
        if ( isset($bean->field_name_map['spiceacl_territories_hash'])) {
            if(!empty($bean->spiceacl_primary_territory)) {
                $territories = [$bean->spiceacl_primary_territory];

                // if the secondary tzerritories is an array process it .. otherwise treat as JSON
                if (is_array($bean->spiceacl_secondary_territories)) {
                    foreach ($bean->spiceacl_secondary_territories as $scondary_territory) {
                        if (array_search($scondary_territory, $territories) === false)
                            $territories[] = $scondary_territory;
                    }
                } else {
                    $secondaryTerritories = json_decode(html_entity_decode($bean->spiceacl_secondary_territories), true);
                    if (is_array($secondaryTerritories)) {
                        foreach ($secondaryTerritories as $scondary_territory) {
                            if (array_search($scondary_territory['id'], $territories) === false)
                                $territories[] = $scondary_territory['id'];
                        }
                    }
                }

                // sort the array and buold the hash
                sort($territories);
                $spiceacl_territories_hash = md5(implode('', $territories));

                // add resp remove hash
                if ($bean->spiceacl_territories_hash != $spiceacl_territories_hash || $bean->spiceacl_territories_hash == '') {
                    // see if we need to write it to the DB if we do not know thiss combination yet
                    $aclTerritory->checkTerritoryHash($spiceacl_territories_hash, $territories, $bean->module_dir);

                    // if the hash key is changing see if we can remove the old hashkey
                    if ($bean->spiceacl_territories_hash != '')
                        $aclTerritory->removeTerritoryHash($bean->spiceacl_territories_hash, $bean->module_dir, $bean->id);

                    // write it back to the bean
                    $bean->spiceacl_territories_hash = $spiceacl_territories_hash;
                }
            } else if (!empty($bean->spiceacl_territories_hash)) {
                $aclTerritory->removeTerritoryHash($bean->spiceacl_territories_hash, $bean->module_dir, $bean->id);
                $bean->spiceacl_territories_hash = '';
            }
        }
    }

    public function hook_create_vardefs(&$bean, $event, $arguments)
    {
        global $beanList;
$db = DBManagerFactory::getInstance();

        if(!isset($GLOBALS['dictionary'][$bean->object_name]['templates']['spiceaclterritories'])) {
            $modulerecord = $db->fetchByAssoc($db->query("SELECT * FROM spiceaclterritories_modules WHERE module = '$bean->module_dir'"));

            if ($modulerecord && empty($modulerecord['relatefrom']))
                VardefManager::addTemplate($bean->module_dir, $bean->object_name, 'spiceaclterritories');
        }
    }
}
