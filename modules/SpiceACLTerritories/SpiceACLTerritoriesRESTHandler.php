<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SpiceACLTerritories;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\includes\authentication\AuthenticationController;

class SpiceACLTerritoriesRESTHandler
{

    public function getUserTerritories($activeTerritories)
    {
        global $beanList;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $territorries = Array();

        $seed = BeanFactory::getBean('SpiceACLTerritories');
        $modulesObjects = $db->query("SELECT * FROM spiceaclterritories_modules");

        while ($modulesObject = $db->fetchByAssoc($modulesObjects)) {
            $territorries[$modulesObject['module']] = $seed->getUserTerritories($modulesObject['module']);
        }

        return $territorries;

    }

    public function getTerritory($id)
    {
        $seed = BeanFactory::getBean('SpiceACLTerritories', $id);
        return array(
            'id' => $seed->id,
            'name' => $seed->name
        );
    }

    public function getTerritoriesByHash($hash_id)
    {
        $db = DBManagerFactory::getInstance();
        $retArray = [];
        $territories = $db->query("SELECT * FROM spiceaclterritories_hash WHERE hash_id = '$hash_id'");
        while ($territory = $db->fetchByAssoc($territories)) {
            $seed = BeanFactory::getBean('SpiceACLTerritories', $territory['spiceaclterritory_id']);
            $retArray[] = array(
                'id' => $seed->id,
                'name' => $seed->name
            );
        }

        return $retArray;
    }

    public function getOrgElements()
    {
        $db = DBManagerFactory::getInstance();

        $elementsArray = array();

        $orgElements = $db->query("SELECT * FROM spiceaclterritoryelements");
        while ($orgElement = $db->fetchByAssoc($orgElements))
            $elementsArray[] = $orgElement;

        return $elementsArray;
    }

    public function setOrgElement($id, $params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $record = $db->fetchByAssoc($db->query("SELECT id FROM spiceaclterritoryelements WHERE id = '$id'"));
        if ($record)
            $db->query("UPDATE spiceaclterritoryelements SET name='" . $params['name'] . "' WHERE id = '$id'");
        else
            $db->query("INSERT INTO spiceaclterritoryelements (id, name) values('$id', '" . $params['name'] . "')");

        return true;
    }

    public function deleteOrgElement($id)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');


        $db = DBManagerFactory::getInstance();

        // delete element
        $db->query("DELETE FROM spiceaclterritoryelements WHERE id = '$id'");
        // delete values
        $db->query("DELETE FROM spiceaclterritoryelementvalues where spiceaclterritoryelement_id='$id'");

