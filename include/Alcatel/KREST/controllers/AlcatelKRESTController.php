<?php
namespace SpiceCRM\includes\Alcatel\KREST\controllers;

use SpiceCRM\includes\Alcatel\Alcatel;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;

class AlcatelKRESTController
{
    /**
     * Handles the events coming from the Alcatel OpenTouch.
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     * @return mixed
     */
    public function handleEvent($req, $res, $args) {
        $body = $req->getParsedBody();
        $alcatel = new Alcatel();
        $result = $alcatel->handleEvent($body);

        return $res->withJson($result);
    }

    /**
     * Sets the preferences (login credentials) for the Alcatel OpenTouch.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function setPreferences($req, $res, $args) {
        $postBody = $req->getParsedBody();

        $alcatel = new Alcatel();
        $success = $alcatel->setPreferences($postBody);

        return $res->withJson(['status' =>  $success ? 'success' : 'error']);
    }

    /**
     * Returns the preferences (login credentials) for the Alcatel OpenTouch.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getPreferences($req, $res, $args) {

        $alcatel = new Alcatel();
        $preferences = $alcatel->getPreferences();
        $preferences['host'] = SpiceConfig::getInstance()->config['alcatel']['host']; // todo change it

        return $res->withJson($preferences);
    }

    /**
     * Logs in to the Alcatel OpenTouch.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function login($req, $res, $args) {
        $alcatel      = new Alcatel();
        $login        = $alcatel->loginToNode();
        $subscription = $login ? true : false;
        return $res->withJson(['login' => $login, 'subscription' => $subscription]);
    }

    // todo remove that later
    public function keepAlive($req, $res, $args) {
        return $res->withJson(['status' => 'success']);
    }

    /**
     * Starts an outgoing call.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function initiateCall($req, $res, $args) {
        $postBody = $req->getParsedBody();

        $alcatel = new Alcatel();
        $response = $alcatel->initiateCall($postBody['msisdn']);
        return $res->withJson(['status' =>  $response !== false ? 'success' : 'error', 'callid' => $response]);
    }

    /**
     * Hangs up a call.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function hangupCall($req, $res, $args) {
        $alcatel = new Alcatel();
        $response = $alcatel->hangupcall($args['callid']);
        return $res->withJson(['status' =>  $response !== false ? 'success' : 'error']);
    }

    /**
     * Returns the phone number formatted to be dialable by Alcatel.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function dialable($req, $res, $args) {
        $postBody = $req->getParsedBody();

        $alcatel = new Alcatel();
        $response = $alcatel->dialable($postBody['msisdn']);
        return $res->withJson(['status' =>  $response !== false ? 'success' : 'error', 'callid' => $response]);
    }
}
