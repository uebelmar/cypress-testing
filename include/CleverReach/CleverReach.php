<?php

namespace SpiceCRM\includes\CleverReach;


use SpiceCRM\data\BeanFactory;


/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\CleverReach\CR\tools\Rest;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class CleverReach
{

    var $rest;
    var $token;
    var $defaultListID;
    var $senderName;
    var $senderEmail;

    public function __construct()
    {
        

        $this->rest = new Rest(SpiceConfig::getInstance()->config['cleverreach']['url']);

        $this->token = $this->rest->post('/login',
            array(
                "client_id" => SpiceConfig::getInstance()->config['cleverreach']['clientid'],
                "login" => SpiceConfig::getInstance()->config['cleverreach']['login'],
                "password" => SpiceConfig::getInstance()->config['cleverreach']['password']
            )
        );

        $this->defaultListID = SpiceConfig::getInstance()->config['cleverreach']['defaultlistid'];
        $this->senderName = SpiceConfig::getInstance()->config['cleverreach']['sender_name'];
        $this->senderEmail = SpiceConfig::getInstance()->config['cleverreach']['sender_email'];
    }


    // creates global attributes for clever reach
    private function createAttributes($id)
    {
        $db = DBManagerFactory::getInstance();
        $receiverAttributes = [];
        $contact = BeanFactory::getBean('Contacts', $id);

        $this->rest->setAuthMode("bearer", $this->token);


        $globalAttributes = $this->rest->get("/attributes");

        $query = "SELECT cleverreach, crtype, spice FROM syscleverreach_fieldmapping WHERE module = 'Contacts'";
        $query = $db->query($query);

        while ($row = $db->fetchByAssoc($query)) {
            if ($row['cleverreach'] != 'email') {
                if (empty($globalAttributes)) {
                    $this->rest->post("/attributes", [
                        'name' => $row['cleverreach'],
                        'type' => $row['crtype']
                    ]);
                } else {
                    $receiverAttributes[$row['cleverreach']] = $contact->{$row['spice']};
                }
            }
        }
        return empty($globalAttributes) ? false : $receiverAttributes;


    }

    public function contactToCleverReach($id)
    {
        $db = DBManagerFactory::getInstance();

        $contact = BeanFactory::getBean('Contacts', $id);


        //connects to rest api
        $this->rest->setAuthMode("bearer", $this->token);


        // gets attributes
        $attributes = $this->createAttributes($contact->id);

        if (!empty($contact)) {
            //retrieves mapping from DB and builds the receiver array
            $receiver = [];
            $query = "SELECT * FROM syscleverreach_fieldmapping WHERE module = 'Contacts'";
            $query = $db->query($query);
            while ($row = $db->fetchByAssoc($query)) {
                if ($row['cleverreach'] == 'email') {
                    $receiver[$row['cleverreach']] = $contact->{$row['spice']};
                    $receiver['global_attributes'] = $attributes;
                }
            }
            // check if contact is new or exists on cr
            $contactsOnlineProfile = BeanFactory::getBean('ContactsOnlineProfiles');
            if (!$contactsOnlineProfile->retrieve_by_string_fields(['name' => 'CleverReach', 'parent_type' => 'Contacts', 'parent_id' => $contact->id])) {
                $user = $this->rest->post("/groups/$this->defaultListID/receivers", $receiver);
                $username = $this->rest->get("/receivers/$contact->email1");
                $contactsOnlineProfile->name = 'CleverReach';
                $contactsOnlineProfile->username = $username->id;
                $contactsOnlineProfile->parent_type = 'Contacts';
                $contactsOnlineProfile->parent_id = $contact->id;
                $contactsOnlineProfile->save();
            } else {
                $user = $this->rest->put("/groups/$this->defaultListID/receivers/$contactsOnlineProfile->username", $receiver);
            }
            return $user;
        } else {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $id, 'module' => 'Contacts']);
        }
    }

    public function deleteFromCleverReach($id)
    {
        $contact = BeanFactory::getBean('Contacts', $id);
        if (empty($contact)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $id, 'module' => 'Contacts']);
        }
        $contactsOnlineProfile = BeanFactory::getBean('ContactsOnlineProfiles');
        if ($contactsOnlineProfile->retrieve_by_string_fields(['name' => 'CleverReach', 'parent_type' => 'Contacts', 'parent_id' => $contact->id])) {
            $contactsOnlineProfile->mark_deleted($contactsOnlineProfile->id);
            $contactsOnlineProfile->save();
            $this->rest->setAuthMode("bearer", $this->token);
            $this->rest->delete("/receivers/$contactsOnlineProfile->username");
        }

    }

