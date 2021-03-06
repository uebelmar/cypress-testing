<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\database;

use Exception;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/*********************************************************************************
 * Description: This file handles the Data base functionality for the application.
 * It acts as the DB abstraction layer for the application. It depends on helper classes
 * which generate the necessary SQL. This sql is then passed to PEAR DB classes.
 * The helper class is chosen in DBManagerFactory, which is driven by 'db_type' in 'dbconfig' under config.php.
 *
 * All the functions in this class will work with any bean which implements the meta interface.
 * The passed bean is passed to helper class which uses these functions to generate correct sql.
 *
 * The meta interface has the following functions:
 * getTableName()                Returns table name of the object.
 * getFieldDefinitions()         Returns a collection of field definitions in order.
 * getFieldDefintion(name)       Return field definition for the field.
 * getFieldValue(name)           Returns the value of the field identified by name.
 *                               If the field is not set, the function will return boolean FALSE.
 * getPrimaryFieldDefinition()   Returns the field definition for primary key
 *
 * The field definition is an array with the following keys:
 *
 * name      This represents name of the field. This is a required field.
 * type      This represents type of the field. This is a required field and valid values are:
 *           �   int
 *           �   long
 *           �   varchar
 *           �   text
 *           �   date
 *           �   datetime
 *           �   double
 *           �   float
 *           �   uint
 *           �   ulong
 *           �   time
 *           �   short
 *           �   enum
 * length    This is used only when the type is varchar and denotes the length of the string.
 *           The max value is 255.
 * enumvals  This is a list of valid values for an enum separated by "|".
 *           It is used only if the type is �enum�;
 * required  This field dictates whether it is a required value.
 *           The default value is �FALSE�.
 * isPrimary This field identifies the primary key of the table.
 *           If none of the fields have this flag set to �TRUE�,
 *           the first field definition is assume to be the primary key.
 *           Default value for this field is �FALSE�.
 * default   This field sets the default value for the field definition.
 *
 *
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


/**
 * MySQL manager implementation for mysqli extension
 */
class MysqliManager extends MysqlManager
{
    /**
     * @see DBManager::$dbType
     */
    public $dbType = 'mysql';
    public $variant = 'mysqli';
    public $priority = 10;
    public $label = 'LBL_MYSQLI';

    /**
     * @see DBManager::$backendFunctions
     */
    protected $backendFunctions = array(
        'free_result' => 'mysqli_free_result',
        'close' => 'mysqli_close',
        'row_count' => 'mysqli_num_rows',
        'affected_row_count' => 'mysqli_affected_rows',
    );

