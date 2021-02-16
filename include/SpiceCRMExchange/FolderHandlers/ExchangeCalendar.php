<?php
namespace SpiceCRM\includes\SpiceCRMExchange\FolderHandlers;

use DateTime;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfPathsToElementType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Enumeration\MapiPropertyTypeType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\Type\CalendarItemType;
use jamesiarmes\PhpEws\Type\CalendarViewType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Type\PathToExtendedFieldType;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\EwsConnectionException;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeConnector;
use SpiceCRM\includes\ErrorHandlers\Exception;

class ExchangeCalendar
{
    /**
     * The amount of sync queue items that are sent to exchange during one cron job run.
     */
    const SYNC_QUEUE_LIMIT = 500;

    public function __construct($user) {
        $this->user = $user;

        $this->connector = new SpiceCRMExchangeConnector($user);
    }

    /**
     * Returns the EWS calendar items for a specific timeframe excluding the ones that are already mapped to
     * existing beans.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     * @throws Exception
     * @throws EwsConnectionException
     */
    public function getEwsEvents(DateTime $startDate, DateTime $endDate) {
//        $this->connector->client->setTimezone($timezone);

        $request = new FindItemType();
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        $folder_id = new DistinguishedFolderIdType();
        $folder_id->Id = DistinguishedFolderIdNameType::CALENDAR;
        $request->ParentFolderIds->DistinguishedFolderId[] = $folder_id;

        $request->CalendarView = new CalendarViewType();
        $request->CalendarView->StartDate = $startDate->format('c');
        $request->CalendarView->EndDate = $endDate->format('c');

        $extendedField = new PathToExtendedFieldType();
        $extendedField->PropertyType = MapiPropertyTypeType::STRING;
        $extendedField->PropertySetId = '00020329-0000-0000-C000-000000000046';
        $extendedField->PropertyName = SpiceCRMExchangeConnector::getExtendedFieldName();
        $request->ItemShape->AdditionalProperties->ExtendedFieldURI = $extendedField;

        $response = $this->connector->client->request('FindItem', $request);

        $calendarItems = [];
        $response_messages = $response->ResponseMessages->FindItemResponseMessage;
        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                throw new Exception("Failed to search for events with \"$code: $message\"\n");
            }

