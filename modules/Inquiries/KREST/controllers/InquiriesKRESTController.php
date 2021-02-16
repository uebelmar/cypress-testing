<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\Inquiries\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class InquiriesKRESTController
{

    /**
     * @param $req
     * @param $res
     * @param $args
     */
    public function getCatalogs($req, $res, $args){
        

        if(!SpiceConfig::getInstance()->config['catalogorders']['productgroup_id']) return [];

        $group = BeanFactory::getBean('ProductGroups', SpiceConfig::getInstance()->config['catalogorders']['productgroup_id']);

        $products = [];
        $group->load_relationship('products');
        $relatedProducts = $group->get_linked_beans('products', 'Products', [], 0, 100, 0, "product_status = 'active'");
        foreach ($relatedProducts as $relatedProduct) {
            $products[] = [
                'id' => $relatedProduct->id,
                'name' => html_entity_decode($relatedProduct->name, ENT_QUOTES),
                'external_id' => $relatedProduct->ext_id
            ];
        }
        return $res->withJson($products);
    }

    /**
     * crate from avada Form in wordpress
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function createFromAvada($req, $res, $args){

        // get the body and parse the params
        $req->getBody()->rewind();
        $params = $req->getBody()->getContents();
        $queryArray = [];
        parse_str($params,$queryArray);

        $seed = BeanFactory::getBean($args['module']);
        foreach($seed->field_defs as $fieldname => $fielddata){
            if(isset($queryArray[$fieldname])){
                $seed->{$fieldname} = $queryArray[$fieldname];
            }
        }
        $seed->save();

        return $res->withJson(['status' => 'OK']);

    }
}
