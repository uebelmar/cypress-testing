<?php

namespace SpiceCRM\modules\GoogleOAuth\KREST\controllers;

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\GoogleOAuth\GoogleOAuthRESTHandler;

class GoogleOauthController{

    /**
     * saves an authorisation token
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleAuthSetToken($req, $res, $args){
        $handler = new GoogleOAuthRESTHandler();
        $RESTManager = RESTManager::getInstance();
        $result = $handler->saveToken($req->getQueryParams());
        if ($result['result'] == true) {
            return $res->withJson($RESTManager->getLoginData());
        } else {
            return $res->withJson($result);
        }

    }

    /**
     * authenticates with an password
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleAuthGetCredential($req, $res, $args){
        $handler = new GoogleOAuthRESTHandler();
        $RESTManager = RESTManager::getInstance();
        $result = $handler->useCredentials($req->getQueryParams());
        return $res->withJson($RESTManager->getLoginData());

    }

    /**
     * archives an email
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleAuthArchiveMail($req, $res, $args){
        $handler = new GoogleOAuthRESTHandler();
        $result = $handler->archiveEmail($req->getQueryParams());
        return $res->withJson($req->getQueryParams());

    }

}
