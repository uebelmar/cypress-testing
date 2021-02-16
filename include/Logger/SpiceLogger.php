<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\includes\Logger;


use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\TimeDate;
use SpiceCRM\includes\authentication\AuthenticationController;

/**
 * Default SugarCRM Logger
 * @api
 */
class SpiceLogger implements LoggerTemplate
{
    /**
     * properties for the SpiceLogger
     */
    protected $logfile = 'spicecrm';
    protected $ext = '.log';
    protected $dateFormat = '%c';
    protected $logSize = '10MB';
    protected $maxLogs = 10;
    protected $filesuffix = "";
    protected $date_suffix = "";
    protected $log_dir = '.';
    protected $full_log_file;
    protected $dbcon;
    protected $_levelCategories;

    /**
     * used for config screen
     */
    public static $filename_suffix = array(
        //bug#50265: Added none option for previous version users
        "" => "None",
        "%m_%Y"    => "Month_Year",
        "%d_%m"    => "Day_Month",
        "%m_%d_%y" => "Month_Day_Year",
    );

    /**
     * Let's us know if we've initialized the logger file
     */
    protected $initialized = false;

    public $db = null;

    /**
     * Logger file handle
     */
    protected $fp = false;

    public function __get(
        $key
    )
    {
        return $this->$key;
    }

    /**
     * Used by the diagnostic tools to get SpiceLogger log file information
     */
    public function getLogFileNameWithPath()
    {
        return $this->full_log_file;
    }

    /**
     * Used by the diagnostic tools to get SpiceLogger log file information
     */
    public function getLogFileName()
    {
        return ltrim($this->full_log_file, "./");
    }

    /**
     * Constructor
     *
     * Reads the config file for logger settings
     */
    public function __construct()
    {
        $config = SpiceConfig::getInstance();
        $this->ext = $config->get('logger.file.ext', $this->ext);
        $this->logfile = $config->get('logger.file.name', $this->logfile);
        $this->dateFormat = $config->get('logger.file.dateFormat', $this->dateFormat);
        $this->logSize = $config->get('logger.file.maxSize', $this->logSize);
        $this->maxLogs = $config->get('logger.file.maxLogs', $this->maxLogs);
        $this->filesuffix = $config->get('logger.file.suffix', $this->filesuffix);
        $log_dir = $config->get('log_dir' , $this->log_dir);
        $this->log_dir = $log_dir . (empty($log_dir)?'':'/');
        //unset($config);
        $this->_doInitialization();
        LoggerManager::setLogger('default','\SpiceCRM\includes\Logger\SpiceLogger');


    }


    /**
     * Handles the SugarLogger initialization
     */
    protected function _doInitialization()
    {
        if( $this->filesuffix && array_key_exists($this->filesuffix, self::$filename_suffix) )
        { //if the global config contains date-format suffix, it will create suffix by parsing datetime
            $this->date_suffix = "_" . date(str_replace("%", "", $this->filesuffix));
        }
        $this->full_log_file = $this->log_dir . $this->logfile . $this->date_suffix . $this->ext;
        $this->initialized = $this->_fileCanBeCreatedAndWrittenTo();
        $this->rollLog();


    }

    /**
     * Checks to see if the SugarLogger file can be created and written to
     */
    protected function _fileCanBeCreatedAndWrittenTo()
    {
        $this->_attemptToCreateIfNecessary();
        return file_exists($this->full_log_file) && is_writable($this->full_log_file);
    }

    /**
     * Creates the SugarLogger file if it doesn't exist
     */
    protected function _attemptToCreateIfNecessary()
    {
        if (file_exists($this->full_log_file)) {
            return;
        }
        @touch($this->full_log_file);
    }

    /**
     * see LoggerTemplate::log()
     */
    public function log(
        $level,
        $message,
        $logparams = array()
    )
    {

        if (!$this->initialized) {
            return;
        }



        //lets get the current user id or default to -none- if it is not set yet
//        $userID = (!empty($logparams['user']))?\SpiceCRM\includes\authentication\AuthenticationController::getInstance()->getCurrentUser()->id:'-none-';
        $userID = '-none-';
        if(is_object(AuthenticationController::getInstance()->getCurrentUser()) && AuthenticationController::getInstance()->getCurrentUser()->id) {
            $userID = AuthenticationController::getInstance()->getCurrentUser()->id;
        }

        //if we haven't opened a file pointer yet let's do that
        if (! $this->fp)$this->fp = fopen ($this->full_log_file , 'a' );


        // change to a string if there is just one entry
        if ( is_array($message) && count($message) == 1 )
            $message = array_shift($message);
        // change to a human-readable array output if it's any other array
        if ( is_array($message) )
            $message = print_r($message,true);

        //if(!\SpiceCRM\includes\database\DBManagerFactory::getInstance()) {
        //write out to the file including the time in the dateFormat the process id , the user id , and the log level as well as the message
        fwrite($this->fp,
            strftime($this->dateFormat) . ' [' . getmypid() . '][' . $userID . '][' . strtoupper($level) . '] ' . $message . "\n"
        );
        //} else {
        //BEGIN introduced maretval 2018-06-06

        $this->logToSyslogs($level, $message, $logparams);
        //END
        //}
    }

