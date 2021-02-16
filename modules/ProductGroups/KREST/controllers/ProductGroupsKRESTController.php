<?php

namespace SpiceCRM\modules\ProductGroups\KREST\controllers;

use Psr\Http\Message\RequestInterface;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
 use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\modules\SpiceACL\SpiceACL;


class ProductGroupsKRESTController
{
    /**
   * load report categories for the ui loadtasks
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     * @return mixed
     */

    public function getTreeNodes($req, $res, $args) {
        $list = [];

        // get an instance of the module handler
        $moduleHandler = new ModuleHandler();

        // a seed bean for the list
        $seed = BeanFactory::getBean('ProductGroups');

        // build the wehre clause
        $whereClause = '';
        if($args['nodeid']){
            $whereClause = "productgroups.parent_productgroup_id = '{$args['nodeid']}'";
        } else {
            $whereClause = "(productgroups.parent_productgroup_id = '' OR productgroups.parent_productgroup_id IS NULL)";
        }

        // process the seed list
        $seedList = $seed->get_full_list('name', $whereClause);
        foreach ($seedList as $seeditem){
            $list[] = $moduleHandler->mapBeanToArray('ProductGroups', $seeditem);
        }

        return $res->withJson($list);
    }

    /**
     * links validation values to a product
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ForbiddenException
     * @throws NotFoundException
     */

    public function ProductWriteValidation($req, $res, $args){
        $resthandler = new ModuleHandler();

        $group = BeanFactory::getBean('ProductGroups');
        $group->retrieve($args['id']);

        $attributes = $group->getProductAttributes();
        $attributes_mapped = array();
        foreach ($attributes as $v) {

            $dummy = $resthandler->mapBeanToArray('ProductAttribute', $v);
            if ($validations = $v->get_linked_beans('productattributevaluevalidations', 'ProductAttributeValueValidation')) {
                $dummy['validations'] = array();
                foreach ($validations as $v2) {
                    $dummy['validations'][] = array('value' => $v2->value, 'value_from' => $v2->value_from, 'value_to' => $v2->value_to);
                }
            }
            $attributes_mapped[] = $dummy;

        }

        return $res->withJson($attributes_mapped);

    }

    /**
     * get the related attributes of a product
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ForbiddenException
     * @throws NotFoundException
     */

    public function ProductGetRelatedAttributes($req, $res, $args){
        $db = DBManagerFactory::getInstance();

        $getParams = $req->getQueryParams();

        $resthandler = new ModuleHandler();

        $group = BeanFactory::getBean('ProductGroups');
        $group->retrieve($args['id']);

        $attributes = $group->getRelatedAttributesRecursively($getParams['searchparams'] == 'true' ? true : false);
        if ($getParams['validations'] !== false) {
            foreach ($attributes as &$attribute) {
                $validations = $db->query("SELECT value, value_from, value_to FROM productattributevaluevalidations WHERE productattribute_id = '" . $attribute['id'] . "'");
                while ($validation = $db->fetchByAssoc($validations)) {
                    $attribute['validations'][] = $validation;
                }
            }
        }

        return $res->withJson($attributes);
    }

    /**
     * changes text datatypes to another
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ForbiddenException
     * @throws NotFoundException
     */

    public function ProductParseTextDataType($req, $res, $args){
        $db = DBManagerFactory::getInstance();
        // acl check if user can get the detail
        if (!SpiceACL::getInstance()->checkAccess('ProductGroups', 'view', true))
            throw ( new ForbiddenException('Forbidden to view in module ProductGroups'))->setErrorCode('noModuleView');

        $group = BeanFactory::getBean('ProductGroups', $args['id']);
        if (!isset($group->id)) throw ( new NotFoundException('ProductGroup not found.'))->setLookedFor(['id'=>$args['id'],'module'=>'ProductGroups']);

        $groups = $group->getParentGroupsV2();

        $attributes = [];

        $attributesObj = $db->query("SELECT productattributes.id, productattributes.name, productattributes.prat_datatype , contentcode, contentcode2, contentprefix, trptgcontentcodeassignments.productgroup_id  FROM trptgcontentcodeassignments, productattributes WHERE trptgcontentcodeassignments.productattribute_id = productattributes.id AND productgroup_id IN ('" . implode("','", $groups) . "')");
        while ($attribute = $db->fetchByAssoc($attributesObj)) {
            if ($attribute['prat_datatype'] == 'S' || $attribute['prat_datatype'] == 'D') {
                $attribute['values'] = new stdClass();
                $attributesValues = $db->query("SELECT value, valueshort FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}' AND valueshort IS NOT NULL");
                while ($attributesValue = $db->fetchByAssoc($attributesValues)) {
                    $attribute['values']->{$attributesValue['value']} = $attributesValue['valueshort'];
                }
            }
            $attributes[$attribute['id']] = $attribute;

        }

        return $res->withJson(array(
            'assignedattributes' => $attributes,
            'allattributes' => $group->getRelatedAttributesRecursively(false),
            'template' => SpiceConfig::getInstance()->config['EpimIntegration']['shortTextTemplate'],
            'shorttext' => $group->getSorParam()
        ));
    }

