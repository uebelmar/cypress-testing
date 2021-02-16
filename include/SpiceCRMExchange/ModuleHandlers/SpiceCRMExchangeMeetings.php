<?php
namespace SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers;

use Exception;
use jamesiarmes\PhpEws\ArrayType\ArrayOfStringsType;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class SpiceCRMExchangeMeetings extends SpiceCRMExchangeEvents
{
    protected $moduleName     = 'Meetings';
    protected $itemName       = 'CalendarItem';
    protected $tableName      = 'meetings';
    protected $pivotTableName = 'meetings_users';
    protected $pivotBeanId    = 'meeting_id';

//    protected $updateFieldMapping = [
//        'Subject' => [
//            'itemField' => 'Subject',
//            'beanField' => 'name',
//            'subtype'   => UnindexedFieldURIType::ITEM_SUBJECT,
//        ],
//        'Location' => [
//            'itemField' => 'Location',
//            'beanField' => 'location',
//            'subtype'   => UnindexedFieldURIType::CALENDAR_LOCATION,
//        ],
//        'Start' => [
//            'itemField' => 'Start',
//            'beanField' => 'date_start',
//            'type'      => 'datetime',
//            'subtype'   => UnindexedFieldURIType::CALENDAR_START,
//        ],
//        'End' => [
//            'itemField' => 'End',
//            'beanField' => 'date_end',
//            'type'      => 'datetime',
//            'subtype'   => UnindexedFieldURIType::CALENDAR_END,
//        ],
//    ];

    /**
     * mapEWSToBean
     *
     * Maps the EWS object into a Sugar bean.
     *
     * @param $exchangeObject
     * @return SugarBean
     * @throws Exception
     */
    public function mapEWSToBean($exchangeObject) {
        $bean = parent::mapEWSToBean($exchangeObject);
        $bean->location = $exchangeObject->Location;

        $this->spiceBean = $bean;

        return $bean;
    }

    /**
     * getExistingCRMRelationships
     *
     * Returns the relationships between a given meeting bean and users/contacts.
     *
     * @param $bean
     * @return array
     */
    protected function getExistingCRMRelationships($bean) {
//        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
//        $relationships = [];
//        if(!empty($bean->id)){
//            $sql = "select * from(
//                    SELECT mc.id, 'Contacts' AS participant_module, mc.meeting_id, mc.contact_id AS participant_id, mc.accept_status, mc.date_modified, ea.email_address, CONCAT(c.first_name, ' ', c.last_name) AS name
//                    FROM meetings_contacts mc JOIN email_addr_bean_rel eabr ON eabr.bean_id=mc.contact_id
//                    JOIN email_addresses ea ON ea.id=eabr.email_address_id
//                    JOIN contacts c ON c.id=mc.contact_id
//                    WHERE mc.meeting_id='" . $bean->id . "' AND mc.deleted=0 AND ea.deleted=0 AND c.deleted=0 AND eabr.deleted=0
//                    UNION
//                    SELECT mu.id, 'Users' AS participant_module,mu.meeting_id, mu.user_id AS participant_id, mu.accept_status, mu.date_modified, ea.email_address, CONCAT(u.first_name, ' ', u.last_name) AS name
//                    FROM meetings_users mu JOIN email_addr_bean_rel eabr ON eabr.bean_id=mu.user_id
//                    JOIN email_addresses ea ON ea.id=eabr.email_address_id
//                    JOIN users u ON u.id=mu.user_id
//                    WHERE mu.meeting_id='" . $bean->id . "' AND mu.user_id != '" . $bean->assigned_user_id. "' AND mu.deleted=0 AND ea.deleted=0 AND u.deleted=0 AND eabr.deleted=0
//                    ) as participants
//                    GROUP BY participant_id";
//            $result = $db->query($sql);
//
//            while ($item = $db->fetchRow($result)) {
//                $relationships[] = [
//                    'id'             => $item['id'],
//                    'event_id'       => $item['meeting_id'],
//                    'participant_id' => $item['participant_id'],
//                    'participant_module' => $item['participant_module'],
//                    'accept_status'  => $item['accept_status'],
//                    'date_modified'  => $item['date_modified'],
//                    'email_address'  => $item['email_address'],
//                    'name'           => $item['name'],
//                ];
//            }
//        }
//        return $relationships;

        $participants = [];

        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['participant_policy'] == 'all') {
            // check contacts
            $contacts = $bean->get_linked_beans('contacts', 'Contact');
            foreach ($contacts as $contact) {
                $participants[] = $contact;
            }
        }

        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['participant_policy'] == 'all'
            || SpiceConfig::getInstance()->config['SpiceCRMExchange']['participant_policy'] == 'users_only') {
            // check users
            $users = $bean->get_linked_beans('users', 'User');
            foreach ($users as $user) {
                if ($user->id != $bean->assigned_user_id) {
                    $participants[] = $user;
                }
            }
        }

        return $participants;
    }

    /**
     * addRelationship
     *
     * Adds a relationship between a meeting bean and user/contact.
     *
     * @param $participant EWSObject
     * @param $participantBean SugarBean: the contact or user
     * @param $bean SugarBean: the meeting
     */
    protected function addRelationship($participant, $participantBean, $bean) {
        $acceptStatus = $this->mapParticipantResponseTypeEWSToBean($participant->ResponseType);

        $rel_link = 'contacts';
        if (get_class($participantBean) == 'User') {
            $rel_link = 'users';
        }

        if($bean->load_relationship($rel_link)){
            $additionalValues = ['required' => 1, 'accept_status' => $acceptStatus];
            $bean->{$rel_link}->add($participantBean, $additionalValues);
        }
    }

    /**
     * getRelationshipId
     *
     * Returns the ID of the relationship between the given meeting and user/contact beans.
     *
     * @param $participantBean
     * @param $bean
     * @return bool|mixed
     */
    protected function getRelationshipId($participantBean, $bean) {
//        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
//        if (get_class($participantBean) == 'Contact') {
//            $sql = "SELECT * FROM meetings_contacts WHERE contact_id='" . $participantBean->id . "'
//                    AND meeting_id='" . $bean->id ."' AND deleted=0";
//        } elseif (get_class($participantBean) == 'User') {
//            $sql = "SELECT * FROM meetings_users WHERE user_id='" . $participantBean->id . "'
//                    AND meeting_id='" . $bean->id ."' AND deleted=0";
//        }
//        $result = $db->query($sql);
//        $row = $db->fetchByAssoc($result);

        $row = [];
        $rel_link = 'contacts';
        if($participantBean->object_name == 'User'){
            $rel_link = 'users';
        }

        // load relationship rows
        if($bean->load_relationship($rel_link)){
            $result = $bean->{$rel_link}->relationship->load($bean->{$rel_link});

            foreach($result['rows'] as $participantId => $participantRel){
                if($participantRel['id'] == $participantBean->id){
                    $row['id'] = $participantRel['relid'];
                }
            }
        }

        return $row['id'] ? $row['id'] : false;
    }

    /**
     * mapBeanToEWS
     *
     * Maps a Sugar bean into an exchange object.
     *
     * @param $exchangeMeeting
     * @throws Exception
     */
    protected function mapBeanToEWS($exchangeMeeting) {
        parent::mapBeanToEWS($exchangeMeeting);

        $exchangeMeeting->Categories = new ArrayOfStringsType();
        $exchangeMeeting->Categories->String = $this->getEventCategories();
    }

    /**
     * @param $responseObject
     * @return bool|resource
     */
    protected function setSyncedUserData($responseObject) {
        // todo check if an entry for this meeting/user pair already exist to avoid duplicates
        global $timedate;
$db = DBManagerFactory::getInstance();

        // get now for the DB
        $dbNow = $timedate->nowDb();

        // encode the response object
        $extData = json_encode($responseObject);


        // check if we have an entry already
        $data = $db->fetchByAssoc($db->query("SELECT id FROM $this->pivotTableName WHERE " . $this->pivotBeanId . " = '{$this->spiceBean->id}' AND user_id = '{$this->user->id}' AND deleted=0"));
        $pivot_id = $data['id'];

        // populate join table
        if($extData) {
            if (!empty($pivot_id)) { // update meetings_users
                return $db->query("UPDATE " . $this->pivotTableName .
                    " SET external_data = '$extData', date_modified = '$dbNow' WHERE id='{$pivot_id}'");
            } else {
                // insert into meetings_users
                return $db->query("INSERT INTO " . $this->pivotTableName .
                    " (id, " . $this->pivotBeanId . ", user_id, external_data, date_modified, deleted) VALUES ('" .
                    create_guid() . "', '{$this->spiceBean->id}', '{$this->user->id}', '$extData', '$dbNow', 0 )");

            }
        }

    }

    /**
     * createUpdateArray
     *
     * Creates an array with the necessary EWS objects for the meeting update request.
     *
     * @return array
     * @throws Exception
     */
    protected function createUpdateArray()
    {
        $retArray = parent::createUpdateArray();
        return $retArray;
    }




    /**
     * removeRelationship
     *
     * Removes a meeting to user/contact relationship.
     *
     * @param $relationship SugarBean contact or user
     * @param $bean SugarBean meeting or call
     */
    protected function removeRelationship($relationship, $bean)
    {
//        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
//        //$sql = "DELETE FROM meetings_contacts WHERE id='" . $relationship['id'] . "'";
//        $sql = "UPDATE meetings_contacts SET deleted='1' WHERE id='{$relationship['id']}'";
//        $db->query($sql);
//        //$sql = "DELETE FROM meetings_users WHERE id='" . $relationship['id'] . "'";
//        $sql = "UPDATE meetings_users SET deleted='1' WHERE='{$relationship['id']}'";
//        $db->query($sql);

        $rel_link = 'contacts';
        if($relationship->object_name == 'User') {
            $rel_link = 'users';
        }
        if($bean->load_relationship($rel_link)) {
            $bean->{$rel_link}->delete($bean->id, $relationship);
        }
    }
}
