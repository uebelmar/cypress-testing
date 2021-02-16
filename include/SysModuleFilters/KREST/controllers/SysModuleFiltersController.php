<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SysModuleFilters\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\authentication\AuthenticationController;

class SysModuleFiltersController
{

    function __construct()
    {

    }

    private function checkAdmin()
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (!$current_user->is_admin) {
            throw (new ForbiddenException('No administration privileges.'))->setErrorCode('notAdmin');

        }
    }

    function getFilters($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();

        $this->checkAdmin();

        $filters = [];
        $filtersObj = "SELECT 'global' As `scope`, fltrs.* FROM sysmodulefilters fltrs  WHERE fltrs.module = '{$args['module']}' UNION ";
        $filtersObj .= "SELECT 'custom' As `scope`, cfltrs.* FROM syscustommodulefilters cfltrs  WHERE cfltrs.module = '{$args['module']}'";
        $filtersObj = $db->query($filtersObj);
        while ($filter = $db->fetchByAssoc($filtersObj))
            $filters[] = $filter;

        return $res->withJson($filters);
    }

    function saveFilter($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();
        $this->checkAdmin();
        $filterdata = $req->getParsedBody();
        // check if filter exists
        if(isset($filterdata['scope'])) {
            $table = $filterdata['scope'] == 'custom' ? 'syscustommodulefilters' : 'sysmodulefilters';
        }elseif($filterdata['type']){
            $table = $filterdata['type'] == 'custom' ? 'syscustommodulefilters' : 'sysmodulefilters';
        }
        $filter = $db->fetchByAssoc($db->query("SELECT * FROM $table WHERE id='{$args['filter']}'"));
        if ($filter) {
            $filterdefs = json_encode($filterdata['filterdefs']);
            $db->query("UPDATE $table SET name='{$filterdata['name']}', filterdefs='$filterdefs', filtermethod='".$db->quote($filterdata['filtermethod'])."', version='{$filterdata['version']}', package='{$filterdata['package']}' WHERE id = '{$args['filter']}'");

            $this->setCR('I', $args['filter'], "{$args['module']}/{$filterdata['name']}");
        } else {
            $filterdefs = json_encode($filterdata['filterdefs']);
            $db->query("INSERT INTO $table (id, created_by_id, module, name, filterdefs, filtermethod, version, package) VALUES('{$args['filter']}', '$current_user->id', '{$args['module']}', '{$filterdata['name']}', '$filterdefs', '".$db->quote($filterdata['filtermethod'])."', '{$filterdata['version']}', '{$filterdata['package']}')");

            $this->setCR('I', $args['filter'], "{$args['module']}/{$filterdata['name']}");
        }
    }

    function deleteFilter($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();
        $this->checkAdmin();
        $id = $db->quote($args['filter']);
        $resultGlobal = $db->query("DELETE FROM sysmodulefilters WHERE id = '$id'");
        $resultCustom = $db->query("DELETE FROM syscustommodulefilters WHERE id = '$id'");
        $this->setCR('D', $id);

        return $res->withJson($resultCustom && $resultGlobal);
    }

    /**
     * adds the filter to the CR if a CR is active
     *
     * @param $action
     * @param $id
     * @param $name
     */
    private function setCR($action, $id, $name = '')
    {
        // check if we have a CR set
        if ($_SESSION['SystemDeploymentCRsActiveCR'])
            $cr = BeanFactory::getBean('SystemDeploymentCRs', $_SESSION['SystemDeploymentCRsActiveCR']);

        if ($cr) {
            $cr->addDBEntry('sysmodulefilters', $id, $action, $name);
        }
    }
}

