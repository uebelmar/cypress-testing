<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\TextMessages;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\SpiceFTSManager\SpiceFTSHandler;
use SpiceCRM\modules\Mailboxes\Mailbox;

class TextMessage extends SugarBean
{
    public $module_dir = 'TextMessages';
    public $table_name = "textmessages";
    public $object_name = "TextMessage";

    const DIRECTION_INBOUND  = 'i';
    const DIRECTION_OUTBOUND = 'o';

    /**
     * Openness Statuses
     */
    const OPENNESS_OPEN          = 'open';
    const OPENNESS_USER_CLOSED   = 'user_closed';
    const OPENNESS_SYSTEM_CLOSED = 'system_closed';

    public function bean_implements($interface) {
        switch($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    public function save($check_notify = false, $fts_index_bean = true) {
        parent::save($check_notify, $fts_index_bean);

        if ($this->to_be_sent) {
            try {
                $result = $this->send();
                if ($result['status'] == 'failed') {
                    return $result;
                }
                $this->to_be_sent = false;
            } catch (\Exception $e) {
                $result = [
                    'result'  => false,
                    'message' => 'Message not sent: ' . $e->getMessage(),
                ];
            }
        }

        return $result;
    }

    private function send() {
        // todo implement it
        if ($this->mailbox_id) {
            $mailbox = BeanFactory::getBean('Mailboxes', $this->mailbox_id);
        } else {
            // todo probably nothing
        }

        $mailbox->initTransportHandler();
        $result = $mailbox->transport_handler->sendMail($this);

        $this->error_message   = $result['error_message'] ?? null;
        $this->delivery_status = $result['status'] ?? null;
        $this->message_id      = $result['message_id'] ?? null;
        $this->to_be_sent      = false;
        $this->save();

        return $result;
    }

    public static function findByMessageId($message_id) {
        $db = DBManagerFactory::getInstance();

        $query = "SELECT id FROM textmessage WHERE message_id='" . $message_id . "'";
        $q = $db->query($query);

        while($row = $db->fetchRow($q)) {
            $message = BeanFactory::getBean('TextMessages', $row['id']);
        }

        if (!isset($message)) {
            throw new Exception('Cannot find Text Message', 404);
        } else {
            return $message;
        }
    }

    public static function convertToTextMessage($payload) {
        $db = DBManagerFactory::getInstance();
        if ($payload['Body'] == "" || $payload['To'] == "" || $payload['MessageSid'] == "") {
            throw new Exception('Incomplete inbound Text Message', 404);
        }

        $textMessage = BeanFactory::getBean('TextMessages');

        try {
            $phonenumber = $payload['From'];
            if (substr($phonenumber, 0, 2) == 00) {
                $phonenumber = '+' . substr($phonenumber, 2);
            }

            $searchresultsraw = SpiceFTSHandler::getInstance()->searchModuleByPhoneNumber('Contacts', str_replace('+', '', $phonenumber));
            $textMessage->contact_id = $searchresultsraw['hits']['hits'][0]['_id'];
        } catch (\Exception $e) {
            $textMessage->contact_id = null;
            $textMessage->description = json_encode($e);
        }

        try {
            $mailbox = Mailbox::findByPhoneNumber($payload['To']);
            $textMessage->mailbox_id = $mailbox->id;
        } catch (\Exception $e) {
            $textMessage->mailbox_id = null;
        }

        $textMessage->name = $payload['Body'];
        $textMessage->description = $payload['Body'];
        $textMessage->msisdn      = $payload['From'];
        $textMessage->message_id  = $payload['MessageSid'];
        $textMessage->date_sent   = date('Y-m-d H:i:s');
        $textMessage->direction   = self::DIRECTION_INBOUND;
        $textMessage->assigned_user_id = '1';
        $textMessage->created_by = '1';
        $textMessage->delivery_status = 'received';

        return $textMessage;
    }
}
