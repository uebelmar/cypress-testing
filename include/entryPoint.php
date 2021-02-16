<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\TimeDate;
use SpiceCRM\includes\LogicHook\LogicHook;
use SpiceCRM\includes\UploadStream;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceModules;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceDictionary\SpiceDictionaryHandler;

// load the config files
SpiceConfig::getInstance()->loadConfigFromDB();

// load the core dictionary files
SpiceDictionaryHandler::loadMetaDataFiles();

require_once('include/utils.php');

require_once('sugar_version.php'); // provides $sugar_version, $sugar_db_version


// get the logger
LoggerManager::getLogger();



// load the metadata from the database
SpiceDictionaryHandler::loadMetaDataDefinitions();

// load the modules
SpiceModules::loadModules();

// require_once('modules/ACL/ACLController.php');
$controllerfile = isset( SpiceConfig::getInstance()->config['acl']['controller'][0] ) ? SpiceConfig::getInstance()->config['acl']['controller'] : 'modules/SpiceACL/SpiceACLController.php';
require_once ($controllerfile);

UploadStream::register();

if (empty($GLOBALS['installing'])) {

    if (!empty(SpiceConfig::getInstance()->config['session_dir'])) {
        session_save_path(SpiceConfig::getInstance()->config['session_dir']);
    }

    // load the config from the db and populate to \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config
    SpiceConfig::loadConfigFromDB();

    $GLOBALS['timedate'] = TimeDate::getInstance();

    $current_user = BeanFactory::getBean('Users');//todo-uebelmar clarify... no global $current_user .. this variable has no usage and no scope
    $system_config = BeanFactory::getBean('Administration');
    $system_config->retrieveSettings();

    LogicHook::initialize()->call_custom_logic('', 'after_entry_point');
}

