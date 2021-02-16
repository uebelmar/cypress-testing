<?php

namespace SpiceCRM\modules\Mailboxes\KREST\controllers;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\modules\TextMessages\TextMessage;

class TwilioController
{
    public function updateStatus($req, $res, $args) {
        $body = $req->getParsedBody();

        global $timedate, $log;
        $db = DBManagerFactory::getInstance();

        // todo search for the text message using the SID and update the status
        try {
            $textMessage = TextMessage::findByMessageId($body['sid']);
            $textMessage->delivery_status = $body['status'];
            $textMessage->save();
        } catch (Exception $e) {
            return $res->withJson($e->getMessage(), $e->getErrorCode());
        }
    }

    public function saveInboundMessage($req, $res, $args) {
        $body = $req->getParsedBody();

        try {
            $textMessage = TextMessage::convertToTextMessage($body);
            $textMessage->save();
        } catch (Exception $e) {
            return $res->withJson($e->getMessage(), $e->getErrorCode());
        }
    }
}
