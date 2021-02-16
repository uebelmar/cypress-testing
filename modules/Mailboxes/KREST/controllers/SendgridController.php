<?php
namespace SpiceCRM\modules\Mailboxes\KREST\controllers;

use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\modules\Emails\Email;
use Exception;
use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use Slim\Routing\RouteCollectorProxy;


class SendgridController
{
    public function handleSendgridEvents($req, $res, $args) {
        $data = file_get_contents("php://input");
        $events = json_decode($data, true);

        foreach ($events as $event) {
            try {
                $email = Email::findByMessageId($event['smtp-id']);
                $email->status = $event['event'];
                $email->save();
            } catch (Exception $e) {
                LoggerManager::getLogger()->info($e->getMessage());
            }

            /*switch ($event['event']) {
                case 'delivered':
                    break;
                case 'processed':
                    break;
                case 'dropped':
                    break;
                case 'bounce':
                    break;
                case 'deferred':
                    break;
                case 'open':
                    break;
                case 'click':
                    break;
                case 'unsubscribe':
                    break;
                case 'spamreport':
                    break;
            }*/
        }
    }

    public function SendGridHandleEvent($req, $res, $args){
        global $timedate, $log;
        $db = DBManagerFactory::getInstance();
        $body = $req->getParsedBody();
        foreach ($body as $event) {
            $messageData = explode('.', $event['sg_message_id']);
            $emails = $db->query("SELECT id FROM emails WHERE message_id='{$messageData[0]}'");
            while ($email = $db->fetchByAssoc($emails)) {

                $campaignLogs = $db->query("SELECT id FROM campaign_log WHERE related_id = '{$email['id']}' AND related_type = 'Emails'");
                while ($campaignLog = $db->fetchByAssoc($campaignLogs)) {
                    $campaignLogBean = BeanFactory::getBean('CampaignLog', $campaignLog['id']);
                    switch ($event['event']) {
                        case 'open';
                            $campaignLogBean->activity_type = 'opened';
                            break;
                        case 'bounce';
                        case 'blocked';
                            $campaignLogBean->activity_type = 'bounced';
                            $emailAddress = BeanFactory::getBean('EmailAddresses');
                            $emailAddress->markEmailAddressInvalid($event['email']);
                            break;
                        default:
                            $campaignLogBean->activity_type = $event['event'];
                            break;
                    }
                    $campaignLog->activity_date = $timedate->nowDb();
                    $campaignLogBean->save();;
                }
            }
        }
    }

}
