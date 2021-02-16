<?php
namespace SpiceCRM\modules\PriceConditions\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\modules\SpiceACL\SpiceACL;

class PriceConditionsKRESTController
{

    public static function getConfiguration($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();

        $retArray = [
            'conditiontypes' => [],
            'conditionelements' => [],
            'determinations' => [],
            'determinationelements' => [],
            'conditiondeterminations' => [],
        ];

        $conditiontypes = $db->query("SELECT * FROM syspriceconditiontypes");
        while($conditiontype = $db->fetchByAssoc($conditiontypes)){
            $retArray['conditiontypes'][] = $conditiontype;
        }

        $conditionelements = $db->query("SELECT * FROM syspriceconditionelements");
        while($conditionelement = $db->fetchByAssoc($conditionelements)){
            $retArray['conditionelements'][] = $conditionelement;
        }

        $determinations = $db->query("SELECT * FROM syspricedeterminations");
        while($determination = $db->fetchByAssoc($determinations)){
            $retArray['determinations'][] = $determination;
        }

        $determinationelements = $db->query("SELECT * FROM syspricedeterminationelements");
        while($determinationelement = $db->fetchByAssoc($determinationelements)){
            $retArray['determinationelements'][] = $determinationelement;
        }

        $conditiondeterminations = $db->query("SELECT * FROM syspriceconditiontypes_determinations");
        while($conditiondetermination = $db->fetchByAssoc($conditiondeterminations)){
            $retArray['conditiondeterminations'][] = $conditiondetermination;
        }

        return $res->withJson($retArray);
    }
    public static function getCustomerConditions($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();

        // check that we can view the account
        $seed = BeanFactory::getBean($args['module'], $args['id']);
        if (!$seed) {
            throw (new ForbiddenException('no access to account'));
        }

        if (!SpiceACL::getInstance()->checkAccess('PriceConditions', 'list', true) && !SpiceACL::getInstance()->checkAccess('PriceConditions', 'listrelated', true)) {
            throw (new ForbiddenException("no access to module PriceConditions"))->setErrorCode('noModuleList');
        }

        $retArray = [];

        // determine the account number element
        $elementQueryArray = [];
        $elements = $db->query("SELECT * FROM syspriceconditionelements WHERE element_module='{$args['module']}'");
        while($element = $db->fetchByAssoc($elements)){
            if(!empty($seed->{$element['element_module_field']})){
                $elementQueryArray[] = "(element_id = '{$element['id']}' AND element_value = '{$seed->{$element['element_module_field']}}')";
            }
        }

        // if we found an entry go for it
        if(count($elementQueryArray) > 0) {
            $pcevQuery = "SELECT pricecondition_id FROM priceconditionelementvalues WHERE deleted = 0 AND (" . implode(' OR ', $elementQueryArray) . ")";
            $priceCondition = BeanFactory::getBean('PriceConditions');
            $conditions = $priceCondition->get_full_list('', "priceconditions.id IN ($pcevQuery)");
            $KRESTModuleHandler = new ModuleHandler();
            foreach ($conditions as $condition) {
                $retArray[] = $KRESTModuleHandler->mapBeanToArray('PriceConditions', $condition);
            }
        }

        return $res->withJson($retArray);
    }
}