    /**
     * writes a textbody in the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ForbiddenException
     * @throws NotFoundException
     */

    public function ProductWriteTextProductBody($req, $res, $args){

        $db = DBManagerFactory::getInstance();

        // acl check if user can get the detail
        if (!SpiceACL::getInstance()->checkAccess('ProductGroups', 'view', true))
            throw ( new ForbiddenException('Forbidden to view in module ProductGroups.'))->setErrorCode('noModuleView');

        $seed = BeanFactory::getBean('ProductGroups', $args['id']);
        if (!isset($seed->id)) throw ( new NotFoundException('ProductGroup not found.'))->setLookedFor(['id'=>$args['id'],'module'=>'ProductGroups']);

        $postBody = $req->getParsedBody();

        if($postBody){
            $db->query("DELETE FROM trptgcontentcodeassignments WHERE productgroup_id='{$args['id']}'");
            foreach($postBody as $record){
                $db->query("INSERT INTO trptgcontentcodeassignments (id, productgroup_id, productattribute_id, contentcode, contentcode2, contentprefix ) VALUES('".create_guid()."', '{$args['id']}', '{$record['id']}', '{$record['contentcode']}', '{$record['contentcode2']}', '{$record['contentprefix']}')");
            }
        }

        return $res->withJson(array('status' => 'success'));
    }

    /**
     * changes longtext datatypes to another
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ForbiddenException
     * @throws NotFoundException
     */

    public function ProductParseLongTextDataType($req, $res, $args){

        $db = DBManagerFactory::getInstance();

        // acl check if user can get the detail
        if (!SpiceACL::getInstance()->checkAccess('ProductGroups', 'view', true))
            throw ( new ForbiddenException('Forbidden to view in module ProductGroups.'))->setErrorCode('noModuleView');

        $group = BeanFactory::getBean('ProductGroups', $args['id']);
        if (!isset($group->id)) throw ( new NotFoundException('ProductGroup not found.'))->setLookedFor(['id'=>$args['id'],'module'=>'ProductGroups']);

        $groups = $group->getParentGroupsV2();

        $attributes = [];

        $attributesObj = $db->query("SELECT productattributes.id, productattributes.name, productattributes.prat_datatype , contentcode, contentcode2, textpattern, sequence, trptglongtextcodeassignments.productgroup_id  FROM trptglongtextcodeassignments, productattributes WHERE trptglongtextcodeassignments.productattribute_id = productattributes.id AND productgroup_id IN ('" . implode("','", $groups) . "')");
        while ($attribute = $db->fetchByAssoc($attributesObj)) {
            if ($attribute['prat_datatype'] == 'S' || $attribute['prat_datatype'] == 'D') {
                $attribute['values'] = new stdClass();
                $attributesValues = $db->query("SELECT value, valueshort FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}' AND valueshort IS NOT NULL");
                while ($attributesValue = $db->fetchByAssoc($attributesValues)) {
                    $attribute['values']->{$attributesValue['value']} = $attributesValue['valueshort'];
                }
            }
            $attributes[$attribute['id']] = $attribute;

        }

        return $res->withJson(array(
            'assignedattributes' => $attributes,
            'allattributes' => $group->getRelatedAttributesRecursively(false),
            'template' => SpiceConfig::getInstance()->config['EpimIntegration']['shortTextTemplate'],
            'shorttext' => $group->getSorParam()
        ));
    }

    /**
     * inserts a new longtext body in the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws ForbiddenException
     * @throws NotFoundException
     */

    public function ProductWriteLongTextProductBody($req, $res, $args){

        $db = DBManagerFactory::getInstance();

        // acl check if user can get the detail
        if (!SpiceACL::getInstance()->checkAccess('ProductGroups', 'view', true))
            throw ( new ForbiddenException('Forbidden to view in module ProductGroups.'))->setErrorCode('noModuleView');

        $seed = BeanFactory::getBean('ProductGroups', $args['id']);
        if (!isset($seed->id)) throw ( new NotFoundException('ProductGroup not found.'))->setLookedFor(['id'=>$args['id'],'module'=>'ProductGroups']);

        $postBody = $req->getParsedBody();

        if($postBody){
            $db->query("DELETE FROM trptglongtextcodeassignments WHERE productgroup_id='{$args['id']}'");
            foreach($postBody as $record){
                $db->query("INSERT INTO trptglongtextcodeassignments (id, productgroup_id, productattribute_id, contentcode, contentcode2, textpattern, sequence ) VALUES('".create_guid()."', '{$args['id']}', '{$record['id']}', '{$record['contentcode']}', '{$record['contentcode2']}', '{$record['textpattern']}', '{$record['sequence']}')");
            }
        }

        return $res->withJson(array('status' => 'success'));
    }

}


