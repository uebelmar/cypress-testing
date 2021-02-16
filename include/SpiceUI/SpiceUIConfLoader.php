<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/**
 * Class SpiceUIConfLoader
 * load UI reference config
 */

namespace SpiceCRM\includes\SpiceUI;

use Exception;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\VardefManager;
use SpiceCRM\includes\SugarObjects\SpiceModules;
use SpiceCRM\includes\authentication\AuthenticationController;

class SpiceUIConfLoader
{
    public $sysuitables = array();

    public $loader;
    public $routebase = 'config'; // the common part in endpoint after domain url itself

    private $conftables = array(
        'syshooks',
        'sysmodules',
        'sysmodulefilters',
        'systemdeploymentrpdbentrys',
        'sysuiactionsetitems',
        'sysuiactionsets',
        'sysuiadmincomponents',
        'sysuiadmingroups',
        'sysuicomponentdefaultconf',
        'sysuicomponentmoduleconf',
        'sysuicomponentsets',
        'sysuicomponentsetscomponents',
        'sysuicopyrules',
        'sysuidashboarddashlets',
        'sysuifieldsets',
        'sysuifieldsetsitems',
        'sysuifieldtypemapping',
        'sysuilibs',
        'sysuiloadtaskitems',
        'sysuiloadtasks',
        'sysuifieldtypemapping',
        'sysuimodulerepository',
        'sysuiobjectrepository',
        'sysuirolemodules',
        'sysuiroles',
        'sysuiroutes',
    );

    /**
     * SpiceUIConfLoader constructor.
     * @param null $endpoint introduced with CR1000133
     */
    public function __construct($endpoint = null)
    {
        global $dictionary;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
        $this->loader = new SpiceUILoader($endpoint);

        // module dictionaries are unknown at that time
        // load them to make sure DBManager will have proper content in global $dictionary
        SpiceModules::loadModules();
        foreach($_SESSION['modules']['moduleList'] as $idx => $module){
            VardefManager::loadVardef($module, $_SESSION['modules']['beanList'][$module]);
        }

    }

    public static function getDefaultRoutes(){
        return ;
    }

    /**
     * retrieve table column names
     * @param $tb
     * @return array
     */
    public function getTableColumns($tb)
    {
        $columns = DBManagerFactory::getInstance()->get_columns($tb);
        $cols = array();
        foreach ($columns as $c => $col) {
            $cols[] = $col['name'];
        }
        return $cols;
    }

    /**
     * called bei SpiceInstaller
     * @param $package
     * @param string $version
     * @return mixed|string[]
     * @throws Exception
     */
    public function loadPackageForInstall($package, $version = '*')
    {
        $endpoint = implode("/", array($this->routebase, $package, $version));
        return $this->loadInstallConf($endpoint);
    }

    /**
     * called by loadPackageForInstall
     * @param $routeparams
     * @return mixed|string[]
     * @throws Exception
     */
    public function loadInstallConf($routeparams)
    {
        //get data
        if (!$response = $this->loader->callMethod("GET", $routeparams)) {
            $errormsg = "REST Call error somewhere... Action aborted";
            throw new Exception($errormsg);
        }
        return $response;
    }

    /**
     * load table entries for a given package
     * @param $package
     * @param string $version
     * @return array
     * @throws Exception
     */
    public function loadPackage($package, $version = '*')
    {
        $endpoint = implode("/", array($this->routebase, $package, $version));
        return $this->loadDefaultConf($endpoint, array('route' => $this->routebase, 'packages' => [$package], 'version' => $version), false);
    }

    /**
     * delete table entries for a given package
     * @param $package
     */
    public function deletePackage($package, $version = '*')
    {
        $db = DBManagerFactory::getInstance();
        // get the package and grab table names in use for thi spackage
        $route = implode("/", array($this->routebase, $package, $version));

        if (!$response = $this->loadPackageData($route)) {
            $errormsg = "ERROR deleting... Action aborted";
            throw new Exception($errormsg);
        }

        $tables = array_keys($response);

        foreach ($tables as $conftable){
            $delWhere = ['package' => $package];
            if(!$db->deleteQuery($conftable, $delWhere)){
                LoggerManager::getLogger()->fatal('error deleting package {$package}  '.$db->lastError());
            }
        }
    }

    /**
     * get the data
     * @param $routeparams
     * @return mixed|string[]
     */
    public function loadPackageData($routeparams){
        return $this->loader->callMethod("GET", $routeparams);
    }

