<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\Logger;

use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

/**
 * Log management
 * Modifications introduced in spicecrm 20180900 to support SpiceLogger
 * @api
 */


class LoggerManager
{
	//this the the current log level
	private $_level = 'fatal';

	//this is a list of different loggers that have been loaded
	protected static $_loggers = array();

	//this is the instance of the LoggerManager
	private static $_instance = NULL;

	//these are the mappings for levels to different log types
	private static $_logMapping = array(
		'default' => '\SpiceCRM\includes\Logger\SpiceLogger',
		'fatal' => '\SpiceCRM\includes\Logger\SpiceLogger',
	);

	//these are the log level mappings anything with a lower value than your current log level will be logged
	private static $_levelMapping = array(
		'debug'      => 100,
		'info'       => 70,
		'warn'       => 50,
		'deprecated' => 40,
        'login'      => 30,
		'error'      => 25,
		'fatal'      => 10,
		'security'   => 5,
		'off'        => 0,
	);

    private static $_levelCategories = array(

    );
    private static $_dbconfig = array(

    );


	//only let the getLogger instantiate this object
	private function __construct()
	{
		$level = SpiceConfig::getInstance()->get('logger.level', $this->_level);
		if (!empty($level))
			$this->setLevel($level);

		if ( empty(self::$_loggers) )
		    $this->_findAvailableLoggers();

	}


	/**
	 * Overloaded method that handles the logging requests.
	 *
	 * @param string $method
	 * @param string $message - also handles array as parameter, though that is deprecated.
	 */
 	public function __call(
 	    $method,
 	    $message
 	    )
 	{



        //ORIGINAL if ( !isset(self::$_levelMapping[$method]) )
        if ( !isset(self::$_levelMapping[$method]) && empty(self::$_levelCategories))
        //END
            $method = $this->_level;
 		//if the method is a direct match to our level let's let it through this allows for custom levels
        //BEGIN SpiceCRM enhancement
//        if($method == $this->_level
//                //otherwise if we have a level mapping for the method and that level is less than or equal to the current level let's let it log
//                || (!empty(self::$_levelMapping[$method])
//                    && self::$_levelMapping[$this->_level] >= self::$_levelMapping[$method]) ) {

        //check levelcategorie for logging
        $logparams = array();
        $all = false;
        if( @self::$_levelCategories[$method]['*'] > 0 ) {
            $all = true;
            $logparams['users'] = '*';
        }
        $user = false;
        $currentUser=AuthenticationController::getInstance()->getCurrentUser();
        if( $currentUser !== null && @self::$_levelCategories[$method][$currentUser->id] > 0) {
            $user = true;
            $logparams['user'] = $currentUser->id;
        }

        //log
        if(
            (
                empty(self::$_levelCategories)
                &&
                ($method == $this->_level
                    //otherwise if we have a level mapping for the method and that level is less than or equal to the current level let's let it log
                    || (!empty(self::$_levelMapping[$method])
                        && self::$_levelMapping[$this->_level] >= self::$_levelMapping[$method])
                )
            )
            || $user
            || $all

        ){
        //END

 			//now we get the logger type this allows for having a file logger an email logger, a firebug logger or any other logger you wish you can set different levels to log differently
 			$logger = (!empty(self::$_logMapping[$method])) ?
 			    self::$_logMapping[$method] : self::$_logMapping['default'];
 			//if we haven't instantiated that logger let's instantiate
 			if (!isset(self::$_loggers[$logger])) {
 			    self::$_loggers[$logger] = new $logger();
 			}
 			//tell the logger to log the message
            self::$_loggers[$logger]->log($method, $message, $logparams);
 		}
 	}

 	/**
 	 * Check if this log level will be producing any logging
 	 * @param string $method
 	 * @return boolean
 	 */
 	public function wouldLog($method)
 	{
 	    if ( !isset(self::$_levelMapping[$method]) )
 	    	$method = $this->_level;
 	    if($method == $this->_level
 	    		//otherwise if we have a level mapping for the method and that level is less than or equal to the current level let's let it log
 	    		|| (!empty(self::$_levelMapping[$method])
 	    				&& self::$_levelMapping[$this->_level] >= self::$_levelMapping[$method]) ) {
 	        return true;
 	    }
 	    return false;
 	}

