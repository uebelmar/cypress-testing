<?php
namespace SpiceCRM\modules\Products\KREST\controllers;

use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\data\BeanFactory;
use Slim\Routing\RouteCollectorProxy;

class ProductKRESTController{

    /**
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     * @return mixed
     * @throws \Exception
     */

    public function ProductMapValidation($req, $res, $args){
        $resthandler = new ModuleHandler();

        $product = BeanFactory::getBean('Products', $args['id']);
        $group = BeanFactory::getBean('ProductGroups', $product->productgroup_id);

        $attributes = $group->getProductAttributes();
        $attributes_mapped = array();
        foreach ($attributes as $attribute) {

            $attribute_mapped = $resthandler->mapBeanToArray('ProductAttribute', $attribute);
            if ($validations = $attribute->get_linked_beans('productattributevaluevalidations', 'ProductAttributeValueValidation')) {
                $attribute_mapped['validations'] = array();
                foreach ($validations as $validation) {
                    $attribute_mapped['validations'][] = array('value' => $validation->value, 'value_from' => $validation->value_from, 'value_to' => $validation->value_to);
                }
            }
            $attributes_mapped[] = $attribute_mapped;
        }

        return $res->withJson($attributes_mapped);
    }

    /**
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws \Exception
     */

    public function ProductGetValue($req, $res, $args){
        $db = DBManagerFactory::getInstance();

        $getParams = $req->getQueryParams();

        $resthandler = new ModuleHandler();

        $product = BeanFactory::getBean('Products', $args['id']);
        $group = BeanFactory::getBean('ProductGroups', $product->productgroup_id);

        $attributes = $group->getRelatedAttributesRecursively($getParams['searchparams'] == 'true' ? true : false);
        if ($getParams['validations'] !== false) {
            $objectAttributeValues = [];
            $attributeValues = $product->get_linked_beans('productattributevalues', 'ProductAttributeValue');
            foreach ($attributeValues as $attributeValue)
                $objectAttributeValues[$attributeValue->productattribute_id] = $attributeValue->pratvalue;
            foreach ($attributes as &$attribute) {
                $validations = $db->query("SELECT value, value_from, value_to FROM productattributevaluevalidations WHERE productattribute_id = '" . $attribute['id'] . "'");
                while ($validation = $db->fetchByAssoc($validations)) {
                    $attribute['validations'][] = $validation;
                }
                $attribute['value'] = $objectAttributeValues[$attribute['id']];
            }
        }

        return $res->withJson($attributes);
    }

    /**
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws \Exception
     */

    public function ProductCleanValue($req, $res, $args){
        $db = DBManagerFactory::getInstance();

        $product = BeanFactory::getBean('Products', $args['id']);
        $group = BeanFactory::getBean('ProductGroups', $product->productgroup_id);
        $groups = $product->getProductGroups();

        $attributes = [];
        $productnames = [];

        $attributesObj = $db->query("SELECT productattributes.id, productattributes.name, productattributes.prat_datatype , contentcode, contentcode2, contentprefix  FROM trptgcontentcodeassignments, productattributes WHERE trptgcontentcodeassignments.productattribute_id = productattributes.id AND productgroup_id IN ('" . implode("','", $groups) . "')");
        while ($attribute = $db->fetchByAssoc($attributesObj)) {
            if ($attribute['prat_datatype'] == 'S' || $attribute['prat_datatype'] == 'D') {
                $attribute['values'] = new stdClass();
                $attributesValues = $db->query("SELECT value, valueshort FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}' AND valueshort IS NOT NULL");
                while ($attributesValue = $db->fetchByAssoc($attributesValues)) {
                    $attribute['values']->{$attributesValue['value']} = $attributesValue['valueshort'];
                }
            }

            // get the values for content code N
            if($attribute['contentcode'] == 'N'){
                $attributesValues = $db->query("SELECT value FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}'");
                //$attributesValues = $db->query("SELECT value, valueshort FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}'");
                while ($attributesValue = $db->fetchByAssoc($attributesValues)) {
                    $productnames[] = $attributesValue['value'];
                }
            }

            $attributes[$attribute['id']] = $attribute;

        }

        $ltattributes = [];
        $attributesObj = $db->query("SELECT productattributes.id, productattributes.name, productattributes.prat_datatype , contentcode, contentcode2, textpattern, sequence  FROM trptglongtextcodeassignments, productattributes WHERE trptglongtextcodeassignments.productattribute_id = productattributes.id AND productgroup_id IN ('" . implode("','", $groups) . "')");
        while ($attribute = $db->fetchByAssoc($attributesObj)) {
            if ($attribute['prat_datatype'] == 'S' || $attribute['prat_datatype'] == 'D') {
                $attribute['values'] = new stdClass();
                $attributesValues = $db->query("SELECT value, valueshort FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}' AND valueshort IS NOT NULL");
                while ($attributesValue = $db->fetchByAssoc($attributesValues)) {
                    $attribute['values']->{$attributesValue['value']} = $attributesValue['valueshort'];
                }
            }

            // get the values for content code N
            if($attribute['contentcode'] == 'N'){
                $attributesValues = $db->query("SELECT value FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}'");
                //$attributesValues = $db->query("SELECT value, valueshort FROM productattributevaluevalidations WHERE productattribute_id = '{$attribute['id']}'");
                while ($attributesValue = $db->fetchByAssoc($attributesValues)) {
                    $productnames[] = $attributesValue['value'];
                }
            }

            $ltattributes[] = $attribute;

        }

        usort($ltattributes, function($a, $b){return $a['sequence'] > $b['sequence'] ? 1 : -1;});

        return $res->withJson(array(
            'attributes' => $attributes,
            'ltattributes' => $ltattributes,
            'template' => SpiceConfig::getInstance()->config['EpimIntegration']['shortTextTemplate'],
            'shorttext' => $group->getSorParam(),
            'productnames' => $productnames
        ));
    }

}