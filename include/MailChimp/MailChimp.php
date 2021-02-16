<?php

namespace SpiceCRM\includes\MailChimp;

use SpiceCRM\data\BeanFactory;

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\MailChimp\tools\MailChimpRest;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class MailChimp
{

    var $chimp;
    var $batch;
    var $replyEmail;
    var $fromName;
    var $defaultListID;

    public function __construct()
    {
        
        $this->chimp = new MailChimpRest(SpiceConfig::getInstance()->config['mailchimp']['api_key']);
//        $this->batch = $this->chimp->new_batch();
        $this->defaultListID = SpiceConfig::getInstance()->config['mailchimp']['defaultlistid'];
        $this->replyEmail = SpiceConfig::getInstance()->config['mailchimp']['reply_to'];
        $this->fromName = SpiceConfig::getInstance()->config['mailchimp']['from_name'];
    }

    private function mapping($id)
    {
        $db = DBManagerFactory::getInstance();
        $contact = BeanFactory::getBean('Contacts', $id);
        $member = [];
        $query = "SELECT * FROM sysmailchimp_fieldmapping WHERE module = 'Contacts'";
        $query = $db->query($query);
        while ($row = $db->fetchByAssoc($query)) {
            if ($row['mailchimp'] == 'email_address') {
                $member[$row['mailchimp']] = $contact->{$row['spice']};
            }
            if ($row['mailchimp'] != 'email_address') {
                $member['merge_fields'][$row['mailchimp']] = $contact->{$row['spice']};
            }
        }
        $member['status'] = 'subscribed';

        // only for development/testing purpose: replacing email domain with freddiesjokes.com because mailchimp can detect fake email addresses

//        $validEmail = strtok($member['email_address'], '@') . '@freddiesjokes.com';
//        $member['email_address'] = $validEmail;

        return $member;
    }

    public function contactToMailChimp($id)
    {

        $contact = BeanFactory::getBean('Contacts', $id);

        if (!empty($contact)) {
            $member = $this->mapping($contact->id);

            // this is the hashed email and the id of the contact
            $subscriber_hash = MailChimpRest::subscriberHash($member['email_address']);

            $contactsOnlineProfile = BeanFactory::getBean('ContactsOnlineProfiles');
            if (!$contactsOnlineProfile->retrieve_by_string_fields(['name' => 'MailChimp', 'parent_type' => 'Contacts', 'parent_id' => $contact->id])) {
                $response = $this->chimp->post("lists/$this->defaultListID/members", $member);
                $contactsOnlineProfile->name = 'MailChimp';
                $contactsOnlineProfile->username = $subscriber_hash;
                $contactsOnlineProfile->parent_type = 'Contacts';
                $contactsOnlineProfile->parent_id = $contact->id;
                $contactsOnlineProfile->save();
            } else {
                $response = $this->chimp->put("lists/$this->defaultListID/members/$contactsOnlineProfile->username", $member);
            }
            return $response;
        } else {
            return false;
        }
    }

    public function deleteFromMailChimp($id)
    {
        $contact = BeanFactory::getBean('Contacts', $id);
        $unsub = $this->mapping($contact->id);
        $unsub['status'] = "unsubscribed";
        $contactsOnlineProfile = BeanFactory::getBean('ContactsOnlineProfiles');
        if ($contactsOnlineProfile->retrieve_by_string_fields(['name' => 'MailChimp', 'parent_type' => 'Contacts', 'parent_id' => $contact->id])) {
            $contactsOnlineProfile->mark_deleted($contactsOnlineProfile->id);
            $contactsOnlineProfile->save();
            $this->chimp->put("lists/$this->defaultListID/members/$contactsOnlineProfile->username", $unsub);
            if ($this->chimp->success()) {
                return true;
            } else {
                $this->chimp->getLastError();
            }
        }

    }


    private function getTargetList($campaignTaskID) {
        $db = DBManagerFactory::getInstance();
        $query = "SELECT * FROM prospect_list_campaigntasks 
        JOIN prospect_lists ON prospect_list_id = prospect_lists.id 
        WHERE campaigntask_id ='" . $campaignTaskID . "'";
        $query = $db->query($query);
        while ($row = $db->fetchByAssoc($query)) {
            if($row["ext_id"] == $this->defaultListID) {
                $targetListId = $row['prospect_list_id'];
            }
        }
        $targetList = BeanFactory::getBean("ProspectLists", $targetListId);
        return $targetList->ext_id;
    }


    public function createCampaign($campaign, $campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $listId = $this->getTargetList($campaignTask->id);
        if(!empty($listId)) {
            $data = ["recipients" => ["list_id" => $listId],
                "type" => $campaign['type'],
                "settings" => ["subject_line" => $campaign['subject'],
                    "title" => $campaign['name'],
                    "preview_text" => $campaign['text'],
                    "reply_to" => $this->replyEmail,
                    "from_name" => $this->fromName,
                ]];
            return $this->chimp->post("/campaigns", $data);
        }

    }


    // doesnt work YET

    public function setCampaignContent($campaignTaskID) {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $content = ["plain_text" => 'my plain text',
            "html" => '<div mc:edit="mytext">my plain text</div>'];

        return $this->chimp->put("/campaigns/$campaignTask->ext_id/content", $content);
//        return  $this->chimp->get("/templates/262475/default-content");

    }

    private function getSentDetailsIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $req = $this->chimp->get("/reports/$campaignTask->ext_id/sent-to");
        if (!empty($req['sent_to'])) {
            foreach ($req['sent_to'] as $item) {
                $sent[] = $item['merge_fields']['SPICEID'];
            }
            return $sent;
        } else {
            return [];
        }

    }

    private function getOpenedDetailsIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $req = $this->chimp->get("/reports/$campaignTask->ext_id/open-details");
        if (!empty($req['members'])) {
            foreach ($req['members'] as $item) {
                $opened[] = $item['merge_fields']['SPICEID'];
            }
            return $opened;
        } else {
            return [];
        }
    }