    /**
     * introduced maretval 2018-06-06
     * save log to syslogs
     * @param $level
     * @param $message
     */
    public function logToSyslogs(
        $level,
        $message,
        $logparams = array()
    )
    {
        //do not log on install!
        if ( !empty( $GLOBALS['installing'] )) return true;

        //check if level is set for user
//        if(!empty(\SpiceCRM\includes\authentication\AuthenticationController::getInstance()->getCurrentUser()->id) &&
//            isset($_SESSION['authenticated_user_syslogconfig']) &&
//            !empty($_SESSION['authenticated_user_syslogconfig']) &&
//            $_SESSION['authenticated_user_syslogconfig']['user_id'] == \SpiceCRM\includes\authentication\AuthenticationController::getInstance()->getCurrentUser()->id &&
//            $_SESSION['authenticated_user_syslogconfig']['level'][$level] > 0
//        ) {

            $td = new TimeDate();
            $log = array("id" => create_guid(),
                "table_name" => "syslogs",
                "log_level" => $level,
                "pid" => getmypid(),
                "created_by" => (!empty($logparams['user']) ?: '-none-'),
                'microtime' => microtime(true),
                "date_entered" => $td->nowDb(),
                "description" => $message,
                "transaction_id" => isset( $GLOBALS['transactionID'] ) ? $GLOBALS['transactionID']:null );

            global $dictionary;
            $sql = DBManagerFactory::getInstance()->insertParams("syslogs", $dictionary['syslogs']['fields'], $log, null, false);
        DBManagerFactory::getInstance()->queryOnly($sql);
//        }
        //END

    }

    /**
     * rolls the logger file to start using a new file
     */
    protected function rollLog(
        $force = false
    )
    {
        if (!$this->initialized || empty($this->logSize)) {
            return;
        }
        // bug#50265: Parse the its unit string and get the size properly
        $units = array(
            'b' => 1,                   //Bytes
            'k' => 1024,                //KBytes
            'm' => 1024 * 1024,         //MBytes
            'g' => 1024 * 1024 * 1024,  //GBytes
        );
        if( preg_match('/^\s*([0-9]+\.[0-9]+|\.?[0-9]+)\s*(k|m|g|b)(b?ytes)?/i', $this->logSize, $match) ) {
            $rollAt = ( int ) $match[1] * $units[strtolower($match[2])];
        }
        //check if our log file is greater than that or if we are forcing the log to roll if and only if roll size assigned the value correctly
        if ( $force || ($rollAt && filesize ( $this->full_log_file ) >= $rollAt) ) {
            $temp = tempnam($this->log_dir, 'rot');
            if ($temp) {
                // warning here is expected in case if log file is opened by another process on Windows
                // or rotation has been already started by another process
                if (@rename($this->full_log_file, $temp)) {

                    // manually remove the obsolete part. Otherwise, rename() may fail on Windows (bug #22548)
                    $obsolete_part = $this->getLogPartPath($this->maxLogs - 1);
                    if (file_exists($obsolete_part)) {
                        unlink($obsolete_part);
                    }

                    // now lets move the logs starting at the oldest and going to the newest
                    for ($old = $this->maxLogs - 2; $old > 0; $old--) {
                        $old_name = $this->getLogPartPath($old);
                        if (file_exists($old_name)) {
                            $new_name = $this->getLogPartPath($old + 1);
                            rename($old_name, $new_name);
                        }
                    }

                    $part1 = $this->getLogPartPath(1);
                    rename($temp, $part1);
                } else {
                    unlink($temp);
                }
            }
        }
    }

    /**
     * Returns path for the given log part
     *
     * @param int $i
     * @return string
     */
    protected function getLogPartPath($i)
    {
        return $this->log_dir . $this->logfile . $this->date_suffix . '_' . $i . $this->ext;
    }

    /**
     * This is needed to prevent unserialize vulnerability
     */
    public function __wakeup()
    {
        // clean all properties
        foreach(get_object_vars($this) as $k => $v) {
            $this->$k = null;
        }
        throw new \Exception("Not a serializable object"); //todo-uebelmar clarify...which expection should be thrown?
    }

    /**
     * Destructor
     *
     * Closes the SugarLogger file handle
     */
    public function __destruct()
    {
        if ($this->fp)
        {
            fclose($this->fp);
            $this->fp = FALSE;
        }
    }
}
