<?php
namespace SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers;

use SpiceCRM\data\SugarBean;

use Exception;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class SpiceCRMExchangeCalls extends SpiceCRMExchangeEvents
{
    protected $moduleName     = 'Calls';
    protected $itemName       = 'CalendarItem';
    protected $tableName      = 'calls';
    protected $pivotTableName = 'calls_users';
    protected $pivotBeanId    = 'call_id';

//    protected $updateFieldMapping = [
//        'Subject' => [
//            'itemField' => 'Subject',
//            'beanField' => 'name',
//            'subtype'   => UnindexedFieldURIType::ITEM_SUBJECT,
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
     * getExistingCRMRelationships
     *
     * Returns the relationships between a given call bean and users/contacts.
     *
     * @param $bean
     * @return array
     */
    protected function getExistingCRMRelationships($bean) {
//        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
//        $relationships = [];
//        if(!empty($bean->id)) {
//            $sql = "select * from (
//                    SELECT cc.id, cc.call_id, cc.contact_id AS participant_id, cc.accept_status, cc.date_modified, ea.email_address, CONCAT(c.first_name, ' ', c.last_name) AS name
//                    FROM calls_contacts cc JOIN email_addr_bean_rel eabr ON eabr.bean_id=cc.contact_id
//                    JOIN email_addresses ea ON ea.id=eabr.email_address_id
//                    JOIN contacts c ON c.id=cc.contact_id
//                    WHERE cc.call_id='" . $bean->id . "' AND cc.deleted=0 AND ea.deleted=0 AND c.deleted=0 AND eabr.deleted=0
//                    UNION
//                    SELECT cu.id, cu.call_id, cu.user_id AS participant_id, cu.accept_status, cu.date_modified, ea.email_address, CONCAT(u.first_name, ' ', u.last_name) AS name
//                    FROM calls_users cu JOIN email_addr_bean_rel eabr ON eabr.bean_id=cu.user_id
//                    JOIN email_addresses ea ON ea.id=eabr.email_address_id
//                    JOIN users u ON u.id=cu.user_id
//                    WHERE cu.call_id='" . $bean->id . "' AND cu.user_id != '" . $bean->assigned_user_id . "' AND cu.deleted=0 AND ea.deleted=0 AND u.deleted=0 AND eabr.deleted=0
//                    ) participants
//                    GROUP BY participant_id";
//            $result = $db->query($sql);
//
//            while ($item = $db->fetchRow($result)) {
//                $relationships[] = [
//                    'id' => $item['id'],
//                    'event_id' => $item['call_id'],
//                    'participant_id' => $item['participant_id'],
//                    'accept_status' => $item['accept_status'],
//                    'date_modified' => $item['date_modified'],
//                    'email_address' => $item['email_address'],
//                    'name' => $item['name'],
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
     * getRelationshipId
     *
     * Returns the ID of the relationship between the given call and user/contact beans.
     *
     * @param $participantBean
     * @param $bean
     * @return bool|mixed
     */
    protected function getRelationshipId($participantBean, $bean) {
//        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
//        if (get_class($participantBean) == 'Contact') {
//            $sql = "SELECT * FROM calls_contacts WHERE contact_id='" . $participantBean->id . "'
//                    AND call_id='" . $bean->id ."' AND deleted=0";
//        } elseif (get_class($participantBean) == 'User') {
//            $sql = "SELECT * FROM calls_users WHERE user_id='" . $participantBean->id . "'
//                    AND call_id='" . $bean->id ."' AND deleted=0";
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
     * addRelationship
     *
     * Adds a relationship between a call bean and user/contact.
     *
     * @param $participant EWSObject
     * @param $participantBean SugarBean: the contact or user
     * @param $bean SugarBean: the call
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
     * removeRelationship
     *
     * Removes a call to user/contact relationship.
     *
     * @param $relationship
     */
    protected function removeRelationship($relationship, $bean) {
//        $db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
//        //$sql = "DELETE FROM calls_contacts WHERE id='" . $relationship['id'] . "'";
//        $sql = "UPDATE calls_contacts SET deleted='1' WHERE id='{$relationship['id']}'";
//        $db->query($sql);
//        //$sql = "DELETE FROM calls_users WHERE id='" . $relationship['id'] . "'";
//        $sql = "UPDATE calls_users SET deleted='1' WHERE id='{$relationship['id']}'";
//        $db->query($sql);

        $rel_link = 'contacts';
        if($relationship->object_name == 'User') {
            $rel_link = 'users';
        }
        if($bean->load_relationship($rel_link)) {
            $bean->{$rel_link}->delete($bean->id, $relationship);
        }
    }

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

        $this->spiceBean = $bean;

        return $bean;
    }

}
