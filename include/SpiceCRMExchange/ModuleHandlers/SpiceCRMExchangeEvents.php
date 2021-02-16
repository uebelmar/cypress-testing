<?php
namespace SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers;

use Exception;
use jamesiarmes\PhpEws\ArrayType\ArrayOfStringsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAttendeesType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfItemChangeDescriptionsType;
use jamesiarmes\PhpEws\Enumeration\CalendarItemCreateOrDeleteOperationType;
use jamesiarmes\PhpEws\Enumeration\CalendarItemUpdateOperationType;
use jamesiarmes\PhpEws\Enumeration\ConflictResolutionType;
use jamesiarmes\PhpEws\Enumeration\DisposalType;
use jamesiarmes\PhpEws\Enumeration\MapiPropertyTypeType;
use jamesiarmes\PhpEws\Enumeration\MessageDispositionType;
use jamesiarmes\PhpEws\Enumeration\RoutingType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\Request\DeleteItemType;
use jamesiarmes\PhpEws\Request\UpdateItemType;
use jamesiarmes\PhpEws\Type\AcceptItemType;
use jamesiarmes\PhpEws\Type\AttendeeType;
use jamesiarmes\PhpEws\Type\CalendarItemType;
use jamesiarmes\PhpEws\Type\EmailAddressType;
use jamesiarmes\PhpEws\Type\ExtendedPropertyType;
use jamesiarmes\PhpEws\Type\ItemChangeType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\ItemType;
use jamesiarmes\PhpEws\Type\PathToExtendedFieldType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\SetItemFieldType;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\EwsConnectionException;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeClient;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeFieldMappingEvent;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeLogger;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeConnector;
use SpiceCRM\data\BeanFactory;
use DateTime;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\SysModuleFilters\SysModuleFilters;
use SpiceCRM\includes\authentication\AuthenticationController;
use SugarBean;

abstract class SpiceCRMExchangeEvents extends SpiceCRMExchangeBeans
{
    public function __construct($user, &$bean = null) {
        if($bean){
            // The User shall be the assigned user for meetings, calls
            // this way users who have writing access permission for the bean may edit and save the bean without error being thrown by exchange
            if($user->id != $bean->assigned_user_id){
                $user = BeanFactory::getBean('Users', $bean->assigned_user_id, ['encode' => false] );
            }
            parent::__construct($user, $bean);
        } else {
            parent::__construct($user, $this->moduleName);
        }
    }

    /**
     * participant policy may be set for Calls or Meetings
     * overwrite the value of general setting
     * @return void
     */
    public function overwriteParticipantsPolicy(){
        if(isset(SpiceConfig::getInstance()->config[$this->spiceBean->module_name]['participant_policy'])){
            SpiceConfig::getInstance()->config['SpiceCRMExchange']['participant_policy'] = SpiceConfig::getInstance()->config['SpiceCRMExchange'][$this->spiceBean->module_name]['participant_policy'];
        }
    }

    /**
     * getSpiceCRMId
     *
     * Returns the id of the meeting for the given EWS ID.
     *
     * @param $itemid
     * @return bool|mixed
     */
    public function getSpiceCRMId($itemid) {
        $db = DBManagerFactory::getInstance();
        $sugarId = $db->fetchByAssoc($db->query("SELECT id FROM " . $this->tableName. " WHERE external_id='$itemid'"));
        return $sugarId ? $sugarId['id'] : false;
    }

    /**
     * todo probably not needed anymore
     *
     * @return SpiceCRMExchangeClient
     */
    public function getClient(): SpiceCRMExchangeClient {
        return $this->connector->client;
    }

    /**
     * Creates an event (meeting/call) on Exchange for the given user.
     *
     * @return mixed
     * @throws Exception
     */
    public function createOnExchange() {
//        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_CREATE);

        // Build the request,
        $request = new CreateItemType();
// not necessary. Exchange will handle any date start change of the event. Let updateParticipants deal with invitations
//        $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;
        $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;
        $request->Items = new NonEmptyArrayOfAllItemsType();

        $exchangeEvent = new CalendarItemType();
        $exchangeEvent->RequiredAttendees = new NonEmptyArrayOfAttendeesType();

        $this->mapBeanToEWS($exchangeEvent);

        // Add the event to the request. You could add multiple events to create more
        // than one in a single request.
        $request->Items->CalendarItem[] = $exchangeEvent;

        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_CREATE, print_r($request, true));

