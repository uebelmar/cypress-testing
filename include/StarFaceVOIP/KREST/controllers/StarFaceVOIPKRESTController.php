<?php

namespace SpiceCRM\includes\StarFaceVOIP\KREST\controllers;

use SpiceCRM\includes\StarFaceVOIP\StarFaceVOIP;
use SpiceCRM\includes\StarFaceVOIP\StarFaceVOIPConnector;

class StarFaceVOIPKRESTController{

    public function login($req, $res, $args)
    {
        $starface = new StarFaceVOIP();
        return $res->withJson(['login' => $starface->login(), 'subscription' => $starface->subscribeEvents()]);
    }

    public function keepalive($req, $res, $args)
    {
        $starface = new StarFaceVOIPConnector();
        return $res->withJson(['status' => $starface->keepAlive() ? 'success' : 'error']);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getPreferences($req, $res, $args)
    {
        $starface = new StarFaceVOIP();
        return $res->withJson($starface->getPreferences());
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

        $starface = new StarFaceVOIP();
        $success = $starface->setPreferences($postBody);
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

        $starface = new StarFaceVOIP();
        $response = $starface->initiateCall($postBody['msisdn']);
        return $res->withJson(['status' =>  $response !== false ? 'success' : 'error', 'callid' => $response]);
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
        $starface = new StarFaceVOIP();
        $response = $starface->hangupcall($args['callid']);
        return $res->withJson(['status' =>  $response !== false ? 'success' : 'error']);
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
        $starfaceUser = $req->getParams()['de_vertico_starface_user'];
        $starface = new StarFaceVOIP();
        $starface->handleEvent($starfaceUser, $postBody);
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
        $starface = new StarFaceVOIPConnector();
        return $res->withJson(['status' => $starface->subscribeEvents() ? 'success' : 'error']);
    }

    /**
     * handles the event
     *
     * @param $req
     * @param $res
     * @param $args
     */
    public function unsubscribeEvents($req, $res, $args){
        $starface = new StarFaceVOIPConnector();
        return $res->withJson(['status' => $starface->unsubscribeEvents() ? 'success' : 'error']);
    }
}
