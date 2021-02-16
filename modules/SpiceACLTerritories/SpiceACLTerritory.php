<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SpiceACLTerritories;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

class SpiceACLTerritory extends SugarBean
{

    public $table_name = 'spiceaclterritories';
    public $object_name = 'SpiceACLTerritory';
    public $module_dir = 'SpiceACLTerritories';


    public function __construct()
    {
        parent::__construct();
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return false;
        }

        return false;
    }

    function fill_in_additional_list_fields()
    {
        parent::fill_in_additional_list_fields();

        $this->load_add_data();
    }

    public function retrieve($id = -1, $encode = true, $deleted = true, $relationships = true)
    {
        $retValue = parent::retrieve($id, $encode, $deleted, $relationships);

        $this->load_add_data();

        // return the bean
        return $retValue;
    }

    /*
     * creates vardefs if module if org managed
     */
    public function createVardefs($event, $arguments){

    }

    /*
     * returns the assigned type ID for a given module
     */
    public function getTypeForModule($module)
    {
        

        if (SpiceConfig::getInstance()->config['acl']['disable_cache'] || empty($_SESSION['spiceaclaccess']['aclmoduletypes'][$module])) {
            $idRecord = $this->db->fetchByAssoc($this->db->query("SELECT spiceaclterritorytype_id FROM spiceaclterritories_modules WHERE module ='$module'"));
            $_SESSION['spiceaclaccess']['aclmoduletypes'][$module] =  $idRecord['spiceaclterritorytype_id'];
        }

        return $_SESSION['spiceaclaccess']['aclmoduletypes'][$module] ?: false;
    }

    /*
     * deactivate a given ACL Object
     */
    public function deactivateACLObject($objectID)
    {
        $this->db->query("DELETE FROM spiceaclobjects_hash WHERE spiceaclobject_id = '$objectID'");
    }

    /*
     * activates an ACL Object with a givben module, id and elementvalues
     */
    public function activateACLObject($module, $objectID, $elementValues)
    {

        // delete all entries
        $this->deactivateACLObject($objectID);

        // build the query
        $query = "SELECT DISTINCT sh.hash_id hash_id, '$objectID' spiceaclobject_id FROM spiceaclterritories_hash sh INNER JOIN spiceaclterritories st ON st.id = sh.spiceaclterritory_id";
        $i = 0;
        foreach ($elementValues as $elementId => $elementIdValues) {
            if (array_search('*', $elementIdValues) === false) {
                $query .= " INNER JOIN spiceaclt_spiceacltelemenv stv" . $i . " ON st.id = stv" . $i . ".spiceaclterritory_id AND stv" . $i . ".spiceaclterritoryelement_id = '$elementId' AND stv" . $i . ".elementvalue in ('" . implode("','", $elementIdValues) . "') ";
            }
            $i++;
        }
        $query .= " WHERE st.territorytype_id = '{$this->getTypeForModule($module)}'";

        // run the insert query
        $this->db->query("INSERT INTO spiceaclobjects_hash $query");

    }

    /*
     * retuirns the territory values for an ACL Object
     */
    public function getAclObjectTerritoryValues($aclobjectId)
    {
        $retArray = array();
        $objectOrgValues = $this->db->query("SELECT * FROM spiceaclobjectsterritoryelementvalues WHERE spiceaclobject_id='$aclobjectId'");
        while ($thisObjectOrgValue = $this->db->fetchByAssoc($objectOrgValues)) {
            $retArray[$thisObjectOrgValue['spiceaclterritoryelement_id']] = json_decode(html_entity_decode($thisObjectOrgValue['value']));

        }

        return $retArray;
    }

    /**
     * returns the elements maintained for a territory type
     *
     * @param $territorytype
     * @return array
     */
    public function getTerritoryTypeElements($territorytype){
        $db = DBManagerFactory::getInstance();

        $elementsArray = [];
        $elements = $db->query("SELECT spiceaclterritoryelements.id, spiceaclterritoryelements.name FROM spiceaclttypes_spiceacltelem, spiceaclterritoryelements WHERE spiceaclttypes_spiceacltelem.spiceaclterritoryelement_id = spiceaclterritoryelements.id AND spiceaclterritorytype_id='$territorytype'");
        while($element = $db->fetchByAssoc($elements)){
            $elementsArray[] = $element;
        }
        return $elementsArray;
    }

    /**
     * queries the territories and returns the terriotry matching the element values for the module
     *
     * @param $module the module to search for
     * @param $values and array with element values ['elementname' => 'elementvalue', ...]
     */
    public function getTerritoryByValues($module, $values){
        $db = DBManagerFactory::getInstance();

        $territoryType = $this->getTypeForModule($module);
        $elements = $this->getTerritoryTypeElements($territoryType);

        $sql = "SELECT id FROM spiceaclterritories";
        foreach($elements as $element){
            $joinName = hash('md5', $element['id']);
            $sql .= " INNER JOIN spiceaclt_spiceacltelemenv $joinName ON $joinName.spiceaclterritory_id = spiceaclterritories.id AND $joinName.spiceaclterritoryelement_id = '{$element['id']}' AND $joinName.elementvalue='{$values[$element['name']]}'";
        }
        $sql .= " AND territorytype_id='{$territoryType}'";

        $territory = $db->fetchByAssoc($db->query($sql));
        if($territory['id']) {
            $this->retrieve($territory['id']);
        }
        return $territory['id'];
    }

    /*
     * returns the territories for a given set of attributes
     */
    public function getTerritoryHashesForElementValues($module, $elementValues)
    {

        // build the query
        $query = "SELECT DISTINCT sh.hash_id FROM spiceaclterritories_hash sh INNER JOIN spiceaclterritories st ON st.id = sh.spiceaclterritory_id";
        $i = 0;
        foreach ($elementValues as $elementId => $elementIdValues) {
            if (array_search('*', $elementIdValues) === false) {
                $query .= "INNER JOIN spiceaclt_spiceacltelemenv stv" . $i . " ON st.id = stv" . $i . ".spiceaclterritory_id AND stv" . $i . ".spiceaclterritoryelement_id = '$elementId' AND stv" . $i . ".elementvalue in ('" . implode("','", $elementIdValues) . "') ";
            }
            $i++;
        }
        $query .= " WHERE st.territorytype_id = '{$this->getTypeForModule($module)}'";

        // get values

    }

    private function load_add_data()
    {
        $this->elementvalues = $this->getElementValues($this->id);

        // get the usage count
        $usagecount = $this->db->fetchByAssoc($this->db->query("SELECT COUNT(hash_id) usagecount FROM spiceaclterritories_hash WHERE spiceaclterritory_id = '$this->id'"));
        $this->usagecount = $usagecount['usagecount'];
    }

    private function getElementValues($territoryId){
        // get all values as well
        $elementsObj = $this->db->query("SELECT stse.spiceaclterritoryelement_id, ste.name, stse.elementvalue, stv.elementdescription FROM spiceaclterritoryelements ste INNER JOIN spiceaclt_spiceacltelemenv stse ON stse.spiceaclterritoryelement_id = ste.id INNER JOIN spiceaclterritoryelementvalues stv ON stv.spiceaclterritoryelement_id = ste.id AND stv.elementvalue = stse.elementvalue WHERE stse.spiceaclterritory_id ='$territoryId'");

        $elementvalues = array();

        while ($thisElement = $this->db->fetchByAssoc($elementsObj)) {
            $elementvalues[$thisElement['spiceaclterritoryelement_id']] = $thisElement;
        }

        return $elementvalues;
    }

    public function addFTSData($bean)
    {
        $indexArray = [];
        $indexArray['spiceacl_primary_territory'] = $bean->spiceacl_primary_territory;
        $indexArray['spiceacl_territories_hash'] = $bean->spiceacl_territories_hash;
        return $indexArray;
    }

    function checkBeanAccessforACLObject($bean, $aclobjectid){
        //Coming from list view, bean is empty  and no territory_hash
        //therefore return true
        if(empty($bean->id))
            return true;

        $type = $this->getTypeForModule($bean->_module ?: $bean->module_dir);
        if(!$type)
            return true;

        $record = $this->db->fetchByAssoc($this->db->query("SELECT count(*) hash_count FROM spiceaclobjects_hash WHERE spiceaclobject_id = '$aclobjectid' AND hash_id = '$bean->spiceacl_territories_hash'"));
        if($record['hash_count'] > 0)
            return true;
        else
            return false;
    }

    public function mapToRestArray($restArray)
    {
        $restArray['elementvalues'] = $this->elementvalues;
        return $restArray;
    }


    public function save($check_notify = false, $index_bean = true)
    {
        $beanData = parent::save($check_notify, $index_bean);

        foreach ($this->elementvalues as $elementvalueid => $elementvaluedata) {
            // check if record exists
            if ($this->db->fetchByAssoc($this->db->query("SELECT * FROM spiceaclt_spiceacltelemenv WHERE spiceaclterritory_id='$this->id' AND spiceaclterritoryelement_id='$elementvalueid'"))) {
                $this->db->query("UPDATE spiceaclt_spiceacltelemenv SET elementvalue='{$elementvaluedata['elementvalue']}' WHERE spiceaclterritory_id='$this->id' AND spiceaclterritoryelement_id='$elementvalueid'");
            } else {
                $this->db->query("INSERT INTO spiceaclt_spiceacltelemenv (spiceaclterritory_id, spiceaclterritoryelement_id, elementvalue) VALUES('$this->id','$elementvalueid', '{$elementvaluedata['elementvalue']}')");
            }
        }

        return $beanData;
    }

    public function mark_deleted($id)
    {
        $this->retrieve($id);
        if (!$this->inactive || $this->usagecount > 0)
            return false;

        // delete all related records
        $this->db->query("DELETE FROM spiceaclt_spiceacltelemenv WHERE spiceaclterritory_id='$id'");
        return parent::mark_deleted($id);
    }

    public function mapFromRestArray($post_params)
    {
        $this->elementvalues = $post_params['elementvalues'];
    }


    /*
     * get hashes for ACL Object
     */
    public function getTerritoryHashesForObject($objectId, $typeId){

        // check if the type is territory managed
        $typeModule = $this->db->fetchByAssoc($this->db->query("SELECT module FROM sysmodules WHERE id='$typeId' UNION SELECT module FROM syscustommodules WHERE id='$typeId'"));
        if(!$typeModule || !$this->getTypeForModule($typeModule['module']))
            return false;

        $hashes = [];
        $hashesObj = $this->db->query("SELECT hash_id FROM spiceaclobjects_hash WHERE spiceaclobject_id='$objectId'");
        while($hash = $this->db->fetchByAssoc($hashesObj)){
            $hashes[] = $hash['hash_id'];
        }
        return $hashes;
    }
    /*
     * get the secondary elements
     * former name: getOrgObjectsForHash
     */
    public function getTerritoryObjectsForHash($hash_id)
    {
        $db = DBManagerFactory::getInstance();
        $retArray = array();
        $objectsObj = $db->query("SELECT to.* FROM spiceaclterritories to INNER JOIN spiceaclterritories_hash toh ON toh.spiceaclterritory_id = to.id WHERE toh.hash_id='$hash_id' ORDER BY to.name ASC");
        while ($thisOrgObject = $db->fetchByAssoc($objectsObj)) {
            $retArray[$thisOrgObject['id']] = array(
                'id' => $thisOrgObject['id'],
                'name' => $thisOrgObject['name']
            );
        }
        return $retArray;
    }

    /*
     * get the secondary elements
     * former name: getOrgUsersForHash
     */

    public function getUsersForHash($hash_id)
    {
        $db = DBManagerFactory::getInstance();
        $retArray = array();
        $userBean = BeanFactory::getBean('Users');
        $usersObj = $db->query("SELECT * FROM spiceaclusers_hash WHERE hash_id='$hash_id'");
        while ($usersEntry = $db->fetchByAssoc($usersObj)) {
            $userBean->retrieve($usersEntry['user_id']);
            $retArray[$usersEntry['user_id']] = array(
                'id' => $usersEntry['user_id'],
                'name' => $userBean->name
            );
        }
        return $retArray;
    }

    /*
     * called from SugarBean
     */

    public function save_orghash(&$bean)
    {
        if ($bean->korgobjectmultiple != '') {
            $orgObject = json_decode(html_entity_decode($bean->korgobjectmultiple));

            //\SpiceCRM\includes\Logger\LoggerManager::getLogger()->debug('KORGOBJECT saving orgobject for bean ' . $bean->object_name . ' with id ' . $bean->id . ' korgobjectmain ' . $bean->korgobjectmain . ' with korgobjectmultiple ' . print_r($orgObject, true));
            // make sure we have a primary object in the call ... might come from an api where this is not filled
            // then we cannot write since we woudl dlete the assignment
            // B&R specific since we do no allow multiple management
            if (!empty($bean->korgobjectmain) && $bean->korgobjectmain != $orgObject->primary)
                $orgObject->primary = $bean->korgobjectmain;

            if ($orgObject->primary != null || !empty($bean->korgobjectmain)) {
                // load the orgobejcts array
                $korgobjects = $orgObject->secondary;

                // add the primary id
                // 2013-03-18 in some cases the array might not be populated initially when records are created
                $korgobjects[] = (empty($orgObject->primary) ? $bean->korgobjectmain : $orgObject->primary);

                // sort the array
                sort($korgobjects);

                // implode the array
                $thisObjectHash = md5(implode('', $korgobjects));

                //\SpiceCRM\includes\Logger\LoggerManager::getLogger()->debug('KORGOBJECT hash = ' . $thisObjectHash . ' for array ' . print_r($korgobjects, true));

                if ($bean->korgobjecthash != $thisObjectHash || $bean->korgobjecthash == '') {
                    // see if we need to write it to the DB if we do not know thiss combination yet
                    $this->checkOrgHashKey($thisObjectHash, $korgobjects, get_class($bean));

                    // if the hash key is changing see if we can remove the old hashkey
                    if ($bean->korgobjecthash != '')
                        $this->removeOrgHashKey($bean->korgobjecthash, get_class($bean), $bean->id);

                    // write it back to the bean
                    $bean->korgobjecthash = $thisObjectHash;
                }
            }
        } else {

        }

        if ($bean->korgusermultiple != '') {
            $orgUser = json_decode(html_entity_decode($bean->korgusermultiple));

            // load the orgobejcts array
            $korgusers = $orgUser->secondary;

            if (count($korgusers) > 0) {
                // add the primary id
                // $korgusers[] = $orgUser->primary;
                // sort the array
                sort($korgusers);

                // implode the array

                $thisUserHash = md5(implode('', $korgusers));
                if ($bean->korguserhash != $thisUserHash || $bean->korguserhash == '') {
                    // see if we need to write it to the DB if we do not know thiss combination yet
                    $this->checkUsrHashKey($thisUserHash, $korgusers);

                    // if the hash key is changing see if we can remove the old hashkey
                    if ($bean->korguserhash != '')
                        $this->removeUsrHashKey($bean->korguserhash, $bean->id);

                    // write it back to the bean
                    $bean->korguserhash = $thisUserHash;
                }
            } else {

                // if the hash key is changing see if we can remove the old hashkey
                if ($bean->korguserhash != '')
                    $this->removeUsrHashKey($bean->korguserhash, $bean->id);

                // set to empty ... no users asgined
                $bean->korguserhash = '';
            }
        }
    }

    /**
     * deprecated
     * replaced by checkUserHash
     * @param $hash
     * @param $users
     */
    public function checkUsrHashKey($hash, $users)
    {
        $db = DBManagerFactory::getInstance();
        // check if the hash exists and if not create it on the DB
        $query = "select * from korgusers_hash where hash_id = '" . $hash . "' and deleted = 0";
        $result = $db->query($query);

        if (!$db->fetchByAssoc($result)) {
            $query = "INSERT INTO korgusers_hash (hash_id, user_id, deleted) VALUES ";
            $i = 0;
            foreach ($users as $value) {
                if ($value != '') {
                    if ($i == 0) {
                        $query .= "('$hash', '$value', 0)";
                    } else {
                        $query .= ", ('$hash', '$value', 0)";
                    }
                    $i++;
                }
            }
            $db->query($query, true, "Error insert user_ids: ");
        }
    }
    public function checkUserHash($hash, $users)
    {
        $db = DBManagerFactory::getInstance();
        // check if the hash exists and if not create it on the DB
        $query = "select * from spiceaclusers_hash where hash_id = '" . $hash . "' and deleted = 0";
        $result = $db->query($query);

        if (!$db->fetchByAssoc($result)) {
            $query = "INSERT INTO spiceaclusers_hash (hash_id, user_id, deleted) VALUES ";
            $i = 0;
            foreach ($users as $value) {
                if ($value != '') {
                    if ($i == 0) {
                        $query .= "('$hash', '$value', 0)";
                    } else {
                        $query .= ", ('$hash', '$value', 0)";
                    }
                    $i++;
                }
            }
            $db->query($query, true, "Error insert user_ids: ");
        }
    }

    /**
     * deprecated
     * replaced by removeUserHash
     * @param $hash
     * @param $users
     */
    public function removeUsrHashKey($hash, $thisBeanId)
    {
        $db = DBManagerFactory::getInstance();

        // get all modules that have a multi assignment
        $modulesObj = $db->query("SELECT module FROM korgobjecttypes_modules WHERE multipleusers = '1'");

        $hashUsed = false;

        while ($thisModule = $db->fetchByAssoc($modulesObj)) {
            $seed = BeanFactory::getBean($thisModule['module']);
            $query = "SELECT id FROM $seed->table_name WHERE korguserhash='$hash' && id<>'$thisBeanId'";
            $countObj = $db->query($query);
            if ($db->getRowCount($countObj) > 0) {
                $hashUsed = true;
                break;
            }
        }

        // if not used we delete all entries of the Hash Key ... performance ...
        if (!$hashUsed) {
            $db->query("DELETE FROM korgusers_hash WHERE hash_id='$hash'");
        }
    }
    public function removeUserHash($hash, $thisBeanId)
    {
        $db = DBManagerFactory::getInstance();

        // get all modules that have a multi assignment
        // $modulesObj = $db->query("SELECT module FROM spiceaclterritories_modules WHERE multipleusers = '1'");
        $modulesObj = $db->query("SELECT module FROM sysmodules WHERE acl_multipleusers = '1' UNION SELECT module FROM syscustommodules WHERE acl_multipleusers = '1'");

        $hashUsed = false;

        while ($thisModule = $db->fetchByAssoc($modulesObj)) {
            $seed = BeanFactory::getBean($thisModule['module']);
            $query = "SELECT id FROM $seed->table_name WHERE spiceacl_users_hash='$hash' && id<>'$thisBeanId'";
            $countObj = $db->query($query);
            if ($db->getRowCount($countObj) > 0) {
                $hashUsed = true;
                break;
            }
        }

        // if not used we delete all entries of the Hash Key ... performance ...
        if (!$hashUsed) {
            $db->query("DELETE FROM spiceaclusers_hash WHERE hash_id='$hash'");
        }
    }
    /*
     * function to check wether the hash key exists ...
     * if it does not add it to the db and build all links
     */

    public function checkTerritoryHash($hash, $territories, $module)
    {
        $db = DBManagerFactory::getInstance();
        // check if the hash exists and if not create it on the DB
        $query = "select * from spiceaclterritories_hash where hash_id = '" . $hash . "' and deleted = 0";
        $result = $db->query($query);

        if ($db->getRowCount($result) == 0) {
            $query = "INSERT INTO spiceaclterritories_hash (hash_id, spiceaclterritory_id, deleted) VALUES ";
            $first = true;
            foreach ($territories as $value) {
                if ($first) {
                    $query .= "('$hash', '$value', 0)";
                    $first = false;
                } else {
                    $query .= ", ('$hash', '$value', 0)";
                }
            }

            $db->query($query, true, "Error insert territory hash IDs: ");

            // TODO if new is created process orgprofiles and see in which allocation table we need to add this ...
            // tbd call to orgprofiles

            $this->activateTerritoryHash($hash, $territories, $module);

            /*
            require_once('modules/KAuthProfiles/KAuthProfile.php');
            $thisKauthObject = new KAuthObject();
            $thisKauthObject->addKOrgObjectHash($hash, $korgobjects, $beanclass);
            */
        }
    }

    private function activateTerritoryHash($hash, $territories, $module)
    {
        $elementValuesArray = [];
        // get the element Values
        foreach ($territories as $territory) {
            $elementValues = $this->db->query("SELECT spiceaclterritoryelement_id, elementvalue FROM spiceaclt_spiceacltelemenv WHERE spiceaclterritory_id = '$territory'");
            while ($elementValue = $this->db->fetchByAssoc($elementValues)) {
                $elementValuesArray[$territory][$elementValue['spiceaclterritoryelement_id']] = $elementValue['elementvalue'];
            }
        }

        // get acl object type
        $acltype = $this->db->fetchByAssoc($this->db->query("SELECT id FROM sysmodules WHERE module = '$module' UNION SELECT id FROM syscustommodules WHERE module = '$module'"));
        $acltype = $acltype['id'];

        $queryArray = [];
        foreach ($elementValuesArray as $territoryId => $territoryValues) {
            $territory = BeanFactory::getBean('SpiceACLTerritories', $territoryId);
            // build the has query
            $query = "SELECT '$hash' hash_id, so.id FROM spiceaclobjects so";
            $i = 0;
            foreach ($territoryValues as $territoryValueID => $territoryValue) {
                $query .= " INNER JOIN spiceaclobjectsterritoryelementvalues sov{$i} ON so.id = sov{$i}.spiceaclobject_id AND sov{$i}.spiceaclterritoryelement_id = '$territoryValueID' AND (sov{$i}.value LIKE '%\"{$territoryValue}\"%' OR sov{$i}.value LIKE '%\"*\"%')";
                $i++;
            }
            $query .= " WHERE so.sysmodule_id = '$acltype' AND so.allorgobjects = 0 AND so.status = 'r'";
            $queryArray[] = $query;
        }

        // build the union query
        $totalQuery = implode(' UNION ', $queryArray);

        // clean hash
        $this->db->query("DELETE FROM spiceaclobjects_hash WHERE hash_id = '$hash'");

        // do the insert from the select
        $this->db->query("INSERT INTO spiceaclobjects_hash (hash_id, spiceaclobject_id) $totalQuery");

    }

    /*
     * checks wether the hash key is still in use
     * if no longer used removes it from the DB
     */

    public function removeTerritoryHash($hash, $beanobject, $thisBeanId)
    {
        $db = DBManagerFactory::getInstance();
        // get all modules where we use the type of object
        $modulesObj = $db->query("SELECT stp.module FROM spiceaclterritories_modules stp
						INNER JOIN spiceaclterritories_modules stm ON stp.spiceaclterritorytype_id = stp.spiceaclterritorytype_id
						WHERE stm.module='$beanobject' AND (stp.relatefrom IS NULL OR stp.relatefrom = '')");

        $profileUsed = false;

        while (!$profileUsed && $thisModule = $db->fetchByAssoc($modulesObj)) {
            $seed = BeanFactory::getBean($thisModule['module']);
            if($seed and !empty($seed->table_name)) {
                $query = "SELECT id FROM $seed->table_name WHERE spiceacl_territories_hash='$hash' && id<>'$thisBeanId'";
                $countObj = $db->query($query);
                if ($db->getRowCount($countObj) > 0) {
                    $profileUsed = true;
                    break;
                }
            }
        }

        // if not used we delete all entries of the Hash Key ... performance ...
        if (!$profileUsed) {
            $db->query("DELETE FROM spiceaclobjects_hash WHERE hash_id='$hash'");
            $db->query("DELETE FROM spiceaclterritories_hash WHERE hash_id='$hash'");
        }
    }


    /**
     * new function forthe UI
     */
    // todo ... das gehört angepasst
    public function getUserTerritoriesQuery($module, $activeterritories)
    {
        return "select spiceaclterritories.* from spiceaclterritories, spiceaclterritories_modules where spiceaclterritories.territorytype_id = spiceaclterritories_modules.spiceaclterritorytype_id and spiceaclterritories_modules.module = '$module' AND spiceaclterritories.id NOT IN ('" . implode("','", $activeterritories) . "')";
    }
    /**
     * new function forthe UI
     */
    // todo ... das gehört angepasst
    public function getUserTerritories($module, $withElementValues = false)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        $retArray = array();

        if ($current_user->is_admin) {
            $territorriesObj = $db->query("select spiceaclterritories.* from spiceaclterritories, spiceaclterritories_modules where spiceaclterritories.territorytype_id = spiceaclterritories_modules.spiceaclterritorytype_id and spiceaclterritories_modules.module = '$module'");
            while ($territorry = $db->fetchByAssoc($territorriesObj)) {
                $retArray[$territorry['id']] = array('id' => $territorry['id'], 'name' => $territorry['name']);
                if($withElementValues){
                    $retArray[$territorry['id']]['elementvalues'] = $this->getElementValues($territorry['id']);
                }
            }
        } else {
            $aclObject = BeanFactory::getBean('SpiceACLObjects');
            $userObjects = $aclObject->getUserACLObjects();

            $queryArray = [];
            foreach ($userObjects as $objectId => $objectData) {
                if ($objectData['module'] == $module) {
                    $query = "SELECT st.id, st.name, '".json_encode($objectData['objectactions'])."' actions FROM spiceaclterritories st";
                    $i = 0;
                    foreach ($objectData['objectterritoryvalues'] as $elementId => $elementIdValues) {
                        if (array_search('*', $elementIdValues) === false) {
                            $query .= " INNER JOIN spiceaclt_spiceacltelemenv stv" . $i . " ON st.id = stv" . $i . ".spiceaclterritory_id AND stv" . $i . ".spiceaclterritoryelement_id = '$elementId' AND stv" . $i . ".elementvalue in ('" . implode("','", $elementIdValues) . "') ";
                        }
                        $i++;
                    }
                    $query .= " WHERE st.territorytype_id = '{$this->getTypeForModule($module)}'";

                    $queryArray[] = $query;
                }
            }

            $territorriesObj = $db->query(implode(" UNION ", $queryArray));
            while ($territorry = $db->fetchByAssoc($territorriesObj)) {
                if(!isset($retArray[$territorry['id']])) {
                    $tmpActions = json_decode($territorry['actions']);
                    $retArray[$territorry['id']] = array(
                        'id' => $territorry['id'],
                        'name' => $territorry['name'],
                        'actions' => ($tmpActions ? $tmpActions : [])
                    );

                    if($withElementValues){
                        $retArray[$territorry['id']]['elementvalues'] = $this->getElementValues($territorry['id']);
                    }

                } else {
                    if($terActions = json_decode($territorry['actions'])) {
                        foreach ($terActions as $action) {
                            if (!array_search($aclObject, $retArray[$territorry['id']]['actions'])) {
                                $retArray[$territorry['id']]['actions'][] = $action;
                            }
                        }
                    } else{
                        $retArray[$territorry['id']]['actions'] = [];
                    }
                }
            }
        }

        // remove the keays from the array and return
        $newRetArray = [];
        foreach($retArray as $id => $data) $newRetArray[] = $data;
        return $newRetArray;

    }
}