        return true;
    }

    public function getOrgElementValues($orgelementId = '')
    {
        $db = DBManagerFactory::getInstance();

        $elementValuesArray = array();

        $orgElementValues = $db->query("SELECT * FROM spiceaclterritoryelementvalues where spiceaclterritoryelement_id='" . $orgelementId . "'");
        while ($orgElementValue = $db->fetchByAssoc($orgElementValues))
            $elementValuesArray[] = $orgElementValue;

        return $elementValuesArray;
    }

    public function setOrgElementValues($params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $record = $db->fetchByAssoc($db->query("SELECT * FROM spiceaclterritoryelementvalues WHERE spiceaclterritoryelement_id = '" . $params['spiceaclterritoryelement_id'] . "' AND elementvalue = '" . $params['elementvalue'] . "'"));
        if ($record) {
            $db->query("UPDATE spiceaclterritoryelementvalues SET elementdescription='" . $params['elementdescription'] . "' WHERE spiceaclterritoryelement_id = '" . $params['spiceaclterritoryelement_id'] . "' AND elementvalue = '" . $params['elementvalue'] . "'");
            // \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('update ' . "UPDATE korgobjectelementvalues SET elementdescription='" . $params['elementdescription'] . "' WHERE korgobjectelement_id = '" . $params['korgobjectelement_id'] . "' AND elementvalue = '" . $params['elementvalue'] . "'")
        } else
            $db->query("INSERT INTO spiceaclterritoryelementvalues (spiceaclterritoryelement_id, elementvalue, elementdescription) values('" . $params['spiceaclterritoryelement_id'] . "', '" . $params['elementvalue'] . "', '" . $params['elementdescription'] . "')");

        return true;
    }

    public function deleteOrgElementValues($spiceaclterritoryelement_id, $elementvalue)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $db->query("DELETE FROM spiceaclterritoryelementvalues WHERE spiceaclterritoryelement_id = '$spiceaclterritoryelement_id' AND elementvalue = '$elementvalue'");

        return true;
    }

    /**
     * used in KReporter backend interface
     * @param $params
     * @return array|bool
     */
    public function getTerritoriesForModule($params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin) {
            http_response_code(401);
            return false;
        }

        $db = DBManagerFactory::getInstance();

        $retArray = array();

        if($params['searchterm'])
            $addWhere = " AND spiceaclterritories.name like '%".$params['searchterm']."%' ";

        if(!isset($params['start'])) $params['start'] = 0;
        if(!isset($params['limit'])) $params['limit'] = -1;

        $records = $db->limitQuery("SELECT spiceaclterritories.*, "
            . "(SELECT COUNT(hash_id) FROM spiceaclterritories_hash WHERE spiceaclterritory_id = spiceaclterritories.id) usagecount "
            . "FROM spiceaclterritories INNER JOIN spiceaclterritories_modules kotm ON kotm.module='".$params['module']."' AND kotm.spiceaclterritorytype_id = spiceaclterritories.territorytype_id $addWhere "
            . "ORDER BY name ASC ",$params['start'], $params['limit']);
        while ($record = $db->fetchByAssoc($records))
            $retArray[] = $record;

        $total = $db->fetchByAssoc($db->query("SELECT count(*) totalcount FROM spiceaclterritories INNER JOIN spiceaclterritories_modules kotm ON kotm.module='".$params['module']."' AND kotm.spiceaclterritorytype_id = spiceaclterritories.territorytype_id $addWhere "));

        return array(
            'items' => $retArray,
            'total' => $total['totalcount']
        );
    }

    public function getOrgObjectTypes()
    {
        $db = DBManagerFactory::getInstance();

        $elementValuesArray = array();

        $orgElementValues = $db->query("SELECT * FROM spiceaclterritorytypes");
        while ($orgElementValue = $db->fetchByAssoc($orgElementValues))
            $elementValuesArray[] = $orgElementValue;

        return $elementValuesArray;
    }

    public function getOrgObjectTypeByModule($module)
    {
        $db = DBManagerFactory::getInstance();

        $type = $db->fetchByAssoc($db->query("SELECT spiceaclterritorytype_id FROM spiceaclterritories_modules WHERE module = '$module'"));
        if ($type) {
            return $this->getOrgObjectType($type['spiceaclterritorytype_id']);
        }

        return array(
            'elementvalues' => array(),
            'elements' => array()
        );
    }

    public function getOrgObjectType($spiceaclterritorytype)
    {
        $db = DBManagerFactory::getInstance();

        $retArray = array(
            'elementvalues' => array(),
            'elements' => array()
        );

        $elements = $db->query("select spiceaclterritoryelements.* from spiceaclttypes_spiceacltelem, spiceaclterritoryelements where spiceaclterritoryelements.id = spiceaclttypes_spiceacltelem.spiceaclterritoryelement_id and spiceaclttypes_spiceacltelem.spiceaclterritorytype_id = '$spiceaclterritorytype'");
        while ($element = $db->fetchByAssoc($elements))
            $retArray['elements'][] = $element;


        $elementValues = $db->query("SELECT spiceaclterritoryelementvalues.* FROM spiceaclterritoryelementvalues, spiceaclttypes_spiceacltelem WHERE spiceaclterritoryelementvalues.spiceaclterritoryelement_id = spiceaclttypes_spiceacltelem.spiceaclterritoryelement_id AND spiceaclttypes_spiceacltelem.spiceaclterritorytype_id='$spiceaclterritorytype'");
        while ($elementValue = $db->fetchByAssoc($elementValues)) {
            $elementValue['elementdescription'] .= ' (' . $elementValue['elementvalue'] . ')';
            $retArray['elementvalues'][] = $elementValue;
        }

        return $retArray;
    }

    public function setOrgObjectTypes($id, $params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $record = $db->fetchByAssoc($db->query("SELECT * FROM spiceaclterritorytypes WHERE id = '" . $params['id'] . "'"));
        if ($record) {
            $db->query("UPDATE spiceaclterritorytypes SET name='" . $params['name'] . "' WHERE id = '" . $params['id'] . "'");
        } else
            $db->query("INSERT INTO spiceaclterritorytypes (id, name) values('" . $params['id'] . "', '" . $params['name'] . "')");

        return true;
    }

    public function deleteOrgObjectType($id)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $db->query("DELETE FROM spiceaclterritorytypes WHERE id = '" . $id . "'");

        return true;
    }

    public function getOrgObjectTypeElements($spiceaclterritorytype_id)
    {
        $db = DBManagerFactory::getInstance();

        $elementValuesArray = array();

        $orgElementValues = $db->query("select ttte.spiceaclterritoryelement_id, ttte.spiceaclterritorytype_id, ste.name, ttte.sequence from spiceaclttypes_spiceacltelem ttte, spiceaclterritoryelements ste WHERE ttte.spiceaclterritoryelement_id = ste.id and ttte.spiceaclterritorytype_id = '$spiceaclterritorytype_id'");
        while ($orgElementValue = $db->fetchByAssoc($orgElementValues))
            $elementValuesArray[] = $orgElementValue;

        return $elementValuesArray;
    }

    public function setOrgObjectTypeElement($params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $record = $db->fetchByAssoc($db->query("select * from spiceaclttypes_spiceacltelem WHERE spiceaclterritoryelement_id = '{$params['spiceaclterritoryelement_id']}' and spiceaclterritorytype_id = '{$params['spiceaclterritorytype_id']}'"));
        if ($record) {
            $db->query("UPDATE spiceaclttypes_spiceacltelem SET sequence='{$params['sequence']}' WHERE spiceaclterritoryelement_id = '{$params['spiceaclterritoryelement_id']}' and spiceaclterritorytype_id = '{$params['spiceaclterritorytype_id']}'");
        } else
            $db->query("INSERT INTO spiceaclttypes_spiceacltelem (spiceaclterritoryelement_id, spiceaclterritorytype_id, sequence) values('{$params['spiceaclterritoryelement_id']}', '{$params['spiceaclterritorytype_id']}', '{$params['sequence']}')");

        return true;
    }

    public function deleteOrgObjectTypeElements($spiceaclterritoryelement_id, $spiceaclterritorytype_id)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $db->query("DELETE FROM spiceaclttypes_spiceacltelem WHERE spiceaclterritoryelement_id = '$spiceaclterritoryelement_id' and spiceaclterritorytype_id = '$spiceaclterritorytype_id'");

        return true;
    }


    public function getOrgObjectTypeModules()
    {
        $db = DBManagerFactory::getInstance();

        $retArray = array();

        $records = $db->query("SELECT * FROM spiceaclterritories_modules ORDER BY module ASC");
        while ($record = $db->fetchByAssoc($records))
            $retArray[] = $record;

        return $retArray;
    }

    public function setOrgObjectTypeModule($params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $record = $db->fetchByAssoc($db->query("select * from spiceaclterritories_modules WHERE module = '{$params['module']}'"));
        if ($record) {
            $db->query("UPDATE spiceaclterritories_modules SET spiceaclterritorytype_id='{$params['spiceaclterritorytype_id']}', relatefrom='{$params['relatefrom']}', multipleobjects='" . $params['multipleobjects'] . "', multipleusers='" . $params['multipleusers'] . "', suppresspanel='" . $params['suppresspanel'] . "' WHERE module = '{$params['module']}'");
        } else
            $db->query("INSERT INTO spiceaclterritories_modules (module, spiceaclterritorytype_id, relatefrom, multipleobjects, multipleusers, suppresspanel) values('{$params['module']}', '{$params['spiceaclterritorytype_id']}', '{$params['relatefrom']}', '" . $params['multipleobjects'] . "', '" . $params['multipleusers'] . "', '" . $params['suppresspanel'] . "')");

        return true;
    }

    public function deleteOrgObjectTypeModules($module)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $db->query("DELETE FROM spiceaclterritories_modules WHERE module = '$module'");

        return true;
    }

    public function getTerritories($params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $retArray = array();

        // build the filter
        $addWhere = "spiceaclterritories.territorytype_id = '{$params['territorytype_id']}'";
        if ($params['searchterm'])
            $addWhere .= " AND spiceaclterritories.name like '%" . $params['searchterm'] . "%' ";

        //$records = $db->limitQuery("SELECT * FROM spiceaclterritories WHERE territorytype_id = '" . $params['territorytype_id'] . "' $addWhere ORDER BY name ASC", $params['start'] ?: 0, $params['limit'] > 0 ? $params['limit'] : 20);
        $seed = BeanFactory::getBean('SpiceACLTerritories');
        $records = $seed->get_list('name', $addWhere, $params['start'] ?: 0, $params['limit'] > 0 ? $params['limit'] : 250);

        $moduleHandle = new ModuleHandler();
        foreach ($records['list'] as $record) {
            $retArray[] = $moduleHandle->mapBeanToArray('SpiceACLTerritories', $record);
        }

        return $retArray;
    }

    public function addTerritory($id, $params)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        /*
        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
        $select = "SELECT count(*) ocount FROM korgobjects ko";
        $join = '';
        $where = "WHERE ko.korgobjecttype_id='".$params['korgobjecttype']."'";

        foreach($params['valueData'] as $valueCount => $valueSet){
            $join .= " INNER JOIN korgobjects_korgooe koe" . $valueCount . " ON ko.id = koe".$valueCount .".korgobject_id";
            $where .= " AND koe".$valueCount.".korgobjectelement_id='".$valueSet['korgobjectelement_id']."' AND koe".$valueCount.".elementvalue='".$valueSet['elementvalue']."'";
        }

        $dbCount = $db->fetchByAssoc($db->query($select.' '.$join.' '.$where));
        if($dbCount && $dbCount['ocount']> 0) {
            return array(
                'status' => 'error',
                'msg' => 'territorry exists already'
            );
        }

        */

        $moduleHandle = new ModuleHandler();
        $beanData = $moduleHandle->add_bean('SpiceACLTerritories', $id, $params);

        /*
        $newObject = \SpiceCRM\data\BeanFactory::getBean('KOrgObjects');
        $newObject->name = $params['name'];
        $newObject->korgobjecttype_id = $params['korgobjecttype'];
        $newObject->save();

        foreach($params['valueData'] as $valueSet){
            $db->query("INSERT INTO korgobjects_korgooe (korgobject_id, korgobjectelement_id, elementvalue) VALUES ('".$newObject->id."', '".$valueSet['korgobjectelement_id']."', '".$valueSet['elementvalue']."')");
        }
        */


        return array(
            'status' => 'success',
            'id' => $params['id'],
            'data' => $beanData
        );
    }

    public function checkTerritory($params)
    {

        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();
        $select = "SELECT count(*) ocount FROM spiceaclterritories st";
        $join = '';
        $where = "WHERE st.territorytype_id='" . $params['territorytype_id'] . "'";

        $count = 0;
        foreach ($params['elementvalues'] as $valueCount => $valueSet) {
            $join .= " INNER JOIN spiceaclt_spiceacltelemenv ste" . $count . " ON st.id = ste" . $count . ".spiceaclterritory_id";
            $where .= " AND ste" . $count . ".spiceaclterritoryelement_id='" . $valueSet['spiceaclterritoryelement_id'] . "' AND ste" . $count . ".elementvalue='" . $valueSet['elementvalue'] . "'";
            $count++;
        }

        $dbCount = $db->fetchByAssoc($db->query($select . ' ' . $join . ' ' . $where));
        if ($dbCount && $dbCount['ocount'] > 0) {
            return false;
        }

        return true;

    }


    public function deleteTerritorry($territoryId)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $seed = BeanFactory::getBean('SpiceACLTerritories', $territoryId);

        if ($seed && $seed->inactive && $seed->usagecount == 0) {
            $seed->mark_deleted($territoryId);
        } else {
            throw (new ForbiddenException('territory cannot be deleted'))->setErrorCode('still active or used');
        }

        return true;
    }


    public function getOrgObjectValues($objectid)
    {
        if (!AuthenticationController::getInstance()->getCurrentUser()->is_admin)
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        $db = DBManagerFactory::getInstance();

        $retArray = array();

        $records = $db->query("SELECT * FROM korgobjects_korgooe WHERE korgobject_id = '$objectid'");
        while ($record = $db->fetchByAssoc($records))
            $retArray[] = $record;

        return $retArray;
    }
}