	/**
     * Used for doing design-by-contract assertions in the code; when the condition fails we'll write
     * the message to the debug log
     *
     * @param string  $message
     * @param boolean $condition
     */
    public function assert(
        $message,
        $condition
        )
    {
        if ( !$condition )
            $this->__call('debug', $message);
	}

	/**
	 * Sets the logger to the level indicated
	 *
	 * @param string $name name of logger level to set it to
	 */
 	public function setLevel(
 	    $name
 	    )
 	{
        if ( isset(self::$_levelMapping[$name]) )
            $this->_level = $name;
 	}

    /**
     * SpiceCRM enhancement
     * allocate dbconfig
     *
     * @param array $config
     */
 	public static function setDbConfig($config){
        self::$_dbconfig = $config;
    }

    /**
     * SpiceCRM enhancement
     * allocate level categories
     *
     * @param string $categories
     */
    public static function setLevelCategories(
        $categories = array()
    )
    {
        if(!empty($categories))
            self::$_levelCategories = $categories;
    }

    /**
     * SpiceCRM enhancement
     * allocate level categories
     *
     */
    public static function getLevelCategories(){
        if(empty(self::$_levelCategories) && isset( SpiceConfig::getInstance()->config['logger']['default'] ) && SpiceConfig::getInstance()->config['logger']['default'] === '\SpiceCRM\includes\Logger\SpiceLogger'){
            $levelCategories = array();
            if(DBManagerFactory::getInstance()) {
                $res = DBManagerFactory::getInstance()->queryOnly("SELECT * FROM syslogusers WHERE logstatus > 0 ORDER BY level");
                while ($row = DBManagerFactory::getInstance()->fetchByAssoc($res)) {
                    $levelCategories[$row['level']][$row['user_id']] = true;
                }

                if (!empty($levelCategories))
                    LoggerManager::setLevelCategories($levelCategories);
            }
        }
    }

 	/**
 	 * Returns a logger instance
 	 */
 	public static function getLogger()
	{
		if(!LoggerManager::$_instance){
			LoggerManager::$_instance = new LoggerManager();
            self::setLogger('default',(SpiceConfig::getInstance()->get('logger.default') ?: '\SpiceCRM\includes\Logger\SpiceLogger'));
            self::setDbConfig(SpiceConfig::getInstance()->get('dbconfig'));
            self::getLevelCategories();
		}
		return LoggerManager::$_instance;
	}

	/**
	 * Sets the logger to use a particular backend logger for the given level. Set level to 'default'
	 * to make it the default logger for the application
	 *
	 * @param string $level name of logger level to set it to
	 * @param string $logger name of logger class to use
	 */
	public static function setLogger(
 	    $level,
 	    $logger
 	    )
 	{
 	    self::$_logMapping[$level] = $logger;
 	}

 	/**
 	 * Finds all the available loggers in the application
 	 */
 	protected function _findAvailableLoggers()
 	{
 	    $locations = ['include/Logger','custom/include/Logger'];
 	    foreach ( $locations as $location ) {
            if (is_dir($location) && $dir = opendir($location)) {
                while (($file = readdir($dir)) !== false) {
                    if ($file == ".."
                            || $file == "."
                            || $file == "LoggerTemplate.php"
                            || $file == "LoggerManager.php"
                            || $file == "RESTLogViewer.php"
                            || !is_file("$location/$file")
                            )
                        continue;
                    require_once("$location/$file");
                    $loggerClass = basename($file, ".php");
                    if ( class_exists($loggerClass) && class_implements($loggerClass,'LoggerTemplate') )
                        self::$_loggers[$loggerClass] = new $loggerClass();
                }
            }
        }
 	}

 	public static function getAvailableLoggers()
 	{
 	    return array_keys(self::$_loggers);
 	}

 	public static function getLoggerLevels()
 	{
 	    $loggerLevels = self::$_levelMapping;
 	    foreach ( $loggerLevels as $key => $value )
 	        $loggerLevels[$key] = ucfirst($key);

 	    return $loggerLevels;
 	}

 	public static function formatBackTrace($backTrace) {
 	    $traces = "";
 	    foreach ($backTrace as $entry) {
 	        $traces .= "\n  " . $entry['file'] . '(' . $entry['line'] . '): ' .
                @$entry['class'] . '->' . $entry['function'];
        }

        return $traces;
    }
}
