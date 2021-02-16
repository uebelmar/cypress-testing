<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

class KOrgAccess
{

    /*
    * check if a module is org managed
    */
    private $orgManagedModules;
    private $relationShip;
    private $beanRelRight;

    public function orgManaged($module)
    {
        $db = DBManagerFactory::getInstance();

        // check if we know the module already
        if (isset($_SESSION['kauthaccess']['orgmanaged'][$module]))
            return $_SESSION['kauthaccess']['orgmanaged'][$module];

        // if not we query the database
        if ($db->fetchByAssoc($db->query("SELECT * FROM korgobjecttypes_modules WHERE module='$module'"))) {
            $this->orgManagedModules[$module] = true;
            $_SESSION['kauthaccess']['orgmanaged'][$module] = true;
            return true;
        } else {
            $this->orgManagedModules[$module] = false;
            $_SESSION['kauthaccess']['orgmanaged'][$module] = false;
            return false;
        }
    }

    public function saveOrghash(&$bean){
        $thisOrgObject = BeanFactory::getBean('KOrgObjects');
        $thisOrgObject->save_orghash($bean);
    }

    /*
     * passing in the bean as well as the previous assessed related access
     * need to handle this from up to just run once through the related acces check
     * but to be able to evaluate it as part of the profile
     */

    private function checkCoOwner($bean)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        // $bean->retrieve($bean->id);
        $coAccess = false;
        if ($bean->korguserhash != '' && $db->getRowCount($db->query("SELECT * FROM korgusers_hash WHERE hash_id='" . $bean->korguserhash . "' AND user_id = '" . $current_user->id . "'")))
            $coAccess = true;

