<?php

namespace SpiceCRM\includes\CleverReach\KREST\controllers;

use SpiceCRM\includes\CleverReach\CleverReach;

class CleverReachController
{
    public function getListStatistics($req, $res, $args)
    {
        $cleverReach = new CleverReach();
        return $res->withJson($cleverReach->getListStatistics($args['id']));
    }

    public function prospectListToCleverReach($req, $res, $args)
    {
        $cleverReach = new CleverReach();
        return $res->withJson(array('status' => 'success', $cleverReach->prospectListToCleverReach($args['id'])));
    }

    public function getCampaignTaskStatistics($req, $res, $args)
    {
        $cleverReach = new CleverReach();
        return $res->withJson($cleverReach->getCampaignTaskStatistics($args['id']));
    }

    public function campaignTaskToCleverReach($req, $res, $args)
    {
        $cleverReach = new CleverReach();
        return $res->withJson(array('status' => 'success', $cleverReach->campaignTaskToCleverReach($args['id'])));
    }

    public function sendMailings($req, $res, $args)
    {
        $cleverReach = new CleverReach();
        $result = $cleverReach->sendMailings($req->getParsedBody(), $args['id']);
        return $res->withJson($result);
    }

    public function getMailingStats($req, $res, $args)
    {
        $cleverReach = new CleverReach();
        return $res->withJson($cleverReach->getMailingStats($args['id']));
    }

    public function getReportState($req, $res, $args)
    {
        $cleverReach = new CleverReach();
        return $res->withJson($cleverReach->getReportState($args['id']));
    }
}