//    private function getClickedDetailsIds($campaignTaskID)
//    {
//        $campaignTask = \SpiceCRM\data\BeanFactory::getBean('CampaignTasks', $campaignTaskID);
//        $req = $this->chimp->get("/reports/$campaignTask->ext_id/click-details");
//
//        if (!empty($req['urls_clicked'])) {
//            foreach ($req['urls_clicked'] as $item) {
//                $clicked[] = $item->global_attributes->spicecrm_id;
//            }
//            return $clicked;
//        } else {
//            return [];
//        }
//    }

//    private function getBouncedDetailsIds($campaignTaskID)
//    {
//        $campaignTask = \SpiceCRM\data\BeanFactory::getBean('CampaignTasks', $campaignTaskID);
//        $this->rest->setAuthMode("bearer", $this->token);
//        $req = $this->rest->get("/reports/$campaignTask->mailing_id/receivers/bounced");
//        if ($req != "false") {
//            foreach ($req as $item) {
//                $bounced[] = $item->global_attributes->spicecrm_id;
//            }
//            return $bounced;
//        } else {
//            return [];
//        }
//    }
//
    private function getUnsubscribedDetailsIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $req = $this->chimp->get("/reports/$campaignTask->ext-id/unsubscribed");
        if (!empty($req['unsubscribes'])) {
            foreach ($req['unsubscribes'] as $item) {
                $unsubscribed[] = $item['merge_fields']['SPICEID'];
            }
            return $unsubscribed;
        } else {
            return [];
        }
    }

    public function getReport($campaignTaskID)
    {
        $db = DBManagerFactory::getInstance();
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        if (empty($campaignTask->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $campaignTaskID, 'module' => 'CampaignTasks']);
        }

        $query = "SELECT id FROM campaign_log WHERE campaigntask_id = '" . $campaignTask->id . "'";
        $query = $db->query($query);
        while ($row = $db->fetchByAssoc($query)) {
            $campaignLogIds[] = $row['id'];
        }
        foreach ($campaignLogIds as $id) {
            $campaignLogs[] = BeanFactory::getBean('CampaignLog', $id);
        }

        if (!empty($campaignTask) and $campaignTask->activated == 1) {
            $report = array(
                "sent" => $this->getSentDetailsIds($campaignTask->id),
                "opened" => $this->getOpenedDetailsIds($campaignTask->id),
                "unsubscribed" => $this->getUnsubscribedDetailsIds($campaignTask->id)
            );
        }

        // if campaignLogId is in one of the report arrays, save the value of activity_type according to the array of the key

        foreach ($campaignLogs as $campaignLog) {
            if (in_array($campaignLog->target_id, $report['sent'])) {

                $campaignLog->activity_type = "sent";
                $campaignLog->save();
            }
            if (in_array($campaignLog->target_id, $report['opened'])) {

                $campaignLog->activity_type = "opened";
                $campaignLog->save();
            }
            if (in_array($campaignLog->target_id, $report['unsubscribed'])) {

                $campaignLog->activity_type = "removed";
                $campaignLog->save();
            }
        }
        return $report;
    }

    public function getAnalytics($campaignTaskId) {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskId);
        $analytics = [];
        if (empty($campaignTask->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $campaignTaskId, 'module' => 'CampaignTasks']);
        }
        if (!empty($campaignTask) and $campaignTask->ext_id) {
            $res = $this->chimp->get("/reports/$campaignTask->ext_id");
            $analytics = ["campaigntitle" => $res["campaign_title"],
                "type" => $res["type"],
                "sendtime" => $res['send_time'],
                "sent" => $res['emails_sent'],
                "opens" => $res['opens']['opens_total'],
                "clicks" => $res['clicks']['clicks_total'],
                "unsub" => $res['unsubscribed'],
                "bounces" => $res['bounces']['hard_bounces']];
            return $analytics;
        } else {
            return [];
        }


    }

}