    /**
     * load sysui config from reference database
     * get column name for each table
     * make a select passing the column names
     * create insert queries.
     * @param $route
     * @param $params
     */
    public function loadDefaultConf($routeparams, $params, $checkopen = true)
    {
        global $dictionary;
        $db = DBManagerFactory::getInstance();
        $tables = [];
        $inserts = [];
        $errors = [];

        $db->transactionStart();

        // make sure we have a full dictionary
//        \SpiceCRM\includes\SugarObjects\SpiceModules::loadModules();
//        foreach($_SESSION['modules']['beanFiles'] as $beanClass => $beanFile){
//            if(file_exists($beanFile)){
//                require_once $beanFile;
//            }
//        }
        //file_put_contents('dict.log', 'dict EmailTemplate '.print_r($dictionary, true)."\n", FILE_APPEND);

        if ($checkopen && $this->loader->hasOpenChangeRequest()) {
            $errormsg = "Open Change Requests found! They would be erased...";
            throw new Exception($errormsg);
        }
        //get data
        if (!$response = $this->loadPackageData($routeparams)) {
            $errormsg = "REST Call error somewhere... Action aborted";
            throw new Exception($errormsg);
        }

        $this->sysuitables = array_keys($response);

        if (!empty($response['nodata'])) {
            die($response['nodata']);
        }

        foreach ($response as $tb => $content) {
            //truncate command
            $tables[$tb] = 0;
            $thisCols = $this->getTableColumns($tb);
            switch ($tb) {
                case 'syslangs':
                    $db->truncateTableSQL($tb);
                    break;
                case 'sysfts': //don't do anything.
                    // Since we have no custom fts table, delete the whole thing might delete custom entries.
                    // therefore no action here
                    // each reference entry will be deleted before insert. See below 'delete before insert'.
                    break;
                default:
                    if(array_search('package', $thisCols) !== false) {
                        $deleteWhere = "package IN('" . implode("','", $params['packages']) . "') ";
                        //if (in_array($params['packages'][0], $params['packages']))
                        $deleteWhere .= "OR package IS NULL OR package=''";
                        if(!$db->deleteQuery($tb, $deleteWhere, true)){
                            LoggerManager::getLogger()->fatal('error deleting packages '.$db->lastError());
                        }
                    }
            }

            $tbColCheck = false;
            foreach ($content as $id => $encoded) {
                if (!$decodeData = json_decode(base64_decode($encoded), true))
                    die("Error decoding data: " . json_last_error_msg() .
                        " Reference table = $tb" .
                        " Action aborted");

                //compare table column names
                if (!$tbColCheck) {
                    $referenceCols = array_keys($decodeData);
                    if (!empty(array_diff($referenceCols, $thisCols))) {
                        die("Table structure for $tb is not up-to-date." .
                            " Reference table = " . implode(", ", $referenceCols) .
                            " Client table = " . implode(", ", $thisCols) .
                            " Action aborted");
                    }
                    $tbColCheck = true;
                }

                //prepare values for DB query
                foreach ($decodeData as $key => $value) {
                    $decodeData[$key] = (is_null($value) || $value === "" ? NULL :  $value);
                }
                //delete before insert
                $delWhere = ['id' => $decodeData['id']];
                if(!$db->deleteQuery($tb, $delWhere)){
                    LoggerManager::getLogger()->fatal("error deleting entry {$decodeData['id']} ".$db->lastError());
                }

                //run insert
//                if($tb == 'email_templates'){
//                    file_put_contents('spicecrm.log', 'dict email_templates '.print_r($dictionary['EmailTemplate'], true)."\n", FILE_APPEND);
//                }
                if($dbRes = $db->insertQuery($tb, $decodeData, true)){
                    $tables[$tb]++;
                    $inserts[] = $dbRes;
                } else{
                    $errors[] = $db->lastError();
                }
            }
        }

        //if no inserts where created => abort
        if (count($inserts) < 1) {
            $db->transactionRollback();
            throw new Exception("No inserts or no inserts run successfully. Action aborted.");
        }

        $db->transactionCommit();

        $success = true;
        if(count($errors) > 0){
            $success = false;
        }

        return ["success" => $success, "queries" => count($inserts), "errors" => $errors, "tables" => $tables];
    }


    /**
     * Remove sysmodules entries for modules that are not present in backend files
     * @return bool
     */
    public function cleanDefaultConf()
    {
        // load moduleList
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();

        $sysmodules = [];
        if ($current_user->is_admin) {
            $sysmodulesres = $db->query("SELECT * FROM sysmodules");
            while ($sysmodule = $db->fetchByAssoc($sysmodulesres)) {
                $sysmodules[] = $sysmodule['module'];
            }
        };

        // process
        if (isset($GLOBALS['moduleList'])) {
            foreach ($sysmodules as $sysmodule) {
                if (!in_array($sysmodule, $GLOBALS['moduleList'])) {
                    $delPks = ['module' => $sysmodule];
                    if(!$db->deleteQuery('sysmodules', $delPks)){
                        LoggerManager::getLogger()->fatal('error deleting packages '.$db->lastError());
                    }
                }
            }
        }
        return true;
    }

    /**
     * Get main information about current config loaded in client
     * package, version....
     */
    public function getCurrentConf()
    {
        $db = DBManagerFactory::getInstance();
        $qArray = [];
        $excludePackageCheck = ['systemdeploymentrpdbentrys'];
        foreach($this->conftables as $conftable) {
            if(!in_array($conftable, $excludePackageCheck)){
                $qArray[] = "(SELECT package, version FROM $conftable WHERE version is not null AND version <> '')";
            }
        }
        $q = implode(" UNION ", $qArray) . " ORDER BY package, version";
        $res = $db->query($q);
        $packages = [];
        $versions = [];

        while ($row = $db->fetchByAssoc($res)) {
            if (!empty($row['package']) && !in_array($row['package'], $packages)) {
                $packages[] = $row['package'];
            } elseif (!in_array('core', $packages) && !in_array($row['package'], $packages)) {
                $packages[] = 'core';
            }
            if (!empty($row['version']) && !in_array($row['version'], $versions))
                $versions[] = $row['version'];
        }
        return array('packages' => $packages, 'versions' => $versions);
    }
}
