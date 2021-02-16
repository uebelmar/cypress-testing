<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesDocs\KREST\controllers;

use SpiceCRM\data\BeanFactory;

class SalesDocsKRESTController
{

    /**
     * reject a document
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    function rejectDocument($req, $res, $args)
    {
        $body = json_decode($req->getBody(), true);
        if ($salesDoc = BeanFactory::getBean('SalesDocs', $args['id'])) {
            foreach ($body['items'] as $itemid => $itemdata) {
                $item = BeanFactory::getBean('SalesDocItems', $itemid);
                if ($item) {
                    $item->rejection_reason = $itemdata['rejection_reason'];
                    $item->rejection_text = $itemdata['rejection_text'];
                    $item->save();
                }
            }
        };

        return $res->withJson(['success' => true], 200, JSON_FORCE_OBJECT);
    }


    /**
     * converts a document to a new targettype
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    function convertDocument($req, $res, $args)
    {
        $target = [];
        $salesDoc = BeanFactory::getBean('SalesDocs', $args['id']);
        if($salesDoc){
            $target = $salesDoc->convertToType($args['targettype']);
        }

        return $res->withJson($target);
    }


}
