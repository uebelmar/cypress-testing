<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\includes\Five9VOIP\KREST\controllers;

use SpiceCRM\includes\Five9VOIP\Five9VOIP;
use SpiceCRM\includes\Five9VOIP\Five9VOIPConnector;

class Five9VOIPKRESTController{

    public function login($req, $res, $args)
    {
        $five9 = new Five9VOIP();
        return $res->withJson(['login' => $five9->login(), 'tokenId' => $five9->connector->tokenId, 'userId' => $five9->connector->userId]);
    }

    public function keepalive($req, $res, $args)
    {
        $five9 = new Five9VOIPConnector();
        return $res->withJson(['status' => $five9->keepAlive() ? 'success' : 'error']);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getPreferences($req, $res, $args)
    {
        $five9 = new Five9VOIP();
        return $res->withJson($five9->getPreferences());
    }

    /**
     * sets the preferences including checking the login
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function setPreferences($req, $res, $args)
    {
        $postBody = $req->getParsedBody();

        $five9 = new Five9VOIP();
        $success = $five9->setPreferences($postBody);
        return $res->withJson(['status' =>  $success ? 'success' : 'error']);
    }

    /**
     * initiate a call
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function initiateCall($req, $res, $args)
    {
        $postBody = $req->getParsedBody();

        $five9 = new Five9VOIP();
        $response = $five9->initiateCall($postBody['msisdn']);
        return $res->withJson($response);
    }

    /**
     * terminate a call with the given id
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function hangupCall($req, $res, $args)
    {
        $five9 = new Five9VOIP();
        $response = $five9->hangupcall($args['callid']);
        return $res->withJson($response);
    }

    /**
     * handles the event
     *
     * @param $req
     * @param $res
     * @param $args
     */
    public function handleEvent($req, $res, $args){
        $postBody = $req->getParsedBody();
        $five9User = $req->getParams()['de_vertico_starface_user'];
        $five9 = new Five9VOIP();
        $five9->handleEvent($five9User, $postBody);
        return $res->withJson(['status' => 'OK']);
    }

    /**
     * handles the event
     *
     * @param $req
     * @param $res
     * @param $args
     */
    public function subscribeEvents($req, $res, $args){
        $five9 = new Five9VOIPConnector();
        return $res->withJson(['status' => $five9->subscribeEvents() ? 'success' : 'error']);
    }

    /**
     * handles the event
     *
     * @param $req
     * @param $res
     * @param $args
     */
    public function unsubscribeEvents($req, $res, $args){
        $five9 = new Five9VOIPConnector();
        return $res->withJson(['status' => $five9->unsubscribeEvents() ? 'success' : 'error']);
    }
}