//    public function getUpdatesFromCleverReach($contact)
//    {
////        $contact = \SpiceCRM\data\BeanFactory::getBean('Contacts');
////        $contact->processed =true;
////        $contact->retrieve( $id);
//        $contactsOnlineProfile = \SpiceCRM\data\BeanFactory::getBean('ContactsOnlineProfiles');
//        if (!empty($contact)) {
//            if ($contactsOnlineProfile->retrieve_by_string_fields(['name' => 'CleverReach', 'parent_type' => 'Contacts', 'parent_id' => $contact->id])) {
//                $this->rest->setAuthMode("bearer", $this->token);
//                $cRUser = $this->rest->get("/receivers/$contactsOnlineProfile->username");
//                if ($contact->first_name != $cRUser->global_attributes->firstname) {
//                    $contact->first_name = $cRUser->global_attributes->firstname;
//                } if ($contact->last_name != $cRUser->global_attributes->lastname) {
//                    $contact->last_name = $cRUser->global_attributes->lastname;
//                } if ($contact->email1 != $cRUser->global_attributes->email) {
//                    $contact->email1 = $cRUser->global_attributes->email;
//                } else {
//                    return;
//                }
//            }
//
//        }
//
//
//    }


    private function createGroup($moduleList)
    {
        //connects to rest api
        $this->rest->setAuthMode("bearer", $this->token);


        $group = $this->rest->post("/groups", array('name' => $moduleList->name));
        $moduleList->ext_id = $group->id;
        $moduleList->save();

        return $moduleList->ext_id;

    }

    private function getReceiversProfiles($groupID)
    {
        $this->rest->setAuthMode("bearer", $this->token);
        $profiles = $this->rest->get("/groups/$groupID/receivers");
        return $profiles;
    }

    private function getLinkedCleverReachProfiles($listID)
    {
        $db = DBManagerFactory::getInstance();

        $list = [];
        $query = "SELECT contactsonlineprofiles.username 
            FROM prospect_lists_prospects 
            JOIN prospect_lists ON prospect_lists_prospects.prospect_list_id = prospect_lists.id
            JOIN contacts ON prospect_lists_prospects.related_id = contacts.id 
            JOIN contactsonlineprofiles ON parent_id = contacts.id
            WHERE prospect_list_id = '" . $listID . "' AND contactsonlineprofiles.name = 'CleverReach' AND contactsonlineprofiles.deleted = 0 AND prospect_lists_prospects.deleted = 0";

        $query = $db->query($query);

        while ($row = $db->fetchByAssoc($query)) {
            $list[] = $row['username'];
        }

        return $list;
    }

    public function getListStatistics($listID)
    {
        $prospectList = BeanFactory::getBean('ProspectLists', $listID);
        if (empty($prospectList->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $listID, 'module' => 'ProspectLists']);
        }
        $spiceList = $this->getLinkedCleverReachProfiles($prospectList->id);
        // if exists retrieve users
        if (empty($prospectList->ext_id)) {
            $prospectList->ext_id = $this->createGroup($prospectList);
            $prospectList->save();
        } else {
            $linkedCRProfiles = $this->getReceiversProfiles($prospectList->ext_id);
        }

        return array(
            'totalcount' => count($spiceList),
            'onCleverReach' => count($linkedCRProfiles)
        );

    }

    /**/

    public function prospectListToCleverReach($listID)
    {
        $prospectList = BeanFactory::getBean('ProspectLists', $listID);

        if (empty($prospectList->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $listID, 'module' => 'ProspectLists']);
        }

        $spiceList = $this->getLinkedCleverReachProfiles($prospectList->id);
        if (count($spiceList) == 0) {
            return;
        }

        if (empty($prospectList->ext_id)) {
            $prospectList->ext_id = $this->createGroup($prospectList);
            $prospectList->save();
        } else {
            $cleverReachProfiles = $this->getReceiversProfiles($prospectList->ext_id);
            foreach ($cleverReachProfiles as $profile) {
                $profileEmails[] = $profile->email;
            }
            $intersect = array_intersect($spiceList, $profileEmails);
            $merge = array_merge($spiceList, $profileEmails);
            if ($merge != 0) {
                $spiceList = array_diff($merge, $intersect);
            }

        }


        if (count($spiceList) == 0) {
            return;
        } else {
//            $receivers = [];

            $defaultList = $this->getReceiversProfiles($this->defaultListID);

            foreach ($defaultList as $item) {
                if (in_array($item->id, $spiceList)) {
                    $receivers[] = array(
                        "email" => $item->email,
                        "global_attributes" => array(
                            "spicecrm_id" => $item->global_attributes->spicecrm_id,
                            "spicecrm_module" => $item->global_attributes->spicecrm_module,
                            "spicecrm_firstname" => $item->global_attributes->spicecrm_firstname,
                            "spicecrm_lastname" => $item->global_attributes->spicecrm_lastname
                        )
                    );
                }
            }

            return $this->rest->post("/groups/$prospectList->ext_id/receivers", $receivers);
        }

    }

    // CAMPAIGN TASK FUNCTIONS

    private function getRelatedProspectLists($campaignTaskID)
    {
        $db = DBManagerFactory::getInstance();
        $query = "SELECT prospect_list_id FROM prospect_list_campaigntasks WHERE campaigntask_id = '" . $campaignTaskID . "'";
        $query = $db->query($query);
        while ($row = $db->fetchByAssoc($query)) {
            $prospectListsIDs[] = $row['prospect_list_id'];
        }
        foreach ($prospectListsIDs as $prospectListsID) {
            $prospectLists[] = BeanFactory::getBean('ProspectLists', $prospectListsID);
        }

        foreach ($prospectLists as $elementKey => $element) {
            foreach ($element as $valueKey => $value) {
                if ($valueKey == 'list_type' && $value == 'exempt') {
                    unset($prospectLists[$elementKey]);
                }
            }
        }

        return $prospectLists;
    }


    private function getRelatedListCleverReachUsers($campaignTaskID)
    {
        $prospectLists = $this->getRelatedProspectLists($campaignTaskID);
        foreach ($prospectLists as $prospectList) {
            $cleverReachLinkedProfiles[] = $this->getLinkedCleverReachProfiles($prospectList->id);
        }
        $relatedCleverReachUsers = call_user_func_array("array_merge", $cleverReachLinkedProfiles);
        return array_unique($relatedCleverReachUsers);
    }


    public function getCampaignTaskStatistics($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);

        if (empty($campaignTask->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $campaignTaskID, 'module' => 'CampaignTasks']);
        }
        $spiceList = $this->getRelatedListCleverReachUsers($campaignTask->id);

        if (empty($campaignTask->ext_id)) {
            $campaignTask->ext_id = $this->createGroup($campaignTask);
            $campaignTask->save();
        } else {
            $linkedCRProfiles = $this->getReceiversProfiles($campaignTask->ext_id);
        }

        return array(
            'totalcount' => count($spiceList),
            'onCleverReach' => count($linkedCRProfiles)
        );
    }


    public function campaignTaskToCleverReach($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        if (empty($campaignTask->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $campaignTaskID, 'module' => 'CampaignTasks']);
        }

        $spiceList = $this->getRelatedListCleverReachUsers($campaignTask->id);
        if (count($spiceList) == 0) {
            return;
        }

        if (empty($campaignTask->ext_id)) {
            $campaignTask->ext_id = $this->createGroup($campaignTask);
            $campaignTask->save();
        } else {
            $cleverReachProfiles = $this->getReceiversProfiles($campaignTask->ext_id);
            foreach ($cleverReachProfiles as $profile) {
                $profileEmails[] = $profile->email;
            }
            $intersect = array_intersect($spiceList, $profileEmails);
            $merge = array_merge($spiceList, $profileEmails);
            if ($merge != 0) {
                $spiceList = array_diff($merge, $intersect);
            }
        }
        if (count($spiceList) == 0) {
            return;
        } else {

            $receivers = [];

            $defaultList = $this->getReceiversProfiles($this->defaultListID);
            foreach ($defaultList as $item) {
                if (in_array($item->id, $spiceList)) {
                    $receivers[] = array(
                        "email" => $item->email,
                        "global_attributes" => array(
                            "spicecrm_id" => $item->global_attributes->spicecrm_id,
                            "spicecrm_module" => $item->global_attributes->spicecrm_module,
                            "spicecrm_firstname" => $item->global_attributes->spicecrm_firstname,
                            "spicecrm_lastname" => $item->global_attributes->spicecrm_lastname
                        )
                    );
                }
            }

            return $this->rest->post("/groups/$campaignTask->ext_id/receivers", $receivers);
        }

    }

    private function getTargetGroups($campaignTaskID)
    {
        $targetGroups = [];
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        if (empty($campaignTask->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $campaignTaskID, 'module' => 'CampaignTasks']);
        }

        if (!empty($campaignTask)) {
            $relatedProspectLists = $this->getRelatedProspectLists($campaignTask->id);
            foreach ($relatedProspectLists as $list) {
                $targetGroups[] = $list->ext_id;
            }
        }
        return $targetGroups;
    }


    public function sendMailings($mailing, $campaignTaskID)
    {
        $groupIDs = $this->getTargetGroups($campaignTaskID);

        $this->rest->setAuthMode("bearer", $this->token);

        $mailing = array("name" => $mailing['name'],
            "type" => 'html/text',
            "subject" => $mailing['subject'],
            "sender_name" => $this->senderName,
            "sender_email" => $this->senderEmail,
            "group_id" => $groupIDs,
            "html" => $mailing['html'],
            "text" => $mailing['html']
        );
        return $this->rest->post("/mailings", $mailing);
    }

    /**
     * retirves metrics for a mailing
     *
     * @param $campaignTaskID id of the campaigntask
     * @return array
     */
    public function getMailingStats($campaignTaskID)
    {
        $mailing = [];
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        if (empty($campaignTask->id)) {
            throw (new NotFoundException('Record not found.'))->setLookedFor(['id' => $campaignTaskID, 'module' => 'CampaignTasks']);
        }
        if (!empty($campaignTask) and $campaignTask->mailing_id) {
            $this->rest->setAuthMode("bearer", $this->token);

            $res = $this->rest->get("/reports/$campaignTask->mailing_id/stats");
            $mailing = ["opens" => $res->basic->unique_opened,
                "clicks" => $res->basic->unique_clicks,
                "dropouts" => $res->basic->dropouts,
                "bounces" => $res->basic->bounced];
            return $mailing;
        } else {
            return [];
        }


    }

    private function getSentStateIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $this->rest->setAuthMode("bearer", $this->token);
        $req = $this->rest->get("/reports/$campaignTask->mailing_id/receivers/sent");
        if ($req != "false") {
            foreach ($req as $item) {
                $sent[] = $item->global_attributes->spicecrm_id;
            }
            return $sent;
        } else {
            return [];
        }

    }

    private function getOpenedStateIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $this->rest->setAuthMode("bearer", $this->token);
        $req = $this->rest->get("/reports/$campaignTask->mailing_id/receivers/opened");
        if ($req != "false") {
            foreach ($req as $item) {
                $opened[] = $item->global_attributes->spicecrm_id;
            }
            return $opened;
        } else {
            return [];
        }
    }

    private function getClickedStateIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $this->rest->setAuthMode("bearer", $this->token);
        $req = $this->rest->get("/reports/$campaignTask->mailing_id/receivers/clicked");
        if ($req != "false") {
            foreach ($req as $item) {
                $clicked[] = $item->global_attributes->spicecrm_id;
            }
            return $clicked;
        } else {
            return [];
        }
    }

    private function getBouncedStateIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $this->rest->setAuthMode("bearer", $this->token);
        $req = $this->rest->get("/reports/$campaignTask->mailing_id/receivers/bounced");
        if ($req != "false") {
            foreach ($req as $item) {
                $bounced[] = $item->global_attributes->spicecrm_id;
            }
            return $bounced;
        } else {
            return [];
        }
    }

    private function getUnsubscribedStateIds($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        $this->rest->setAuthMode("bearer", $this->token);
        $req = $this->rest->get("/reports/$campaignTask->mailing_id/receivers/unsubscribed");
        if ($req != "false") {
            foreach ($req as $item) {
                $bounced[] = $item->global_attributes->spicecrm_id;
            }
            return $bounced;
        } else {
            return [];
        }
    }

    /**
     * retrieves a report state for the mailing receivers
     *
     * @param $campaignTaskID id of the campaigntask
     * @return array
     */

    // state: sent/opened/clicked/bounced/unsubscribed
    public function getReportState($campaignTaskID)
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
                "sent" => $this->getSentStateIds($campaignTask->id),
                "opened" => $this->getOpenedStateIds($campaignTask->id),
                "clicked" => $this->getClickedStateIds($campaignTask->id),
                "bounced" => $this->getBouncedStateIds($campaignTask->id),
                "unsubscribed" => $this->getUnsubscribedStateIds($campaignTask->id)
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
            if (in_array($campaignLog->target_id, $report['clicked'])) {

                $campaignLog->activity_type = "link";
                $campaignLog->save();
            }
            if (in_array($campaignLog->target_id, $report['bounced'])) {

                $campaignLog->activity_type = "bounced";
                $campaignLog->save();
            }
            if (in_array($campaignLog->target_id, $report['unsubscribed'])) {

                $campaignLog->activity_type = "removed";
                $campaignLog->save();
            }
        }
        return $report;
    }
}
