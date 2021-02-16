<?php

namespace SpiceCRM\includes\MailChimp\KREST\controllers;

use SpiceCRM\includes\MailChimp\MailChimp;

class MailChimpController
{

    public function getReport($req, $res, $args)
    {
        $mailChimp = new MailChimp();
        return $res->withJson($mailChimp->getReport($args['id']));
    }

    public function getAnalytics($req, $res, $args)
    {
        $mailChimp = new MailChimp();
        return $res->withJson($mailChimp->getAnalytics($args['id']));
    }

    public function createCampaign($req, $res, $args)
    {
        $mailChimp = new MailChimp();
        $result = $mailChimp->createCampaign($req->getParsedBody(), $args['id']);
        return $res->withJson($result);
    }
    public function setCampaignContent($req, $res, $args)
    {
        $mailChimp = new MailChimp();
        return $res->withJson($mailChimp->setCampaignContent($args['id']));
    }

}
