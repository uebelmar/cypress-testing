<?php

namespace SpiceCRM\includes\DialogMail;


/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use IXR\Client\ClientSSL;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;


class DialogMail
{

    var $client;
    var $code;

    public function __construct()
    {
        

        $this->code = SpiceConfig::getInstance()->config['dialogmail']['code'];

        $this->client = new ClientSSL(SpiceConfig::getInstance()->config['dialogmail']['url']);

    }

    public function contactToDialogMail($id)
    {
        $db = DBManagerFactory::getInstance();

        $contact = BeanFactory::getBean('Contacts', $id);
        if (!empty($contact)) {

            // get the mapping from the database
            $query = "SELECT * FROM sysdialogmail_maillogs WHERE module = 'Contacts'";
            $query = $db->query($query);
            while ($row = $db->fetchByAssoc($query)) {
                $mapping[$row['spice']] = $row['dialogmail'];
            }

            // build the user array
            $user = [];
            foreach ($mapping as $spice => $dialogmail) {
                $user[$dialogmail] = $contact->$spice;
            }

            // check if contact is new or exists on dialogmail
            $contactsOnlineProfile = BeanFactory::getBean('ContactsOnlineProfiles');
            $response = null;
            if (!$contactsOnlineProfile->retrieve_by_string_fields(['name' => 'Maillog', 'parent_type' => 'Contacts', 'parent_id' => $contact->id])) {
                if (!$this->client->query('dmail.createUser', array($this->code, $user))) {
                    // Error handling
                    // ToDo:
                    // die('Error: ' . $this->client->getErrorCode() . "<br>" . $this->client->getErrorMessage());
                }
                $UserID = $this->client->getResponse();
                if (gettype($UserID) != 'integer' && gettype($UserID) != 'string') return false;
                // save the new profile
                $contactsOnlineProfile->name = 'Maillog';
                $contactsOnlineProfile->username = $UserID;
                $contactsOnlineProfile->parent_type = 'Contacts';
                $contactsOnlineProfile->parent_id = $contact->id;
                $contactsOnlineProfileId = $contactsOnlineProfile->save();
                $response = $UserID && $contactsOnlineProfileId;
            } else {
                // call to dialogmail with update
                if (!empty($contactsOnlineProfile->username) && !$this->client->query('dmail.updateUser', array($this->code, $contactsOnlineProfile->username, $user))) {
                    // Error handling
                    // ToDo:
                    // die('Error: ' . $this->client->getErrorCode() . "<br>" . $this->client->getErrorMessage());
                }
                $response = $this->client->getResponse();
            }
            return $response;
        } else {

            return false;

        }


    }

    // 4210797
    // $contactsOnlineProfile->username
    public function getUsersMails($id)
    {
        $contact = BeanFactory::getBean('Contacts', $id);
        $contactsOnlineProfile = BeanFactory::getBean('ContactsOnlineProfiles');
        //$contactsOnlineProfile->retrieve_by_string_fields(['parent_id' => $id, 'name' => 'DialogMail']);
        if (!empty($contact) AND $contactsOnlineProfile->retrieve_by_string_fields(['parent_id' => $id, 'name' => 'DialogMail'])) {
            if (!$this->client->query('dmail.getUserMails', array($this->code, $contactsOnlineProfile->username))) {
                // Error handling
                // ToDo:
                // die('Error: ' . $this->client->getErrorCode() . "<br>" . $this->client->getErrorMessage());
            }
            return $this->client->getResponse();
        }

        return [];

    }

    private function createGroup($moduleList)
    {
        if (!$this->client->query('dmail.createGroup', array($this->code, $moduleList->name, 1, 0))) {
            // Error handling
            // ToDo:
            // die('Error: ' . $this->client->getErrorCode() . "<br>" . $this->client->getErrorMessage());

            if ($this->client->getErrorCode() == '-26') {
                if (!$this->client->query('dmail.listGroups', $this->code)) {
                }
                $groups = $this->client->getResponse();
                foreach ($groups as $group) {
                    if ($group['bezeichnung'] == $moduleList->name) {
                        $groupID = $group['id'];
                    }
                }
            }
        } else {
            $groupID = $this->client->getResponse();
            $moduleList->ext_id = $groupID;
            $moduleList->save();
        }
        return $groupID;
    }

