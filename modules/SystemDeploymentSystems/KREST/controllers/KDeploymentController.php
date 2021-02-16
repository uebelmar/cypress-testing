<?php
namespace SpiceCRM\modules\SystemDeploymentSystems\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;

class KDeploymentController{

    /**
     * get system infos
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     * @return mixed
     */

    public function KDGetSystem($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->get_systems();
        return $res->withJson($return);
    }

    /**
     * get the remote packages
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetRemotePackage($req, $res, $args){
        $getParams = $req->getQueryParams();
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->get_remote_packages($getParams['system']);
        return $res->withJson($return);
    }

    /**
     * fetches remote packages
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDFetchRemotePackage($req, $res, $args){
        $postBody = $req->getParsedBody();
        $postParams = $req->getQueryParams();
        $params = array_merge($postBody,  $postParams);
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->fetch_remote_package($args['id'], $args['system'], $params);
        return $res->withJson($return);
    }

    /** fetches the content of a package
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDFetchPackageContent($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->fetch_package_content($args['id'], $args['system']);
        return $res->withJson($return);
    }

    /**
     * releases a package
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDReleasePackage($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->release_package($packages);
        return $res->withJson($return);
    }

    /**
     * checks access for a user
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDCheckAccess($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->check_access($packages);
        return $res->withJson($return);
    }

    /**+
     * write a backup in the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDWriteBackup($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->backup($packages);
        return $res->withJson($return);
    }

    /**
     * writes in a local file
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDWriteLocalFiles($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->write_files($packages, false);
        return $res->withJson($return);
    }

    /**
     * writes in a remote file
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDWriteRemoteFiles($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->write_files($packages, true);
        return $res->withJson($return);
    }

    /**
     * writes in the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDWriteDb($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->write_db($packages);
        return $res->withJson($return);
    }

    /**
     * mark beans as deployed
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDMarkDeployed($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->mark_deployed($packages);
        return $res->withJson($return);
    }

    /**
     * make a rollback on the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDRollback($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->rollback($packages);
        return $res->withJson($return);
    }

    /**
     * repairs a local database link
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDRepairLocal($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->repair($packages, false);
        return $res->withJson($return);
    }

    /**
     * repairs remote database links
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDRemoteRepair($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $res = json_decode($args['id']);
        if ($res === NULL) {
            $packages = array($args['id']);
        } else {
            $packages = $res;
        }
        $return = $dep->repair($packages, true);
        return $res->withJson($return);
    }

    /**
     * gets the package history
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDReleasePackageHistory($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->release_package_history($args['id']);
        return $res->withJson($return);
    }

    /**
     * updates the remote packages
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDUpdateRemotePackage($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $getParams = $req->getQueryParams();
        $return = $dep->update_remote_package_status($args['package'], $args['status'], $args['system']);
        return $res->withJson($return);
    }

    /**
     * get packages from the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetPackages($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $getParams = $req->getQueryParams();
        $return = $dep->get_release_packages($getParams);
        return $res->withJson($return);
    }

    /**
     * gets systeminfos from normal and linked beans
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetSourceSystem($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->get_source_systems();
        return $res->withJson($return);
    }

    /**
     * deletes the system configuration
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDDeleteSystem($req, $res, $args){
        $dep = new KDeploymentSystem(); //todo-uebelmar which class is this?
        $return = $dep->del_system($args['id']);
        return $res->withJson($return);
    }

    /**
     * inserts a systemlink into systemdeploymentsystems
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDInsertSystemLink($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->add_system_link($args['id'],$args['link']);
        return $res->withJson($return);
    }

    /**
     * tests the connection
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDTestConnection($req,$res,$args){
        $postBody = $body = $req->getParsedBody();
        $postParams = $req->getQueryParams();
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->test_connection(array_merge($postBody, $postParams));
        return $res->withJson($return);
    }

    /**
     * updates the systemdeploymentsystems table
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDImportSystem($req, $res, $args){
        $postBody = $body = $req->getParsedBody();
        $postParams = $req->getQueryParams();
        $params = array_merge($postBody, $postParams);
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->import_systems($params);
        return $res->withJson($return);

    }

    /**
     * gets the distribution list from the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetDistribute($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $return = $dep->distribute($args['id']);
        return $res->withJson($return);

    }

    /**
     * creates a list from the user git repos
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetGitRepo($req, $res, $args){
        $getParams = $req->getQueryParams();
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $repos = $dep->getRepositories($getParams);
        return $res->withJson(array('list' => $repos));

    }

    /**
     * gets the software version from the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetSwVersion($req, $res, $args){
        $getParams = $req->getQueryParams();
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $latestSwPacks = $dep->latestSwVersions(json_decode(html_entity_decode($args['swpacks'])));
        return $res->withJson($latestSwPacks);

    }

    /**
     * get the app config from the database
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetAppConfig($req, $res, $args){
        $getParams = $req->getQueryParams();
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $conf = $dep->getAppConfig();
        return $res->withJson($conf);

    }

    /**
     * receives a Zip file from a from
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetZipFromForm($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $postParams = $req->getQueryParams();
        $res = $dep->recieveZipFromForm($postParams);
        return $res->withJson($res);

    }

    /**
     * gets a zip file from Rest
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function KDGetZipFromRest($req, $res, $args){
        $dep = BeanFactory::getBean('SystemDeploymentSystems');
        $postBody = $req->getParsedBody();
        $postParams = $req->getQueryParams();
        $params = array_merge($postBody, $postParams);
        $res = $dep->recieveZipFromRest($params);
        return $res->withJson($res);

    }
}