        $response = $this->connector->client->request('CreateItem', $request);
        $externalData = $this->generateExternalData(
            $response->ResponseMessages->CreateItemResponseMessage[0]->Items->CalendarItem[0]->ItemId
        );
        $this->spiceBean->external_id   = $externalData['Id'];
        $this->spiceBean->lastSynced    = $externalData['LastSynced'];
        $this->spiceBean->external_data = json_encode($externalData);

        $this->logger->logOutboundRecord(
            $this->spiceBean,
            __CLASS__.'::'.__FUNCTION__.'() '.$response->ResponseMessages->CreateItemResponseMessage[0]->ResponseCode,
            $response->ResponseMessages->CreateItemResponseMessage[0]->MessageText
        );

        return $response;
    }

    /**
     * updateOnExchange
     *
     * Updates an event(meeting/call) on the Exchange server.
     *
     * @return mixed
     * @throws Exception
     */
    public function updateOnExchange() {
        
//        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_UPDATE);

        $request = new UpdateItemType();
        $request->ConflictResolution = ConflictResolutionType::ALWAYS_OVERWRITE;
// not necessary. Exchange will handle any date start change of the event. Let updateParticipants deal with invitations
//        if (\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['SpiceCRMExchange']['save_invitation_copy']) {
//            $request->SendMeetingInvitationsOrCancellations = CalendarItemUpdateOperationType::SEND_TO_ALL_AND_SAVE_COPY;
//        } else {
//            // set SEND_ONLY_TO_ALL to ensure invited participants get information in exchange
//            $request->SendMeetingInvitationsOrCancellations = CalendarItemUpdateOperationType::SEND_ONLY_TO_ALL;
//        }
        $request->SendMeetingInvitationsOrCancellations = CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;

        $change = new ItemChangeType();
        $change->ItemId = new ItemIdType();
        $change->ItemId->Id = $this->getExternalId();
        $change->ItemId->ChangeKey = $this->getChangeKey();
        $change->Updates = new NonEmptyArrayOfItemChangeDescriptionsType();
        $change->Updates->SetItemField = $this->createUpdateArray();

        // make sure data contained in external_data is accurate
        if($external_data = json_decode($this->spiceBean->external_data, true)){
            $updateExternalData = false;
            foreach($external_data as $extendedKey => $extendedValue){
                if(property_exists($this->spiceBean, $extendedKey)){
                    $external_data[$extendedKey] = $this->spiceBean->{$extendedKey};
                    $updateExternalData = true;
                }
            }
            if($updateExternalData){
                $this->spiceBean->external_data = json_encode($external_data);
            }
        }
        $externalData = json_decode($this->spiceBean->external_data);

        $property = new ExtendedPropertyType();
        $property->ExtendedFieldURI = new PathToExtendedFieldType();
        $property->ExtendedFieldURI->PropertyType = MapiPropertyTypeType::STRING;
        $property->ExtendedFieldURI->PropertySetId = '00020329-0000-0000-C000-000000000046';
        $property->ExtendedFieldURI->PropertyName = SpiceCRMExchangeConnector::getExtendedFieldName();
        // todo save the extended properties somewhere and just send them back
        $property->Value = json_encode($externalData->ExtendedProperties) ?? '{"_id":"' . $this->spiceBean->id . '","_module":"' . $this->moduleName . '"}';
        $event = new ItemType();
        $event->ExtendedProperty = $property;

        $field = new SetItemFieldType();
        $field->ExtendedFieldURI = new PathToExtendedFieldType();
        $field->ExtendedFieldURI->PropertyType = MapiPropertyTypeType::STRING;
        $field->ExtendedFieldURI->PropertySetId = '00020329-0000-0000-C000-000000000046';
        $field->ExtendedFieldURI->PropertyName = SpiceCRMExchangeConnector::getExtendedFieldName();
        $field->CalendarItem = $event;

        $change->Updates->SetItemField[] = $field;

        $request->ItemChanges[] = $change;
        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_UPDATE, print_r($request, true));
        $response = $this->connector->client->request('UpdateItem', $request);

        $itemId = $response->ResponseMessages->UpdateItemResponseMessage[0]->Items->CalendarItem[0]->ItemId;


        $externalData = $this->generateExternalData(
            $itemId
        );
        $this->spiceBean->lastSynced    = $externalData['LastSynced'];
        $this->spiceBean->external_data = json_encode($externalData);

        $this->logger->logOutboundRecord(
            $this->spiceBean,
            __CLASS__.'::'.__FUNCTION__.'() '.$response->ResponseMessages->UpdateItemResponseMessage[0]->ResponseCode,
            $response->ResponseMessages->UpdateItemResponseMessage[0]->MessageText
        );

        return $response;
    }

    /**
     * set status of invitation in exchange
     * triggered in setStatus in Meetings KREST controller
     * but it does not work..... access denied...
     * @param $bean
     * @param $status
     * @throws EwsConnectionException
     */
    public function setInvitationStatusOnExchange($bean, $status){
        // check itemType name
        switch($status) {
            case 'accept':
                // Create the request.
                $request = new CreateItemType();

                // Set the message disposition on the request.
                $request->MessageDisposition = MessageDispositionType::SEND_ONLY;

                // Create the AcceptItem response object.
                $acceptItem = new AcceptItemType();

                // Identify the meeting request to accept.
                $acceptItem->ReferenceItemId = new ItemIdType();
                $acceptItem->ReferenceItemId->Id = $this->getExternalId();
                $acceptItem->ReferenceItemId->ChangeKey = $this->getChangeKey();


                // Add the AcceptItem response object to the request.
                $request->Items = new NonEmptyArrayOfAllItemsType();
                $request->Items->AcceptItem[] = $acceptItem;

                // Send the request and get the response.
                $response = $this->connector->client->request('CreateItem', $request);
                $this->logger->logOutboundRecord(
                    $bean,
                    __CLASS__.'::'.__FUNCTION__.'() ',
                    'request = '.print_r($request, true)
                );

                $this->logger->logOutboundRecord(
                    $bean,
                    __CLASS__.'::'.__FUNCTION__.'() ',
                    'response = '.print_r($response->ResponseMessages->CreateItemResponseMessage[0], true)
                );

                break;
            case 'decline':
                $obj = new DeclineItemType();
                break;
            case 'tentative':
                $obj = new TentativelyItemType();
                break;
        }

    }

    /**
     * getDeleteRequest
     *
     * Creates an EWS request for deleting an event(meeting/call) from the Exchange server.
     * It just returns the request object and doesn't send it to EWS.
     *
     * @return DeleteItemType
     */
    public function getDeleteRequest() {

        $ewsData = json_decode(html_entity_decode($this->spiceBean->external_data));

        $request             = new DeleteItemType();
        $request->DeleteType = DisposalType::HARD_DELETE;
        $request->ItemIds->ItemId[0] = new ItemIdType();
        $request->ItemIds->ItemId[0]->Id = $ewsData->Id;
        $request->ItemIds->ItemId[0]->ChangeKey = $ewsData->ChangeKey;

        $request = $this->addToDeleteRequest($request);

        return $request;
    }

    /**
     * Adds and removes an event participants on the Exchange server.
     * Used after create and update.
     *
     * @return mixed
     * @throws EwsConnectionException
     */
    public function updateParticipants() {
//        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_UPDATE_PARTICIPANTS);

        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $ewsAttendees = $this->getEwsAttendees();
        $guests = $this->getExistingCRMRelationships($this->spiceBean);
        $attendeeList = $this->compareAttendeeLists($ewsAttendees, $guests);

        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_UPDATE_PARTICIPANTS, 'attendeeList => '.print_r($attendeeList, true));

        if ($GLOBALS['isKRESTExchange']) {
            $this->updateParticipantsInSpice($attendeeList);
        } else {
            $this->updateParticipantsOnExchange($attendeeList);
        }
    }

    /**
     * getExternalId
     *
     * Returns the EWS ID for the current bean.
     *
     * @return mixed
     */
    public function getExternalId() {
        if(empty($this->spiceBean->external_id)){
            if($externalData = json_decode(html_entity_decode($this->spiceBean->external_data))){
                $this->spiceBean->external_id = $externalData->Id;
            }
        }
        return $this->spiceBean->external_id;
    }

    /**
     *
     * @param $exchangeObject
     */
    protected function mapBeanToEWS($exchangeObject) {
//        $start = new DateTime($this->spiceBean->date_start, new DateTimeZone('UTC'));
//        $end = new DateTime($this->spiceBean->date_end, new DateTimeZone('UTC'));
//        $exchangeObject->Start = $start->format('c');
//        $exchangeObject->End = $end->format('c');
//        $exchangeObject->Subject = $this->spiceBean->name;
//
//        // Set the event body.
//        $exchangeObject->Body = new BodyType();
//        $exchangeObject->Body->_ = $this->spiceBean->description;
//        $exchangeObject->Body->BodyType = BodyTypeType::TEXT;

        // get mapping
        $fields = $this->createCreateArray();

        // enrich exchange object
        foreach($fields as $property => $value){
            $exchangeObject->$property = $value;
        }

        $property = new ExtendedPropertyType();
        $property->ExtendedFieldURI = new PathToExtendedFieldType();
        $property->ExtendedFieldURI->PropertyType = MapiPropertyTypeType::STRING;
        $property->ExtendedFieldURI->PropertySetId = '00020329-0000-0000-C000-000000000046';
        $property->ExtendedFieldURI->PropertyName = SpiceCRMExchangeConnector::getExtendedFieldName();
        $property->Value = '{"_id":"' . $this->spiceBean->id . '","_module":"' . $this->moduleName . '"}';
        $exchangeObject->ExtendedProperty = $property;
    }

    /**
     *  createCreateArray
     *
     * Creates an array with the necessary EWS objects for the event create request.
     * @return array
     */
    protected function createCreateArray()
    {
        $mapping = new SpiceCRMExchangeFieldMappingEvent($this->spiceBean);
        $retArray =  $mapping->createCreateArray();

        // add Categories
        $retArray['Categories'] = $this->generateCategories();

        return $retArray;
    }

    /**
     * createUpdateArray
     *
     * Creates an array with the necessary EWS objects for the event update request.
     *
     * @return array
     * @throws Exception
     */
    protected function createUpdateArray()
    {
        $mapping = new SpiceCRMExchangeFieldMappingEvent($this->spiceBean);
        $retArray = $mapping->createUpdateArray();

        // add Categories
        $retArray[] = $this->generateCategoriesField();

        return $retArray;
    }

    /**
     * generateCategoriesField
     *
     * Generates the set item object for event categories.
     *
     * @return SetItemFieldType
     */
    public function generateCategoriesField() {
        $field = new SetItemFieldType();
        $field->FieldURI = new PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_CATEGORIES;
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->Categories = $this->generateCategories();

        return $field;
    }

    /**
     * @return ArrayOfStringsType
     */
    public function generateCategories() {
        $propertyCategories = new ArrayOfStringsType();
        $propertyCategories->String = $this->getEventCategories();
        return $propertyCategories;
    }

    /**
     * @param $request
     * @return mixed
     */
    protected function addToDeleteRequest($request) {
        $request->SendMeetingCancellations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;

        return $request;
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
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('Mapping EWS to Bean.');

        if (!empty($exchangeObject->ExtendedProperty)) {
            $extendedFields = json_decode($exchangeObject->ExtendedProperty[0]->Value);

            if (isset($extendedFields->_id)) {
                // todo exception if bean not found
                $bean = BeanFactory::getBean($this->moduleName);
                if ($bean->retrieve($extendedFields->_id) == false) {
                    $bean->id = $extendedFields->_id;
                    $bean->new_with_id = true;
                }
            } elseif (isset($exchangeObject->ItemId->Id)) {
                $this->needsUpdate = true;
                $bean = BeanFactory::getBean($this->moduleName);
                if(!$bean->retrieve_by_string_fields(['external_id' => $exchangeObject->ItemId->Id])) {
                    $bean = BeanFactory::getBean($this->moduleName);
                }
            }
        } else {
            $this->needsUpdate = true;
            $bean = BeanFactory::getBean($this->moduleName);
        }

        $this->logger->logInboundRecord(
            $bean->external_id,
            $bean->id,
            get_class($bean),
            __CLASS__.'::'.__FUNCTION__.'() '.print_r($exchangeObject, true)
        );

        $this->spiceBean = $bean;

        $bean->external_id = $exchangeObject->ItemId->Id;
        $externalData = [];
        $externalData['ChangeKey'] = $exchangeObject->ItemId->ChangeKey;
        $externalData['Id'] = $exchangeObject->ItemId->Id;
        $externalData['ExtendedProperties'] = json_decode($exchangeObject->ExtendedProperty[0]->Value);
        if (!isset($externalData['ExtendedProperties']->_id)) {
            if(empty($bean->id)){
                $bean->id = create_guid();
                $bean->new_with_id = true;
            }
            $externalData['ExtendedProperties']->_id = $bean->id;
        }
        $bean->external_data = json_encode($externalData);

        // get mapping
        $mapping = new SpiceCRMExchangeFieldMappingEvent($bean);
        $fields = $mapping->createArrayEWSToBean();

        // set bean property values from exchange object
        foreach ($fields as $key => $value) {
            switch ($value['type']) {
                case 'datetime': // will be handle manually below
                    break;
                case 'body':
                    $bean->{$value['beanField']} = $exchangeObject->Body->_;
                    break;
                case 'reminder':
                    $bean->{$value['beanField']} = $exchangeObject->ReminderMinutesBeforeStart * 60;
                    $bean->reminder_checked = $exchangeObject->ReminderIsSet;
                    if(!$bean->reminder_checked) {
                        $bean->{$value['beanField']} = 0;
                    }
                    break;
                default:
                    if($value['itemField']){
                        $bean->{$value['beanField']} = $exchangeObject->{$value['itemField']};
                    }
            }

            // apply customFunction to value if defined so
            if(isset($value['customFunction'])){
                $bean->{$value['beanField']} = $mapping->applyCustomFunctionToBean($bean, $value['customFunction'], 'in');
            }
        }


//        $bean->name = $exchangeObject->Subject;
        $start = new DateTime($exchangeObject->Start);
        $end = new DateTime($exchangeObject->End);
        $bean->date_start = $start->format('Y-m-d H:i:s');
        $bean->date_end = $end->format('Y-m-d H:i:s');

        // calculate the duration
        $duration = $start->diff($end);
        $bean->duration_hours = $duration->h;
        $bean->duration_minutes = $duration->i;

//        $bean->description = $exchangeObject->Body->_;

        if (!empty($exchangeObject->ExtendedProperty)) {
            $extendedFields = json_decode($exchangeObject->ExtendedProperty[0]->Value);

            foreach ($extendedFields as $extendedFieldName => $extendedFieldValue) {
                if (substr($extendedFieldName, 0, 1) != '_') {
                    $bean->$extendedFieldName = $extendedFieldValue;
                }
            }
        }

        // do not send empty Attendee array
        if($exchangeObject->RequiredAttendees->Attendee) {
            $this->syncParticipants($exchangeObject->RequiredAttendees->Attendee, $bean);
        }

        return $bean;
    }

    /**
     * syncParticipants
     *
     * Synchronizes the participants from the EWS meeting object
     *
     * @param $participants
     * @param SugarBean $bean
     */
    protected function syncParticipants($participants, $bean) {
        $this->logger->logInboundRecord(
            $bean->external_id,
            $bean->id,
            get_class($bean),
            'Synchronizing participants '.__FUNCTION__.'() '.print_r($participants, true)
        );
        if(!empty($bean->id)) {
            foreach ($participants as $participant) {
                $participantBean = $this->findParticipantByEmailAddress($participant->Mailbox->EmailAddress);
                if (!$participantBean) {
                    // if none found just ignore
                    continue;
                }

                // Do addRelationship.
                // If relationship already exists, additional parameter for accept status will be set
                // If relationship does not exist, it will be added including accept status
                $this->addRelationship($participant, $participantBean, $bean);

            }

            foreach ($this->getExistingCRMRelationships($bean) as $relationship) {
                if (!$this->isRelationshipInExchange($relationship, $participants)) {
                    $this->removeRelationship($relationship, $bean);
                }
            }
        }
    }

    /**
     * findParticipantByEmailAddress
     *
     * Searches for event participants (Users/Contacts) using the email address from Exchange.
     *
     * @param $emailAddress
     * @return bool|SugarBean
     */
    protected function findParticipantByEmailAddress($emailAddress) {
        
$db = DBManagerFactory::getInstance();

        $sql = "SELECT u.id, eabr.bean_module FROM users u JOIN email_addr_bean_rel eabr ON u.id=eabr.bean_id
                JOIN email_addresses ea ON ea.id=eabr.email_address_id
                WHERE eabr.bean_module='Users' AND eabr.deleted=0
                AND ea.email_address_caps='" . strtoupper($emailAddress) . "'
                AND eabr.primary_address=1";

        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['participant_policy'] == 'all') {
            $sql .= " UNION 
                SELECT c.id, eabr.bean_module FROM contacts c JOIN email_addr_bean_rel eabr ON c.id=eabr.bean_id
                JOIN email_addresses ea ON ea.id=eabr.email_address_id
                WHERE eabr.bean_module='Contacts' AND eabr.deleted=0
                AND ea.email_address_caps='" . strtoupper($emailAddress) . "'
                AND eabr.primary_address=1";
        }

        $result = $db->query($sql);
        $row = $db->fetchRow($result); // $db->fetchByAssoc($result);
        if (isset($row['id'])) {
            $participant = BeanFactory::getBean($row['bean_module'], $row['id']);
            return $participant;
        }

        return false;
    }

    /**
     * isRelationshipInExchange
     *
     * Checks if a given meeting to user/contact relationship exists as a meeting participant on EWS.
     *
     * @param $relationship
     * @param $participants
     * @return bool
     */
    protected function isRelationshipInExchange($relationship, $participants) {
        foreach ($participants as $participant) {
            if (strtolower($participant->Mailbox->EmailAddress) == strtolower($relationship->email1)) {
                return true;
            }
        }

        return false;
    }

    /**
     * getEventCategories
     *
     * Returns an array with all the categories for the given even.
     * @return array
     */
    public function getEventCategories() {
        $db = DBManagerFactory::getInstance();

        $query = "SELECT * FROM sysuicalendarcolorconditions WHERE module = '{$this->spiceBean->module_dir}' ORDER BY priority";
        $queryRes = $db->query($query);

        if (!$queryRes) return [];

        $categoryArray = [];

        $moduleFilter = new SysModuleFilters();

        // push the category if the filter match or undefined
        while ($row = $db->fetchByAssoc($queryRes)) {
            if (isset($row['module_filter']) && strlen($row['module_filter']) != '') {
                if ($moduleFilter->checkBeanForFilterIdMatch($row['module_filter'], $this->spiceBean)) {
                    $categoryArray[] = $row['category'];
                }
            } else {
                $categoryArray[] = $row['category'];
            }
        }

        return $categoryArray;
    }

    /**
     * Generates the contents for the external_data field in a bean.
     *
     * @param ItemIdType $itemId
     * @return array
     */
    protected function generateExternalData(ItemIdType $itemId) {
        global $timedate;
        $externalData = [];
        $externalData['ChangeKey'] = $itemId->ChangeKey;
        $externalData['Id'] = $itemId->Id;
        $externalData['LastSynced'] = $timedate->nowDb();

        return $externalData;
    }

    /**
     * Fetches the attendee list from ews.
     *
     * @return array
     */
    protected function getEwsAttendees() {
        $ewsAttendees = [];

        $externalData = json_decode($this->spiceBean->external_data);
        $ewsItem = $this->connector->getItem($externalData->Id, $externalData->ChangeKey);

        foreach ($ewsItem->RequiredAttendees->Attendee as $attendee) {
            $ewsAttendees[] = $attendee->Mailbox->EmailAddress;
        }

        return $ewsAttendees;
    }

    /**
     * Creates a comparison list with information on which participants is present in which system (spice/ews)
     * and what action should be taken.
     *
     * @param $ewsAttendees
     * @param $spiceAttendees
     * @return array
     */
    protected function compareAttendeeLists($ewsAttendees, $spiceAttendees) {
        $attendeeList = [];

        foreach ($ewsAttendees as $ewsAttendee) {
            $attendeeList[$ewsAttendee]['ews'] = true;
        }

        foreach ($spiceAttendees as $spiceAttendee) {
            $attendeeList[$spiceAttendee->email1]['spice'] = true;
            $attendeeList[$spiceAttendee->email1]['name'] = (empty($spiceAttendee->full_name) ? $spiceAttendee->email1 : $spiceAttendee->full_name);
        }

        foreach ($attendeeList as $email => $attendee) {
            $attendeeList[$email]['action'] = $this->determineAttendeeAction($attendee);
        }

        return $attendeeList;
    }

    /**
     * Determines if a participant should be added/deleted from spice or ews.
     *
     * @param $attendee
     * @return string
     */
    protected function determineAttendeeAction($attendee) {
        if ($attendee['ews'] && $attendee['spice']) {
            // attendee in both systems. no action needed
            return 'none';
        }

        if ($attendee['ews'] && !$attendee['spice']) {
            if ($GLOBALS['isKRESTExchange']) {
                // attendee missing in spice and the hook was triggered by ews. attendee needs to be saved in spice.
                return 'savetospice';
            }
            // attendee missing in spice but present in ews. the hook was triggered by spice. needs deleting from ews.
            return 'deletefromews';
        }

        if (!$attendee['ews'] && $attendee['spice']) {
            if ($GLOBALS['isKRESTExchange']) {
                // attendee missing in ews and the hook was triggered by ews. needs deleting from spice.
                return 'deletefromspice';
            }
            // attendee missing in ews and the hook was triggered by spice. needs saving in ews.
            return 'savetoews';
        }

        if (!$attendee['ews'] && !$attendee['spice']) {
            // attendee in neither system. you shouldn't be here. please go away.
            return 'goaway';
        }

        return 'none';
    }

    /**
     * Updates the participants in exchange.
     *
     * @param $participants
     * @return mixed
     * @throws EwsConnectionException
     */
    private function updateParticipantsOnExchange($participants) {
        
        $purgeParticipants = false;
        $request = new UpdateItemType();
        $request->ConflictResolution = ConflictResolutionType::ALWAYS_OVERWRITE;
        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['save_invitation_copy']) {
            $request->SendMeetingInvitationsOrCancellations = CalendarItemUpdateOperationType::SEND_TO_ALL_AND_SAVE_COPY;
        } else {
            // set SEND_ONLY_TO_ALL to ensure invited participants get information in exchange
            $request->SendMeetingInvitationsOrCancellations = CalendarItemUpdateOperationType::SEND_ONLY_TO_ALL;
        }

        $change = new ItemChangeType();
        $change->ItemId = new ItemIdType();
        $change->ItemId->Id = $this->getExternalId();
        $change->ItemId->ChangeKey = $this->getChangeKey();

        // prepare attendees array
        $fieldSet = new SetItemFieldType();
        $fieldSet->FieldURI->FieldURI = UnindexedFieldURIType::CALENDAR_REQUIRED_ATTENDEES;
        $someAttendees = false; // store if any attendee is found

        // add participants from spice
        foreach ($participants as $email => $participant) {
            if ($participant['spice']) {
                $someAttendees = true;
                // send all participants from spice (not just the new ones)
                $attendee = new AttendeeType();
                $attendee->Mailbox = new EmailAddressType();
                $attendee->Mailbox->EmailAddress = $email;
                $attendee->Mailbox->Name = $participant['name'];
                $attendee->Mailbox->RoutingType = RoutingType::SMTP;

                $fieldSet->CalendarItem->RequiredAttendees->Attendee[] = $attendee;
            }

            if ($participant['action'] == 'deletefromews') {
                $purgeParticipants = true;
            }
        }

        // send attendees array only if any attendee was found
        if($someAttendees) {
            $change->Updates->SetItemField[] = $fieldSet;
        }

        $request->ItemChanges[] = $change;

        // remove any marked deleted participant in exchange
        if ($purgeParticipants) {
            $this->purgeParticipantsOnEws();
        }

        $response = $this->connector->client->request('UpdateItem', $request);

        $this->logger->logOutboundRecord(
            $this->spiceBean,
            __CLASS__.'::'.__FUNCTION__.'() '.$response->ResponseMessages->UpdateItemResponseMessage[0]->ResponseCode,
            $response->ResponseMessages->UpdateItemResponseMessage[0]->MessageText
        );

        return $response;
    }

    /**
     * Add/deletes the participants in spice.
     *
     * @param $participants
     */
    private function updateParticipantsInSpice($participants) {
        foreach ($participants as $email => $participant) {
            if($person = $this->findParticipantByEmailAddress($email)) {

                // handle link name
                $rel_link = 'contacts';
                if ($person->object_name == 'User') {
                    $rel_link = 'users';
                }

                // load relationship and process action
                if ($this->spiceBean->load_relationship($rel_link)) {
                    if ($participant['action'] == 'deletefromspice') {
                        $this->spiceBean->{$rel_link}->delete($this->spiceBean->id, $person);
                    }

                    if ($participant['action'] == 'savetospice') {
                        $this->spiceBean->{$rel_link}->add([$person]);
                    }
                }
            }
        }
    }

    /**
     * Deletes all event participants on exchange.
     * There is no API call to delete just a specific participant, so that's why such a drastic method is needed.
     *
     * @return mixed
     * @throws EwsConnectionException
     */
    private function purgeParticipantsOnEws() {
        
        $request = new UpdateItemType();
        $request->ConflictResolution = ConflictResolutionType::ALWAYS_OVERWRITE;
        if (SpiceConfig::getInstance()->config['SpiceCRMExchange']['save_invitation_copy']) {
            $request->SendMeetingInvitationsOrCancellations = CalendarItemUpdateOperationType::SEND_TO_ALL_AND_SAVE_COPY;
        } else {
            // set SEND_ONLY_TO_ALL to ensure invited participants get information in exchange
            $request->SendMeetingInvitationsOrCancellations = CalendarItemUpdateOperationType::SEND_ONLY_TO_ALL;
        }

        $change = new ItemChangeType();
        $change->ItemId = new ItemIdType();
        $change->ItemId->Id = $this->getExternalId();
        $change->ItemId->ChangeKey = $this->getChangeKey();

        $fieldDelete = new SetItemFieldType();
        $fieldDelete->FieldURI->FieldURI = UnindexedFieldURIType::CALENDAR_REQUIRED_ATTENDEES;

        $change->Updates->DeleteItemField = $fieldDelete;

        $request->ItemChanges[] = $change;

        $response = $this->connector->client->request('UpdateItem', $request);

        return $response;
    }

    /**
     * map ews values for participation status
     * Accept, Decline, NoResponseReceived, Organizer, Tentative, Unknown
     * @param $ewsResponseType
     * @return string
     */
    public function mapParticipantResponseTypeEWSToBean($ewsResponseType){
        switch($ewsResponseType) {
            case 'Accept':
                return 'accept';
            case 'Tentative':
                return 'tentative';
            case 'Decline':
                return 'decline';
            default:
                return 'none';
        }

    }

    abstract protected function getExistingCRMRelationships($bean);

    abstract protected function getRelationshipId($participantBean, $bean);

    abstract protected function addRelationship($participant, $participantBean, $bean);

    abstract protected function removeRelationship($relationship, $bean);

}