    /**
     * retirves all userids linked to a prospectlist id that are synced to Dialog Mail
     *
     * @param $listID id of the prospect list
     * @return array
     */
    private function getLinkedDialogMailProfiles($listID)
    {
        $db = DBManagerFactory::getInstance();

        $list = [];
        $query = "SELECT contactsonlineprofiles.username userid
            FROM prospect_lists_prospects 
            JOIN prospect_lists ON prospect_lists_prospects.prospect_list_id = prospect_lists.id
            JOIN contacts ON prospect_lists_prospects.related_id = contacts.id 
            JOIN contactsonlineprofiles ON parent_id = contacts.id
            WHERE prospect_list_id = '" . $listID . "' AND contactsonlineprofiles.name = 'Maillog' AND contactsonlineprofiles.deleted = 0 AND prospect_lists_prospects.deleted = 0";

        $query = $db->query($query);

        while ($row = $db->fetchByAssoc($query)) {
            $list[] = $row['userid'];
        }

        return $list;
    }

    /**
     * retrieves all linked users for a given groupid from DialogMail
     *
     * @param $dialogmailGroupId
     * @return mixed
     */
    private function getProfilesFromDialogMail($dialogmailGroupId)
    {
        if (!$this->client->query('dmail.listUserGroup', array($this->code, $dialogmailGroupId))) {
        }
        return $this->client->getResponse();
    }

    /**
     * @param $listID
     * @return array
     */
    public function getListStatistics($listID)
    {
        $prospectList = BeanFactory::getBean('ProspectLists', $listID);

        // wenn nicht existier -> error
        if (empty($prospectList->id)) {
            // thor KREST Error
        }

        $list = $this->getLinkedDialogMailProfiles($prospectList->id);

        // check if list exists on Dialogmail
        // if exists retrieve users
//        $dialogMailLinkedProfileIds = [];
        if (empty($prospectList->ext_id)) {
            $prospectList->ext_id = $this->createGroup($prospectList);
            $prospectList->save();

        } else {
            $dialogMailLinkedProfiles = $this->getProfilesFromDialogMail($prospectList->ext_id);
        }

        return array(
            'totalcount' => count($list),
            'onDialogMail' => count($dialogMailLinkedProfiles)
        );
    }


    /**
     * builds the list to be added for a groupid for Dialogmail and sends them
     * if group does not exist creates the group on Dialogmail
     *
     * @param $listID the guid of the prodpectlist to be added and synced
     * @return
     */
    public function prospectListToDialogMail($listID)
    {
        $prospectList = BeanFactory::getBean('ProspectLists', $listID);
        // wenn nicht existiert -> error
        if (empty($prospectList->id)) {
            // throw KREST Error
        }

        // get all candidates and check if count > 0
        $list = $this->getLinkedDialogMailProfiles($prospectList->id);
        if (count($list) == 0) {
            // throw error or simply return
            return;
        }

        // check if group exists .. if not create it
        if (empty($prospectList->ext_id)) {
            $prospectList->ext_id = $this->createGroup($prospectList);
            $prospectList->save();
        } else {
            $dialogMailLinkedProfiles = $this->getProfilesFromDialogMail($prospectList->ext_id);
            // diff the returns list with the candidate IDs ... and remove all ids from $list, that are already linked
            foreach ($dialogMailLinkedProfiles as $profile) {
                $dialogMailLinkedProfileIds[] = $profile['id'];
            }
            $intersect = array_intersect($list, $dialogMailLinkedProfileIds);
            $merge = array_merge($list, $dialogMailLinkedProfileIds);
            $list = array_diff($merge, $intersect);
        }

//         check if all are synced .. if yes exit
        if (count($list) == 0) {
            return;
        }

        // process remaining list and add the Groupname ... ->
        $updatelist = [];
        foreach ($list as $dialogmailuserid) {
            $updatelist[] = [
                'id' => $dialogmailuserid,
                'gruppen' => $prospectList->name
            ];
        }


//       send to Dialogmail and update the profiles
        if (!$this->client->query('dmail.setSyncUsers', array($this->code, $updatelist, 0, 1, 1, 1))) {
            // Error handling
            // ToDo:
            // die('Error: ' . $this->client->getErrorCode() . "<br>" . $this->client->getErrorMessage());
        }


    }