    /**
     * @see MysqlManager::query()
     */
    public function query($sql, $dieOnError = false, $msg = '', $suppress = false, $keepResult = false)
    {
        try {


            if (is_array($sql)) {
                return $this->queryArray($sql, $dieOnError, $msg, $suppress);
            }

            static $queryMD5 = array();

            parent::countQuery($sql);
            LoggerManager::getLogger()->info('Query:' . $sql);
            $this->checkConnection();
            $this->query_time = microtime(true);
            $this->lastsql = $sql;
            $result = $suppress ? @mysqli_query($this->database, $sql) : mysqli_query($this->database, $sql);
            $md5 = md5($sql);

            if (empty($queryMD5[$md5]))
                $queryMD5[$md5] = true;

            $this->query_time = microtime(true) - $this->query_time;
            LoggerManager::getLogger()->info('Query Execution Time:' . $this->query_time);

            if (isset($GLOBALS['totalquerytime'])) $GLOBALS['totalquerytime'] += $this->query_time;

            // This is some heavy duty debugging, leave commented out unless you need this:
            /*
            $bt = debug_backtrace();
            for ( $i = count($bt) ; $i-- ; $i > 0 ) {
                if ( strpos('MysqliManager.php',$bt[$i]['file']) === false ) {
                    $line = $bt[$i];
                }
            }

            \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal("${line['file']}:${line['line']} ${line['function']} \nQuery: $sql\n");
            */


            if ($keepResult) {
                $this->lastResult = $result;
            }
            if ($result === false) {
                $this->checkError($msg . ' Query Failed: ' . $sql, $dieOnError);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $result;

    }

    /**
     * just execute query
     * introduced 2018-06-06
     * @param string $sql SQL Statement to execute
     * @param bool $suppress Flag to suppress all error output unless in debug logging mode.
     */
    public function queryOnly($sql, $suppress = true)
    {
        $this->checkConnection();
        $result = $suppress ? @mysqli_query($this->database, $sql) : mysqli_query($this->database, $sql);
        return $result;
    }

    /**
     * Returns the number of rows affected by the last query
     *
     * @return int
     */
    public function getAffectedRowCount($result)
    {
        return mysqli_affected_rows($this->getDatabase());
    }

    /**
     * Returns the number of rows returned by the result
     *
     * This function can't be reliably implemented on most DB, do not use it.
     * @abstract
     * @param resource $result
     * @return int
     * @deprecated
     */
    public function getRowCount($result)
    {
        return mysqli_num_rows($result);
    }


    /**
     * Disconnects from the database
     *
     * Also handles any cleanup needed
     */
    public function disconnect()
    {
        LoggerManager::getLogger()->debug('Calling MySQLi::disconnect()');
        if (!empty($this->database)) {
            $this->freeResult();
            mysqli_close($this->database);
            $this->database = null;
        }
    }

    /**
     * @see DBManager::freeDbResult()
     */
    protected function freeDbResult($dbResult)
    {
        if (!empty($dbResult))
            mysqli_free_result($dbResult);
    }

    /**
     * @see DBManager::getFieldsArray()
     */
    public function getFieldsArray($result, $make_lower_case = false)
    {
        $field_array = array();

        if (!isset($result) || empty($result))
            return 0;

        $i = 0;
        while ($i < mysqli_num_fields($result)) {
            $meta = mysqli_fetch_field_direct($result, $i);
            if (!$meta)
                return 0;

            if ($make_lower_case == true)
                $meta->name = strtolower($meta->name);

            $field_array[] = $meta->name;

            $i++;
        }

        return $field_array;
    }

    /**
     * @see DBManager::fetchRow()
     */
    public function fetchRow($result)
    {
        if (empty($result)) return false;

        $row = mysqli_fetch_assoc($result);
        if ($row == null) $row = false; //Make sure MySQLi driver results are consistent with other database drivers
        return $row;
    }

    /**
     * @see DBManager::quote()
     */
    public function quote($string)
    {
        return mysqli_real_escape_string($this->getDatabase(), $this->quoteInternal($string));
    }

    /**
     * @see DBManager::connect()
     */
    public function connect(array $configOptions = null, $dieOnError = false)
    {


        if (is_null($configOptions))
            $configOptions = SpiceConfig::getInstance()->config['dbconfig'];

        if (!isset($this->database)) {

            //mysqli connector has a separate parameter for port.. We need to separate it out from the host name
            $dbhost = $configOptions['db_host_name'];
            $dbport = null;
            $pos = strpos($configOptions['db_host_name'], ':');
            if ($pos !== false) {
                $dbhost = substr($configOptions['db_host_name'], 0, $pos);
                $dbport = substr($configOptions['db_host_name'], $pos + 1);
            }

            $this->database = mysqli_connect($dbhost, $configOptions['db_user_name'], $configOptions['db_password'], isset($configOptions['db_name']) ? $configOptions['db_name'] : '', $dbport);
            if (empty($this->database)) {
                LoggerManager::getLogger()->fatal("Could not connect to DB server " . $dbhost . " as " . $configOptions['db_user_name'] . ". port " . $dbport . ": " . mysqli_connect_error());
                if ($dieOnError) {
                    if (isset($GLOBALS['app_strings']['ERR_NO_DB'])) {
                        sugar_die($GLOBALS['app_strings']['ERR_NO_DB']);
                    } else {
                        sugar_die("Could not connect to the database. Please refer to spicecrm.log for details.");
                    }
                } else {
                    return false;
                }
            }
        }

        if (!empty($configOptions['db_name']) && !@mysqli_select_db($this->database, $configOptions['db_name'])) {
            LoggerManager::getLogger()->fatal("Unable to select database {$configOptions['db_name']}: " . mysqli_connect_error());
            if ($dieOnError) {
                if (isset($GLOBALS['app_strings']['ERR_NO_DB'])) {
                    sugar_die($GLOBALS['app_strings']['ERR_NO_DB']);
                } else {
                    sugar_die("Could not connect to the database. Please refer to spicecrm.log for details.");
                }
            } else {
                return false;
            }
        }

        // cn: using direct calls to prevent this from spamming the Logs
        // CR1000349 mysql8 compatibility: remove hardcoded charset
        $charset = $this->getOption('charset');
        if (empty($charset)) {
            $charset = 'utf8';
        }
        mysqli_query($this->database, "SET CHARACTER SET " . $charset . "");
        $names = "SET NAMES '$charset'";
        $collation = $this->getOption('collation');
        if (!empty($collation)) {
            $names .= " COLLATE '$collation'";
        }
        mysqli_query($this->database, $names);

        if ($this->checkError('Could Not Connect', $dieOnError))
            LoggerManager::getLogger()->info("connected to db");

        $this->connectOptions = $configOptions;
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see MysqlManager::lastDbError()
     */
    public function lastDbError()
    {
        if ($this->database) {
            if (mysqli_errno($this->database)) {
                return "MySQL error " . mysqli_errno($this->database) . ": " . mysqli_error($this->database);
            }
        } else {
            $err = mysqli_connect_error();
            if ($err) {
                return $err;
            }
        }

        return false;
    }

    public function getDbInfo()
    {
        $charsets = $this->getCharsetInfo();
        $charset_str = array();
        foreach ($charsets as $name => $value) {
            $charset_str[] = "$name = $value";
        }
        return array(
            "MySQLi Version" => @mysqli_get_client_info(),
            "MySQLi Host Info" => @mysqli_get_host_info($this->database),
            "MySQLi Server Info" => @mysqli_get_server_info($this->database),
            "MySQLi Client Encoding" => @mysqli_character_set_name($this->database),
            "MySQL Character Set Settings" => join(", ", $charset_str),
        );
    }

    /**
     * Select database
     * @param string $dbname
     */
    protected function selectDb($dbname)
    {
        return mysqli_select_db($this->getDatabase(), $dbname);
    }

    /**
     * Check if this driver can be used
     * @return bool
     */
    public function valid()
    {
        return function_exists("mysqli_connect") && empty(SpiceConfig::getInstance()->config['mysqli_disabled']);
    }

    public function compareVarDefs($fielddef1, $fielddef2, $ignoreName = false)
    {
        $compared = parent::compareVarDefs($fielddef1, $fielddef2, $ignoreName);
        if (!$compared) return false;

        // return  mysqli_real_escape_string($this->database, $fielddef2['comment']) != $fielddef1['comment'] ? false : true;
        if (str_replace([';'], '', $fielddef2['comment']) != $fielddef1['comment'])
            return false;
        else
            return true;
    }

    /**
     * introduced in SpiceCRM 20180900
     * Original DBManager function: added ticks for columns names
     * add ticks to column names to prevent mysql strict mode from issuing errors when column name is a reserved word
     * @param array $fieldDef
     * @param bool $ignoreRequired
     * @param string $table
     * @param bool $return_as_array
     * @return array|string
     */
    protected function oneColumnSQLRep($fieldDef, $ignoreRequired = false, $table = '', $return_as_array = false)
    {
        // introduced in spicecrm 202001001
        // check that the right type is passed to support definitions like int 20
        $this->normalizeVardefs($fieldDef);


        $name = $fieldDef['name'];
        $type = $this->getFieldType($fieldDef);
        $colType = $this->getColumnType($type);

        if ($parts = $this->getTypeParts($colType)) {
            $colBaseType = $parts['baseType'];
            $defLen = isset($parts['len']) ? $parts['len'] : '255'; // Use the mappings length (precision) as default if it exists
        }

        if (!empty($fieldDef['len'])) {
            if (in_array($colBaseType, array('nvarchar', 'nchar', 'varchar', 'varchar2', 'char',
                'clob', 'blob', 'text'))) {
                $colType = "$colBaseType(${fieldDef['len']})";
            } elseif (($colBaseType == 'decimal' || $colBaseType == 'float')) {
                if (!empty($fieldDef['precision']) && is_numeric($fieldDef['precision']))
                    if (strpos($fieldDef['len'], ',') === false) {
                        $colType = $colBaseType . "(" . $fieldDef['len'] . "," . $fieldDef['precision'] . ")";
                    } else {
                        $colType = $colBaseType . "(" . $fieldDef['len'] . ")";
                    }
                else
                    $colType = $colBaseType . "(" . $fieldDef['len'] . ")";
            }
        } else {
            if (in_array($colBaseType, array('nvarchar', 'nchar', 'varchar', 'varchar2', 'char'))) {
                $colType = "$colBaseType($defLen)";
            }
        }

        $default = '';

        // Bug #52610 We should have ability don't add DEFAULT part to query for boolean fields
        if (!empty($fieldDef['no_default'])) {
            // nothing to do
        } elseif (isset($fieldDef['default']) && strlen($fieldDef['default']) > 0) {
            $default = " DEFAULT " . $this->quoted($fieldDef['default']);
        } elseif (!isset($default) && $type == 'bool') {
            $default = " DEFAULT 0 ";
        }

        $auto_increment = '';
        if (!empty($fieldDef['auto_increment']) && $fieldDef['auto_increment'])
            $auto_increment = $this->setAutoIncrement($table, $fieldDef['name']);

        $required = 'NULL';  // MySQL defaults to NULL, SQL Server defaults to NOT NULL -- must specify
        //Starting in 6.0, only ID and auto_increment fields will be NOT NULL in the DB.
        if ((empty($fieldDef['isnull']) || strtolower($fieldDef['isnull']) == 'false') &&
            (!empty($auto_increment) || $name == 'id' || ($fieldDef['type'] == 'id' && !empty($fieldDef['required'])))) {
            $required = "NOT NULL";
        }
        // If the field is marked both required & isnull=>false - alwqys make it not null
        // Use this to ensure primary key fields never defined as null
        if (isset($fieldDef['isnull']) && (strtolower($fieldDef['isnull']) == 'false' || $fieldDef['isnull'] === false)
            && !empty($fieldDef['required'])) {
            $required = "NOT NULL";
        }
        if ($ignoreRequired)
            $required = "";

        $comment = '';
        if (!empty($fieldDef['comment'])) {
            $comment = " COMMENT '" . mysqli_real_escape_string($this->database, $fieldDef['comment']) . "'";
        }

        if ($return_as_array) {
            return array(
                'name' => "`" . $name . "`",
                'colType' => $colType,
                'colBaseType' => $colBaseType,  // Adding base type for easier processing in derived classes
                'default' => $default,
                'required' => $required,
                'auto_increment' => $auto_increment,
                'comment' => $comment,
                'full' => "$name $colType $default $required $auto_increment $comment",
            );
        } else {
            return "`$name` $colType $default $required $auto_increment $comment";
        }
    }

    /**
     * use MYSQL in the upsert Query
     *
     * @see DBManager::upsertQuery()
     */
    public function upsertQuery($table, array $pks, array $data)
    {
        $cols = array_keys($data);
        $vals = array_values($data);
        $this->query("REPLACE INTO " . $table . " (" . implode(',', $cols) . ") VALUES ('" . implode("','", $vals) . "')");
    }

}
