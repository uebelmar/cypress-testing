<?php
namespace SpiceCRM\modules\ProductVariants\KREST\controllers;

use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\includes\SpiceFTSManager\SpiceFTSHandler;
use Slim\Routing\RouteCollectorProxy;

class ProductVariantsController{

    /**
     * changes the set of value by datatype
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function ProductCleanUpDataType($req, $res, $args){
        $db = DBManagerFactory::getInstance();

        $retArray = array(
            'variants' => array(),
            'aggregates' => array()
        );

        $moduleHandler = new ModuleHandler();

        $filterArray = array(
            array(
                'term' => array(
                    'productgroups' => $args['id']
                )
            )
        );

        $searchParams = $req->getQueryParams();

        $start = $searchParams['start'] ?: 0;
        $size = $searchParams['size'] ?: 25;

        $searchfilters = json_decode(html_entity_decode($searchParams['searchfilters']), true);
        if(is_array($searchfilters) && count($searchfilters) > 0){
            foreach($searchfilters as $searchfilterid => $searchfiltervalues){
                switch(strtolower($searchfiltervalues['datatype'])) {
                    case 'vc':
                        if (!empty($searchfiltervalues['value'])) {
                            $filterArray[] = array(
                                'wildcard' => array(
                                    'attrib->' . $searchfilterid => '*'.$searchfiltervalues['value'].'*'
                                )
                            );
                        }
                        break;
                    case 's':
                        if (!empty($searchfiltervalues['value'])) {

                            $filtervalues = explode(',', $searchfiltervalues['value']);
                            $termsArray = [];
                            foreach ($filtervalues as $filtervalue){
                                $termsArray[] = array(
                                    'match' => ['attrib->' . $searchfilterid => [
                                        'query' => $filtervalue,
                                        'minimum_should_match' => '100%'
                                    ]]
                                );
                            }

                            $filterArray[] = [
                                'bool' => [
                                    'should' => $termsArray,
                                    "minimum_should_match" => 1
                                ]
                            ];
                        }
                        break;
                    case 'n':
                        if (!empty($searchfiltervalues['valuefrom']) || !empty($searchfiltervalues['valueto'])) {

                            $attrib = $db->fetchByAssoc($db->query("SELECT prat_length, prat_precision FROM productattributes WHERE id='$searchfilterid'"));
                            $precision = $attrib['prat_precision'] || $attrib['prat_precision'] === '0' ? $attrib['prat_precision'] : 2;
                            $length = $attrib['prat_length'] ?: $attrib['prat_precision'] + 5;

                            $rangeArray= [];
                            if(!empty($searchfiltervalues['valuefrom'])) {
                                $attribValue = (string) round($searchfiltervalues['valuefrom'] * pow(10, $precision ));
                                while(strlen($attribValue) < $length)
                                    $attribValue = '0' . $attribValue;
                                $rangeArray['gte'] = $attribValue;
                            }
                            if(!empty($searchfiltervalues['valueto'])) {
                                $attribValue = (string) round($searchfiltervalues['valueto'] * pow(10, $precision ));
                                while(strlen($attribValue) < $length)
                                    $attribValue = '0' . $attribValue;
                                $rangeArray['lte'] = $attribValue;
                            }

                            $filterArray[] = array(
                                'range' => array(
                                    'attrib->' . $searchfilterid => $rangeArray
                                )
                            );
                        }
                        break;
                    case 'f':
                        if (!empty($searchfiltervalues['value'])) {
                            $filterArray[] = array(
                                'match' => array(
                                    'attrib->' . $searchfilterid => $searchfiltervalues['value'] ? '1' : '0'
                                )
                            );
                        }
                        break;
                    default:
                        if (!empty($searchfiltervalues['value'])) {
                            $filterArray[] = array(
                                'match' => array(
                                    'attrib->' . $searchfilterid => $searchfiltervalues['value']
                                )
                            );
                        }
                        break;
                }
            }
        }

        $variants = SpiceFTSHandler::getInstance()->searchModule('ProductVariants', $searchParams['searchterm'], array(), array(), $size, $start, array(), $filterArray);

        foreach($variants['hits']['hits']as $variant){
            // $pVariant = \SpiceCRM\data\BeanFactory::getBean('ProductVariants', $variant['_id']);
            // $retArray['variants'][] = $moduleHandler->mapBeanToArray('ProductVariants', $pVariant);
            $dataArray = array();
            foreach($variant['_source'] as $fieldname => $fieldvalue){
                if(strpos($fieldname, 'attrib->') === 0){
                    $attrib = explode('->', $fieldname);
                    $dataArray['productattributevalues']['beans'][$attrib[1]] = array(
                        'id' => $attrib[1],
                        'productattribute_id' => $attrib[1],
                        'pratvalue' => $fieldvalue
                    );

                    // extract the matnr
                    if($attrib[1] == 'ca141ab8-55ad-8e88-c7f3-58639a9550c9'){
                        $dataArray['sap_matnr'] = $fieldvalue;
                        $dataArray['sap_inventory'] = rand(0,1) == 1;
                    }

                } else {
                    $dataArray[$fieldname] = $fieldvalue;
                }
            }
            $retArray['variants'][] = $dataArray;
        }

        foreach($variants['aggregations'] as $aggregate => $aggergatedata){
            foreach($aggergatedata['buckets'] as $bucket){
                $retArray['aggregates'][$aggregate][$bucket['key']] = $bucket['doc_count'];
            }
        }

        $retArray['total'] = $variants['hits']['total'];

        return $res->withJson($retArray);

    }

    /**
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function ProductSearchParam($req, $res, $args){
        $db = DBManagerFactory::getInstance();

        $retArray = array(
            'variants' => array(),
            'aggregates' => array()
        );

        $moduleHandler = new ModuleHandler();

        $filterArray = array(
            array(
                'term' => array(
                    'productid' => $args['id']
                )
            )
        );

        $searchParams = $req->getQueryParams();

        $start = $searchParams['start'] ?: 0;
        $size = $searchParams['size'] ?: 25;

        $searchfilters = json_decode($searchParams['searchfilters'], true);
        if(is_array($searchfilters) && count($searchfilters) > 0){
            foreach($searchfilters as $searchfilterid => $searchfiltervalues){
                switch($searchfiltervalues['datatype']) {
                    case 'vc':
                        if (!empty($searchfiltervalues['value'])) {
                            $filterArray[] = array(
                                'wildcard' => array(
                                    'attrib->' . $searchfilterid => '*'.$searchfiltervalues['value'].'*'
                                )
                            );
                        }
                        break;
                    default:
                        if (!empty($searchfiltervalues['value'])) {
                            $filterArray[] = array(
                                'match' => array(
                                    'attrib->' . $searchfilterid => $searchfiltervalues['value']
                                )
                            );
                        }
                        break;
                }
            }
        }

        $variants = SpiceFTSHandler::getInstance()->searchModule('ProductVariants', $searchParams['searchterm'], array(), array(), $size, $start, array(), $filterArray);

        /*
        foreach($variants['hits']['hits']as $variant){
            $pVariant = \SpiceCRM\data\BeanFactory::getBean('ProductVariants', $variant['_id']);
            $retArray['variants'][] = $moduleHandler->mapBeanToArray('ProductVariants', $pVariant);
        }
        */
        foreach($variants['hits']['hits']as $variant){
            // $pVariant = \SpiceCRM\data\BeanFactory::getBean('ProductVariants', $variant['_id']);
            // $retArray['variants'][] = $moduleHandler->mapBeanToArray('ProductVariants', $pVariant);
            $dataArray = array();
            foreach($variant['_source'] as $fieldname => $fieldvalue){
                if(strpos($fieldname, 'attrib->') === 0){
                    $attrib = explode('->', $fieldname);
                    $dataArray['productattributevalues']['beans'][$attrib[1]] = array(
                        'id' => $attrib[1],
                        'productattribute_id' => $attrib[1],
                        'pratvalue' => $fieldvalue
                    );

                    // extract the matnr
                    if($attrib[1] == 'ca141ab8-55ad-8e88-c7f3-58639a9550c9'){
                        $dataArray['sap_matnr'] = $fieldvalue;
                        $dataArray['sap_inventory'] = rand(0,1) == 1;
                    }

                } else {
                    $dataArray[$fieldname] = $fieldvalue;
                }
            }
            $retArray['variants'][] = $dataArray;
        }

        foreach($variants['aggregations'] as $aggregate => $aggergatedata){
            foreach($aggergatedata['buckets'] as $bucket){
                $retArray['aggregates'][$aggregate][$bucket['key']] = $bucket['doc_count'];
            }
        }

        $retArray['total'] = $variants['hits']['total'];

        return $res->withJson($retArray);

    }

}