    /**
     * reads all lists from prospectlists (except list_type == exempt) and gets their dmail user ids
     * @param $campaignTaskID the guid of the campaignTask to be added and synced
     * @return array
     */

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

    private function getRelatedListDMailIDs($campaignTaskID)
    {
        $prospectLists = $this->getRelatedProspectLists($campaignTaskID);
        foreach ($prospectLists as $prospectList) {
            $dmailLinkedProfiles[] = $this->getLinkedDialogMailProfiles($prospectList->id);
        }
        $relatedDialogMailIDs = call_user_func_array("array_merge", $dmailLinkedProfiles);
        return $relatedDialogMailIDs;
    }

    private function getRelatedListDMailIProfiles($campaignTaskID)
    {
        $prospectLists = $this->getRelatedProspectLists($campaignTaskID);
        foreach ($prospectLists as $prospectList) {
            $dmailProfiles[] = $this->getProfilesFromDialogMail($prospectList->ext_id);
        }
        $relatedDialogMailProfs = call_user_func_array("array_merge", $dmailProfiles);
        return $relatedDialogMailProfs;
    }

    /**
     * reads all lists from prospectlists (except list_type == exempt)
     * @param $campaignTaskID the guid of the campaignTask to be added and synced
     * @return array
     */

    public function getCampaignTaskStatistics($campaignTaskID)
    {

        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);

        // wenn nicht existier -> error
        if (empty($campaignTask->id)) {
            // thor KREST Error
        }

        $list = $this->getRelatedListDMailIDs($campaignTask->id);


        // check if list exists on Dialogmail
//         if exists retrieve users
//        $dialogMailLinkedProfileIds = [];
        if (empty($campaignTask->ext_id)) {
            $campaignTask->ext_id = $this->createGroup($campaignTask);
            $campaignTask->save();

        } else {
            $dialogMailLinkedProfiles = $this->getRelatedListDMailIProfiles($campaignTask->id);
        }

        return array(
            'totalcount' => count($list),
            'onDialogMail' => count($dialogMailLinkedProfiles)
        );
    }

    public function campaignTaskToDialogMail($campaignTaskID)
    {
        $campaignTask = BeanFactory::getBean('CampaignTasks', $campaignTaskID);
        // wenn nicht existiert -> error
        if (empty($campaignTask->id)) {
            // throw KREST Error
        }

        // get all candidates and check if count > 0
        $list = $this->getRelatedListDMailIDs($campaignTask->id);
        if (count($list) == 0) {
            // throw error or simply return
            return;
        }

        // check if group exists .. if not create it
        if (empty($campaignTask->ext_id)) {
            $campaignTask->ext_id = $this->createGroup($campaignTask);
            $campaignTask->save();
        } else {
            $dialogMailLinkedProfiles = $this->getProfilesFromDialogMail($campaignTask->ext_id);
            // diff the returns list with the candidate IDs ... and remove all ids from $list, that are already linked
            foreach ($dialogMailLinkedProfiles as $profile) {
                $dialogMailLinkedProfileIds[] = $profile['id'];
            }

            $intersect = array_intersect($list, $dialogMailLinkedProfileIds);
            $merge = array_merge($list, $dialogMailLinkedProfileIds);
            if($merge != 0) {
                $list = array_diff($merge, $intersect);
            }
    }

//         check if all are synced .. if yes exit
        if (count($list) == 0) {
            return;
        }
//
        // process remaining list and add the Groupname ... ->
        $updatelist = [];
        foreach ($list as $dialogmailuserid) {
            $updatelist[] = [
                'id' => $dialogmailuserid,
                'gruppen' => $campaignTask->name
            ];
        }

////       send to Dialogmail and update the profiles
        if (!$this->client->query('dmail.setSyncUsers', array($this->code, $updatelist, 0, 1, 1, 1))) {
            // Error handling
            // ToDo:
            // die('Error: ' . $this->client->getErrorCode() . "<br>" . $this->client->getErrorMessage());
        }


    }

}
