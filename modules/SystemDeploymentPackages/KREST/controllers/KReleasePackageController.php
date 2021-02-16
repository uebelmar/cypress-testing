<?php
namespace SpiceCRM\modules\SystemDeploymentPackages\KREST\controllers;

use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;
use SpiceCRM\data\BeanFactory;
use Slim\Routing\RouteCollectorProxy;

class KReleasePackageController{

    /**
     * get SystemDeploymentPackages list
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GetDeploymentPackageList($req,$res,$args){
        $rp = BeanFactory::getBean('SystemDeploymentPackages');
        $getParams = $req->getQueryParams();
        $list = $rp->getList($getParams);
        return $res->withJson($list);

    }

    /**
     * saves SystemDeploymentPackages
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function SaveRPPackages($req,$res,$args){
        $rp = BeanFactory::getBean('SystemDeploymentPackages');
        $postBody = $req->getParsedBody();
        $postParams = $req->getQueryParams();
        $params = array_merge($postBody, $postParams);
        $res = $rp->saveRP($params);
        return $res->withJson($res);
    }

    /**
     * mark packages as deleted
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

        public function MarkPackagesDeleted($req,$res,$args){
        $rp = BeanFactory::getBean('SystemDeploymentPackages');
        $rp->retrieve($args['id']);
        $list = $rp->mark_deleted($args['id']);
        return $res->withJson(array('status' => 'OK'));
    }

    /**
     * maps the status with a package
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GetStatus($req,$res,$args){
        $list = array();
        $app_list_strings['rpstatus_dom'] = array(
            '0' => 'created',
            '1' => 'in progress',
            '2' => 'completed',
        );
        foreach ($app_list_strings['rpstatus_dom'] as $id => $name) {
            $list[] = array(
                'id' => $id,
                'name' => $name
            );
        }
        return $res->withJson(array('list' => $list));
    }

    /**
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GetType($req,$res,$args){
        $app_list_strings = return_app_list_strings_language("en_us");
        $list = array();
        foreach ($app_list_strings['rptype_dom'] as $id => $name) {
            if($id === '4') continue; // type imported only over upload in deployment manager
            $list[] = array(
                'id' => $id,
                'name' => $name
            );
        }
        return $res->withJson(array('list' => $list));
    }

    /**
     * get the SystemDeploymentPackages CRs
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

        public function KGetCRs($req,$res,$args){
        $getParams = $req->getQueryParams();
        $rp = BeanFactory::getBean('SystemDeploymentPackages');
        $files = $rp->getCRs($getParams);
        return $res->withJson($files);
    }

    /**
     * gets the cr list
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

        public function KGetCRList($req,$res,$args){
        $getParams = $req->getQueryParams();
        $rp = BeanFactory::getBean('SystemDeploymentPackages');
        $files = $rp->getCRList($getParams);
        return $res->withJson($files);
    }

    /**
     * packages the SystemDeploymentPackages
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KPackage($req,$res,$args){
        $getParams = $req->getQueryParams();
        $rp = BeanFactory::getBean('SystemDeploymentPackages');
        $files = $rp->package($getParams);
        return $res->withJson($files);
    }

    /**
     * release the SystemDeploymentPackages
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KReleasePackage($req,$res,$args){
        $getParams = $req->getQueryParams();
        $rp = BeanFactory::getBean('SystemDeploymentPackages');
        $files = $rp->release_package($args['id']);
        return $res->withJson(array('status' => 'RELEASED '.$args['id']));
    }

}