            // Iterate over the events that were found, printing some data for each.
            $items = $response_message->RootFolder->Items->CalendarItem;
            foreach ($items as $item) {
                if($this->itemHasModule($item) === false) {
                    $calendarItems[] = $this->convertEwsItem($item);
                }
            }
        }

        return $calendarItems;
    }

    /**
     * Returns the EWS calendar items for a specific timeframe excluding the ones that are not mapped to
     * existing beans.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     * @throws Exception
     * @throws EwsConnectionException
     */
    public function getMissedEwsEvents(DateTime $startDate, DateTime $endDate) {
//        $this->connector->client->setTimezone($timezone);

        $request = new FindItemType();
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        $folder_id = new DistinguishedFolderIdType();
        $folder_id->Id = DistinguishedFolderIdNameType::CALENDAR;
        $request->ParentFolderIds->DistinguishedFolderId[] = $folder_id;

        $request->CalendarView = new CalendarViewType();
        // todo change to last modified date
        $request->CalendarView->StartDate = $startDate->format('c');
        $request->CalendarView->EndDate = $endDate->format('c');

        $extendedField = new PathToExtendedFieldType();
        $extendedField->PropertyType = MapiPropertyTypeType::STRING;
        $extendedField->PropertySetId = '00020329-0000-0000-C000-000000000046';
        $extendedField->PropertyName = SpiceCRMExchangeConnector::getExtendedFieldName();
        $request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType();
        $request->ItemShape->AdditionalProperties->ExtendedFieldURI = $extendedField;

        $response = $this->connector->client->request('FindItem', $request);

        $calendarItems = [];
        $response_messages = $response->ResponseMessages->FindItemResponseMessage;
        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                throw new Exception("Failed to search for events with \"$code: $message\"\n");
            }

            // Iterate over the events that were found, printing some data for each.
            $items = $response_message->RootFolder->Items->CalendarItem;
            foreach ($items as $item) {
                if($this->itemHasModule($item) === true) {
                    $calendarItems[] = $this->convertEwsItem($item);
                }
            }
        }

        return $calendarItems;
    }

    /**
     * Returns the IDs of the bean that will be queued for sync.
     *
     * @param $moduleName
     * @param $startDate
     * @param null $endDate
     * @return array
     */
    public function getBeansForQueue($moduleName, $startDate, $endDate = null) {
        $db = DBManagerFactory::getInstance();
//        $sql = "SELECT id FROM {$moduleName} WHERE assigned_user_id = '{$this->user->id}' date_start >= '{$startDate}'";
        $sql = "SELECT id FROM {$moduleName} WHERE assigned_user_id = 'seed_will_id' AND date_start >= '{$startDate}'";
        if ($endDate) {
            $sql .= " AND date_end <= {$endDate}";
        }
        $query = $db->query($sql);
        $results = [];
        while ($row = $db->fetchRow($query)) {
            $results[] = $row['id'];
        }

        return $results;
    }

    /**
     * Creates the entries for the bean sync queue.
     *
     * @param $moduleName
     * @param $beanList
     */
    public function fillUpSyncTable($moduleName, $beanList) {
        global $timedate;
$db = DBManagerFactory::getInstance();
        foreach ($beanList as $key => $beanId) {
            // todo check if it's already there
            $id = create_guid();
            $sql = "INSERT INTO sysgroupwarebeansyncqueue (id, bean_id, bean_type, user_id, date_entered)
                    VALUES ('{$id}', '{$beanId}', '{$moduleName}', '{$this->user->id}', {$timedate->nowDb()})";
            $db->query($sql);
        }
    }

    /**
     * Processes the sync queue by:
     * - instantiating the bean
     * - saving the bean which triggers the hooks
     * - the ews before_save hook sends the bean to the exchange server
     * - after succesfully sending it to ews the queue item is removed
     */
    public static function processSyncQueue() {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT * FROM sysgroupwarebeansyncqueue LIMIT " . self::SYNC_QUEUE_LIMIT;
        $query = $db->query($sql);
        while ($row = $db->fetchRow($query)) {
            $bean = BeanFactory::getBean($row['bean_type'], $row['bean_id'], ['encode' => false, 'relationships' => false]);
            $bean->retrieve($row['bean_id'], false, true, false);
            $bean->save(false);
            $bean->call_custom_logic('after_save_completed', '');
            if (isset($bean->lastSynced) && $bean->lastSynced != '') {
                self::removeQueueItem($row['id']);
            }
            // todo figure out what to do when it wasn't synced, so that the queue won't just fill up with unsyncable entires
        }
    }

    /**
     * Removes an item from the bean sync queue
     *
     * @param $itemId
     */
    private static function removeQueueItem($itemId) {
        $db = DBManagerFactory::getInstance();
        $sql = "DELETE FROM sysgroupwarebeansyncqueue WHERE id = '{$itemId}'";
        $db->query($sql);
    }

    /**
     * Checks if the EWS item has _module variable set in the extended properties.
     *
     * @param CalendarItemType $item
     * @return bool
     */
    private function itemHasModule(CalendarItemType $item): bool {
        if (!empty($item->ExtendedProperty)) {
            $extendedFields = json_decode($item->ExtendedProperty[0]->Value);

            if (isset($extendedFields->_module)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Removes the InstanceKey attribute of the EWS Item because it was malformed.
     * Needs to be reworked if the InstanceKey is ever needed for anything.
     *
     * @param CalendarItemType $item
     * @return CalendarItemType
     */
    private function convertEwsItem(CalendarItemType $item): CalendarItemType {
        $item->InstanceKey = null;

        return $item;
    }
}
