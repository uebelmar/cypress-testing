<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\SugarObjects\VardefManager;

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

global  $mod_strings ;
$db = DBManagerFactory::getInstance();
$log = & $GLOBALS [ 'log' ] ;

if(!isset(SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) || !SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) {

    $query = "DELETE FROM relationships";
    $db->query($query);

//clear cache before proceeding..
    VardefManager::clearVardef();

// loop through all of the modules and create entries in the Relationships table (the relationships metadata) for every standard relationship, that is, relationships defined in the /modules/<module>/vardefs.php
// SugarBean::createRelationshipMeta just takes the relationship definition in a file and inserts it as is into the Relationships table
// It does not override or recreate existing relationships
    foreach ($GLOBALS['moduleList'] as $module) {
        $focus = BeanFactory::getBean($module);
        if ($focus instanceof SugarBean) {
            $table_name = $focus->table_name;
            $empty = array();
            if (empty ($_REQUEST ['silent']))
                echo $mod_strings ['LBL_REBUILD_REL_PROC_META'] . $focus->table_name . "...";
            SugarBean::createRelationshipMeta($focus->getObjectName(), $db, $table_name, $empty, $focus->module_dir);
            if (empty ($_REQUEST ['silent']))
                echo $mod_strings ['LBL_DONE'] . '<br>';
        }
    }

// do the same for custom relationships (true in the last parameter to SugarBean::createRelationshipMeta) - that is, relationships defined in the custom/modules/<modulename>/Ext/vardefs/ area
    foreach ($GLOBALS['moduleList'] as $module) {
        $focus = BeanFactory::getBean($module);
        if ($focus instanceof SugarBean) {
            $table_name = $focus->table_name;
            $empty = array();
            if (empty ($_REQUEST ['silent']))
                echo $mod_strings ['LBL_REBUILD_REL_PROC_C_META'] . $focus->table_name . "...";
            SugarBean::createRelationshipMeta($focus->getObjectName(), $db, $table_name, $empty, $focus->module_dir, true);
            if (empty ($_REQUEST ['silent']))
                echo $mod_strings ['LBL_DONE'] . '<br>';
        }
    }

// finally, whip through the list of relationships defined in TableDictionary.php, that is all the relationships in the metadata directory, and install those
    $dictionary = array();
    require('modules/TableDictionary.php');
    //for module installer incase we already loaded the table dictionary
    if (file_exists('custom/application/Ext/TableDictionary/tabledictionary.ext.php')) {
        include('custom/application/Ext/TableDictionary/tabledictionary.ext.php');
    }
    $rel_dictionary = $dictionary;
    foreach ($rel_dictionary as $rel_name => $rel_data) {
        $table = isset($rel_data ['table']) ? $rel_data ['table'] : "";

        if (empty ($_REQUEST ['silent']))
            echo $mod_strings ['LBL_REBUILD_REL_PROC_C_META'] . $rel_name . "...";
        SugarBean::createRelationshipMeta($rel_name, $db, $table, $rel_dictionary, '');
        if (empty ($_REQUEST ['silent']))
            echo $mod_strings ['LBL_DONE'] . '<br>';
    }

//clean relationship cache..will be rebuilt upon first access.
    if (empty ($_REQUEST ['silent']))
        echo $mod_strings ['LBL_REBUILD_REL_DEL_CACHE'];
    Relationship::delete_cache();

//////////////////////////////////////////////////////////////////////////////
// Remove the "Rebuild Relationships" red text message on admin logins


    if (empty ($_REQUEST ['silent']))
        echo $mod_strings ['LBL_REBUILD_REL_UPD_WARNING'];

// clear the database row if it exists (just to be sure)
    $query = "DELETE FROM versions WHERE name='Rebuild Relationships'";
    $log->info($query);
    $db->query($query);

// insert a new database row to show the rebuild relationships is done
    $id = create_guid();
    $gmdate = gmdate('Y-m-d H:i:s');
    $date_entered = DBManagerFactory::getInstance()->convert("'$gmdate'", 'datetime');
    $query = 'INSERT INTO versions (id, deleted, date_entered, date_modified, modified_user_id, created_by, name, file_version, db_version) ' . "VALUES ('$id', '0', $date_entered, $date_entered, '1', '1', 'Rebuild Relationships', '4.0.0', '4.0.0')";
    $log->info($query);
    $db->query($query);

    $rel = new Relationship();
    Relationship::delete_cache();
    $rel->build_relationship_cache();

// unset the session variable so it is not picked up in DisplayWarnings.php
    if (isset ($_SESSION ['rebuild_relationships'])) {
        unset ($_SESSION ['rebuild_relationships']);
    }

    if (empty ($_REQUEST ['silent']))
        echo $mod_strings ['LBL_DONE'];

}

// BEGIN CR1000108 vardefs to db. Grab directly from db
//if(isset(\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) && \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) {
//    $relationships = \SpiceCRM\modules\SystemVardefs\SystemVardefs::loadRelationships();
//    \SpiceCRM\modules\SystemVardefs\SystemVardefs::saveRelationshipsCache($relationships);
//
//}
// END CR1000108
