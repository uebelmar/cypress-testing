<?php
namespace SpiceCRM\modules\SystemDeploymentCRs\KREST\controllers;

use Psr\Http\Message\RequestInterface;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;


class SystemDeploymentCrsController
{

    /**
     * Get Files System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getFiles($req, $res, $args)
    {
        $getParams = $req->getQueryParams();
        $cr = BeanFactory::getBean('SystemDeploymentCRs');
        $files = $cr->getFiles($getParams);
        return $res->withJson($files);
    }

    /**
     * Get Detail Files System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getDetailFiles($req, $res, $args)
    {
        $getParams = $req->getQueryParams();
        $cr = BeanFactory::getBean('SystemDeploymentCRs');
        $files = $cr->getDetailFiles($getParams);
        return $res->withJson($files);
    }

    /**
     * Get Commits System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getCommits($req, $res, $args)
    {
        $getParams = $req->getQueryParams();
        $cr = BeanFactory::getBean('SystemDeploymentCRs');
        $commits = $cr->getCommits($getParams);
        return $res->withJson($commits);
    }

    /**
     * Get Branches System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getBranches($req, $res, $args)
    {
        $getParams = $req->getQueryParams();
        $cr = BeanFactory::getBean('SystemDeploymentCRs');
        $branches = $cr->getBranches($getParams);
        return $res->withJson(array('list' => $branches));
    }

    /**
     * Get Tables System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getTables($req, $res, $args)
    {
        $getParams = $req->getQueryParams();
        $cr = BeanFactory::getBean('SystemDeploymentCRs');
        $branches = $cr->getTables($getParams);
        return $res->withJson(array('list' => $branches));
    }

    /**
     * Get Detail DB Entries System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getDetailDBEntries($req, $res, $args)
    {
        if($cr = BeanFactory::getBean('SystemDeploymentCRs', $args['id']))
            $files = $cr->getDetailDBEntries();
        return $res->withJson($files);
    }

    /**
     * Active Entries System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function active($req, $res, $args)
    {
        if ($_SESSION['SystemDeploymentCRsActiveCR']) {
            $cr = BeanFactory::getBean('SystemDeploymentCRs', $_SESSION['SystemDeploymentCRsActiveCR']);
        }
        return $res->withJson([
            'id' => $_SESSION['SystemDeploymentCRsActiveCR'] ?: '',
            'name' => $cr->name ?: ''
        ]);
    }

    /**
     * Set Active Entries System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function setActive($req, $res, $args)
    {
        $_SESSION['SystemDeploymentCRsActiveCR'] = $args['id'];
        return $res->withJson(['status' => 'success']);
    }

    /**
     * Delete Active Entries System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function deleteActive($req, $res, $args)
    {
        unset($_SESSION['SystemDeploymentCRsActiveCR']);
        return $res->withJson(['status' => 'success']);
    }

    /**
     * Get SQL Entries System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getSql($req, $res, $args){
        $cr = BeanFactory::getBean('SystemDeploymentCRs', $args['id']);
        $sql = $cr->getDBEntriesSQL();
        return $res->withJson(['sql' => $sql]);
    }

    /**
     * Get DB Entries Entries System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getDBEntries($req, $res, $args)
    {
        $getParams = $req->getQueryParams();
        $cr = BeanFactory::getBean('SystemDeploymentCRs');
        $files = $cr->getDBEntries($getParams);
        return $res->withJson($files);
    }

    /**
     * Get App Config Entries Entries System Deployment CRS.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function appConfig($req, $res, $args)
    {
        $getParams = $req->getQueryParams();
        $cr = BeanFactory::getBean('SystemDeploymentCRs');
        $conf = $cr->getAppConfig();
        return $res->withJson($conf);
    }
}