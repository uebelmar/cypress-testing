<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SugarObjects;

use Exception;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\utils\SugarArray;
use SpiceCRM\includes\database;

/**
 * Config manager
 * @api
 */
class SpiceConfig
{
    var $_cached_values = array();

    private static $instance = null;

    public $config = [];


    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {}
    /**
     * @return SpiceConfig
     */
    static function getInstance()
    {
        if (self::$instance === null) {

            //set instance
            self::$instance = new self;
            self::$instance->loadConfigFiles();
        }
        return self::$instance;
    }

    public function get($key, $default = null)
    {
        $value = SugarArray::staticGet($this->config, $key, $default);
        return $value ? $value : $default;
    }

    public function configExists()
    {
        return $this->config !== [];
    }

    /**
     * @return Array
     */
    protected function loadConfigFiles()
    {
        $sugar_config = [];
        if (is_file('config.php')) {
            include('config.php'); // provides \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config
        }

        // load up the config_override.php file.  This is used to provide default user settings
        if (is_file('config_override.php')) {
            include('config_override.php');
        }
        $this->config = $sugar_config;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function loadConfigFromDB()
    {
        $entries = [];
        $db = DBManagerFactory::getInstance();
        if ($db) {
            $result = $db->query("SELECT * FROM config");
            while ($configEntry = $db->fetchByAssoc($result)) {
                $entries[$configEntry['category']][$configEntry['name']] = $configEntry['value'];
            }
            //if(count($entries))
            {
                $this->config = array_merge($this->config, $entries);
            }
        } else {
            //todo clarify if we should throw an error...
        }
        return true;
    }



    /**
     * reloads the complete config
     */
    function reloadConfig(){
        $this->loadConfigFiles();
        $this->loadConfigFromDB();
    }
}

