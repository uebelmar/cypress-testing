<?php

namespace SpiceCRM\includes\DialogMail\KREST\controllers;

use SpiceCRM\includes\DialogMail\DialogMail;

class DialogMailController
{
    public function getUsersMails($req, $res, $args) {
        $dialogMail = new DialogMail();
        return $res->withJson($dialogMail->getUsersMails($args['id']));
    }
    public function getListStatistics($req, $res, $args) {
        $dialogMail = new DialogMail();
        return $res->withJson($dialogMail->getListStatistics($args['id']));
    }
    public function prospectListToDialogMail($req, $res, $args) {
        $dialogMail = new DialogMail();
        return $res->withJson(array('status' => 'success',$dialogMail->prospectListToDialogMail($args['id'])));
    }

    public function getCampaignTaskStatistics($req, $res, $args) {
        $dialogMail = new DialogMail();
        return $res->withJson($dialogMail->getCampaignTaskStatistics($args['id']));
    }

    public function campaignTaskToDialogMail($req, $res, $args) {
        $dialogMail = new DialogMail();
        return $res->withJson(array('status' => 'success',$dialogMail->campaignTaskToDialogMail($args['id'])));
    }
}