        return $coAccess;
    }

    private function getKauthObjectOrgHash($bean)
    {
        if ($this->relationShip == '') {
            return "WHERE hash_id = (SELECT korgobjecthash FROM $bean->table_name WHERE id='$bean->id')";
        } else {
            if (empty($this->relationShip['join_table'])) {
                return 'WHERE hash_id IN(SELECT korgobjecthash FROM ' .
                ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) .
                ' WHERE ' .
                ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . '.' . ($this->beanRelRight ? $this->relationShip['lhs_key'] : $this->relationShip['rhs_key']) . '=\'' . $bean->id . '\')';
            } else {
                return 'WHERE hash_id IN (SELECT korgobjecthash FROM ' .
                ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) .
                ' LEFT JOIN ' .
                $this->relationShip['join_table'] .
                ' ON ' .
                $this->relationShip['join_table'] . '.' . ($this->beanRelRight ? $this->relationShip['join_key_lhs'] : $this->relationShip['join_key_rhs']) . ' = ' . ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . '.' . ($this->beanRelRight ? $this->relationShip['lhs_key'] : $this->relationShip['rhs_key']) .
                ' WHERE ' .
                $this->relationShip['join_table'] . '.' . ($this->beanRelRight ? $this->relationShip['join_key_rhs'] : $this->relationShip['join_key_lhs']) . '=\'' . $bean->id . '\')';;
            }
        }

        return " WHERE 1 = 1";
    }


    /*
     * function to match AuthValues to bean values
     */

    private function matchOrgArrays($authArray, $orgArray)
    {
        foreach ($authArray as $orgElementId => $orgElementValue) {
            $thisMatch = false;
            $orgValues = json_decode(html_entity_decode($orgElementValue));
            foreach ($orgValues as $thisOrgvalue) {
                if ($thisOrgvalue == $orgArray[$orgElementId])
                    $thisMatch = true;
            }

            if (!$thisMatch)
                return false;
        }
        return true;
    }

    public function checkBeanAccess($bean, $authObject, $relatedAccess)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        $orgAccess = false;
        // check Org Assignments if it is not an all access, a related or a ignore flag
        if ($authObject->objDetail['allorgobjects'] == 0 && $authObject->objDetail['kauthorgassignment'] == 0) {
            $authObjectOrgValuesArray = array();
            $objectOrgValues = $db->query("SELECT * FROM kauthobjectorgelementvalues WHERE kauthobject_id='$authObject->id'");
            while ($thisObjectOrgValue = $db->fetchByAssoc($objectOrgValues)) {
                $thisObjectOrgValue['value'] = html_entity_decode($thisObjectOrgValue['value']);
                if ($thisObjectOrgValue['value'] != '["*"]')
                    $authObjectOrgValuesArray[$thisObjectOrgValue['korgobjectelement_id']] = $thisObjectOrgValue['value'];
            }
            // see if we have values to match or non specified or all are *
            if (count($authObjectOrgValuesArray) > 0) {
                // get all Orgunits for the bean
                $orgUnitQuery = "SELECT korgobjects_korgooe.* FROM korgobjects_korgooe INNER JOIN korgobjects_hash ON korgobjects_korgooe.korgobject_id = korgobjects_hash.korgobject_id ";

                $orgUnitQuery .= $this->getKauthObjectOrgHash($bean);

                $orgObjectsObj = $db->query($orgUnitQuery);
                $orgObjectsValuesArray = array();
                while ($thisOrgObject = $db->fetchByAssoc($orgObjectsObj)) {
                    $orgObjectsValuesArray[$thisOrgObject['korgobject_id']][$thisOrgObject['korgobjectelement_id']] = $thisOrgObject['elementvalue'];
                }

                // start matching
                foreach ($orgObjectsValuesArray as $orgObjectID => $orgObjectValues) {
                    if ($this->matchOrgArrays($authObjectOrgValuesArray, $orgObjectValues)) {
                        $orgAccess = ($authObject->objDetail['kauthowner'] && ($bean->assigned_user_id == $current_user->id || $this->checkCoOwner($bean)) ? true : ($authObject->objDetail['kauthowner'] ? false : true));
                        break;
                    }
                }
            } else {
                $orgAccess = ($authObject->objDetail['kauthowner'] && ($bean->assigned_user_id == $current_user->id || $this->checkCoOwner($bean)) ? true : ($authObject->objDetail['kauthowner'] ? false : true));
            }
        } // if we have a related access check for user assignment and if not the assigned user return the related access flag
        elseif ($authObject->objDetail['kauthorgassignment'] == 2) {
            $orgAccess = ($authObject->objDetail['kauthowner'] && ($bean->assigned_user_id == $current_user->id || $this->checkCoOwner($bean)) ? true : $relatedAccess);
        } else
            $orgAccess = ($authObject->objDetail['kauthowner'] && ($bean->assigned_user_id == $current_user->id || $this->checkCoOwner($bean)) ? true : ($authObject->objDetail['kauthowner'] ? false : true));

        return $orgAccess;
    }

    /*
 * separate function to just build the org Where Clause ... required for the relate field
 * $subSELECT controls wethre the org elöeents are determined in separate statement and then are included in an IN clause
 */

    public function getObjectOrgWhereClause($tableName, $authObject)
    {
        $db = DBManagerFactory::getInstance();
        $subSELECT = false;
        $inString = '';
        // build the WHERE for the org hash
        // group concat will only work with MYSQL .. need to change that for MSSQL ..
        if ($authObject->objDetail['allorgobjects'] == 0 && $authObject->objDetail['kauthorgassignment'] != 1 && $authObject->objDetail['kauthorgassignment'] != 3) {
            switch ($this->objDetail['kauthorgassignment']) {
                case '2':
                    // get the relationship for this object
                    // $this->getKauthObjectRelationship();
                    // $thisHashIds = $db->fetchByAssoc
                    break;
                default:
                    if (!$subSELECT) {
                        $db->query('SET SESSION group_concat_max_len = 10000');
                        $thisHashString = $db->fetchByAssoc($db->query("SELECT concat(\"'\", group_concat(distinct(hash_id) separator \"','\"), \"'\") as hashstring FROM kauthobjects_hash WHERE kauthobject_id='$authObject->id'"));
                        if (!empty($thisHashString['hashstring']))
                            $inString = html_entity_decode($thisHashString['hashstring'], ENT_QUOTES);
                        else
                            $inString = '\'---noObject---\'';
                    } else
                        $inString = "SELECT hash_id FROM kauthobjects_hash WHERE kauthobject_id='$authObject->id'";
                    break;
            }

            // check for the Org Unit Picker ... if that adds

            if ($inString != '')
                $orgWhere = $this->getKAuthObjectHashWhere($tableName, $inString, '');
        } // check if we should still check that thje bean has an orgassignment
        elseif ($authObject->objDetail['kauthorgassignment'] != 3) {
            // check for the Org Unit Picker ... if that adds
            $addInString = '';
            /*
              if (!empty(\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['KOrgObjects']['filtertype']) && !empty($_SESSION['korgmanagement']['objectfilter']) && $this->getObjectTypeId() == \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['KOrgObjects']['filtertype'])
              $addInString = $_SESSION['korgmanagement']['objectfilter'];
             */
            $orgWhere = $this->getKAuthObjectHashWhere($tableName, '', $addInString); // $tableName . ".korgobjecthash <> ''";
        } elseif ($authObject->objDetail['kauthorgassignment'] == 3) {
            $orgWhere = $tableName . '.korgobjecthash is null';
        }

        return $orgWhere;
    }


    private function buildCompleteInString($tableName, $inString, $addInString)
    {
        // built a complete inString
        $thisInString = '';
        if ($inString != '')
            $thisInString = $tableName . ".korgobjecthash IN ($inString)";

        if ($addInString != '') {
            if ($thisInString != '')
                $thisInString = $thisInString . ' AND ' . $tableName . ".korgobjecthash IN ($addInString)";
            else
                $thisInString = $tableName . ".korgobjecthash IN ($addInString)";
        }

        if ($thisInString == '')
            $thisInString = $tableName . ".korgobjecthash <> ''";


        return $thisInString;
    }


    private function getKAuthObjectHashWhere($tableName, $inString, $addInString = '')
    {


        // see what we have to do
        if ($this->relationShip == '') {
            return $this->buildCompleteInString($tableName, $inString, $addInString);
        } else {
            // handle the relationship
            if (empty($this->relationShip['join_table'])) {
                $WHEREString = ' EXISTS (SELECT * FROM ' .
                    ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) .
                    ' WHERE ' .
                    ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . '.' . ($this->beanRelRight ? $this->relationShip['lhs_key'] : $this->relationShip['rhs_key']) . '=' . $tableName . '.' . ($this->beanRelRight ? $this->relationShip['rhs_key'] : $this->relationShip['lhs_key']);


                return $WHEREString . ' AND ' . $this->buildCompleteInString(($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']), $inString, $addInString) . ')';
                /*
                  if ($inString == '')
                  return $WHEREString . ' AND ' . ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . ".korgobjecthash <> '')";
                  else
                  return $WHEREString . ' AND ' . ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . ".korgobjecthash IN ($inString))";
                 */
            } else {
                $WHEREString = ' EXISTS (SELECT * FROM ' .
                    ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) .
                    ' LEFT JOIN ' .
                    $this->relationShip['join_table'] .
                    ' ON ' .
                    $this->relationShip['join_table'] . '.' . ($this->beanRelRight ? $this->relationShip['join_key_lhs'] : $this->relationShip['join_key_rhs']) . ' = ' . ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . '.' . ($this->beanRelRight ? $this->relationShip['lhs_key'] : $this->relationShip['rhs_key']) .
                    ' WHERE ' .
                    $this->relationShip['join_table'] . '.' . ($this->beanRelRight ? $this->relationShip['join_key_rhs'] : $this->relationShip['join_key_lhs']) . '=' . $tableName . '.' . ($this->beanRelRight ? $this->relationShip['rhs_key'] : $this->relationShip['rhs_key']);

                return $WHEREString . ' AND ' . $this->buildCompleteInString(($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']), $inString, $addInString) . ')';
                /*
                  if ($inString == '')
                  return $WHEREString . ' AND ' . ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . ".korgobjecthash <> '')";
                  else
                  return $WHEREString . ' AND ' . ($this->beanRelRight ? $this->relationShip['lhs_table'] : $this->relationShip['rhs_table']) . ".korgobjecthash IN ($inString))";
                 */
            }
        }
    }

    /*
     * get the owne Where Clause
     */

    public function getOwnerWhereClause($tableName, $authObject)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $ownerWhere = '';

        // handle user assignments
        if ($authObject->objDetail['kauthowner']) {
            $ownerWhere .= " " . $tableName . ".assigned_user_id ='$current_user->id' ";

            /* not used at VIP
              $ownerWhere .= " OR $tableName.korguserhash IN (SELECT DISTINCT hash_id FROM korgusers_hash WHERE user_id='" . $current_user->id . "') ";
             */
        }
        return $ownerWhere;
    }


    /*
     * separate function to just build the org Where Clause ... required for the relate field
     * $subSELECT controls wethre the org elöeents are determined in separate statement and then are included in an IN clause
     */

    public function getObjectAddInWhereClause($tableName, $authObject)
    {
        $orgWhere = '';

        if ($authObject->objDetail['kauthorgassignment'] != 3 || ($authObject->objDetail['allorgobjects'] == 0 && $authObject->objDetail['kauthorgassignment'] != 1 && $authObject->objDetail['kauthorgassignment'] != 3)) {

            // check for the Org Unit Picker ... if that adds
            $addInString = '';
            if (!empty(SpiceConfig::getInstance()->config['KOrgObjects']['filtertype']) && !empty($_SESSION['korgmanagement']['objectfilter']) && $authObject->getObjectTypeId($authObject) == SpiceConfig::getInstance()->config['KOrgObjects']['filtertype']) {
                $addInString = $_SESSION['korgmanagement']['objectfilter'];

                $orgWhere = $this->getKAuthObjectHashWhere($tableName, '', $addInString);
            }
        }

        return $orgWhere;
    }

    public function getObjectTypeId($authObject)
    {
        $db = DBManagerFactory::getInstance();
        $thisObj = $db->fetchByAssoc($db->query("SELECT ktm.korgobjecttype_id FROM korgobjecttypes_modules ktm INNER JOIN kauthtypes kat ON kat.bean = ktm.module WHERE kat.id='" . $authObject->objDetail['kauthtype_id'] . "'"));
        return $thisObj['korgobjecttype_id'];
    }
}
