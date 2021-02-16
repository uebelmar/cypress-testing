<?php
namespace SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers;

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfItemChangeDescriptionsType;
use jamesiarmes\PhpEws\Enumeration\ConflictResolutionType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\Request\UpdateItemType;
use jamesiarmes\PhpEws\Type\ItemChangeType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\TaskType;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeFieldMappingTask;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeLogger;
use SpiceCRM\includes\ErrorHandlers\Exception;

class SpiceCRMExchangeTasks extends SpiceCRMExchangeBeans
{
    protected $moduleName     = 'Tasks';
//    protected $itemName       = 'Task';
    protected $tableName      = 'tasks';
    protected $pivotTableName = 'tasks_users';
    protected $pivotBeanId    = 'task_id';

//    protected $updateFieldMapping = [
//        'Subject' => [
//            'itemField' => 'Subject',
//            'beanField' => 'name',
//            'subtype'   => UnindexedFieldURIType::ITEM_SUBJECT,
//        ],
//        'StartDate' => [
//            'itemField' => 'StartDate',
//            'beanField' => 'date_start',
//            'type'      => 'datetime',
//            'subtype'   => UnindexedFieldURIType::TASK_START_DATE,
//        ],
//        'DueDate' => [
//            'itemField' => 'DueDate',
//            'beanField' => 'date_due',
//            'type'      => 'datetime',
//            'subtype'   => UnindexedFieldURIType::TASK_DUE_DATE,
//        ],
//        // Body
//        // Importance
//        // Status
//        // PercentComplete
//    ];

    public function createOnExchange() {
        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_CREATE);

        $request = new CreateItemType();
        $request->Items = new NonEmptyArrayOfAllItemsType();

        $exchangeTask = new TaskType();

        $this->mapBeanToEWS($exchangeTask);

        $request->Items->Task[] = $exchangeTask;

        try {
            $response = $this->connector->client->request('CreateItem', $request);
            $this->spiceBean->external_id = $response->ResponseMessages->CreateItemResponseMessage[0]->Items->Task[0]->ItemId->Id;
            $this->spiceBean->external_data = json_encode($response->ResponseMessages->CreateItemResponseMessage[0]->Items->Task[0]->ItemId);

            $this->logger->logOutboundRecord(
                $this->spiceBean,
                $response->ResponseMessages->CreateItemResponseMessage[0]->ResponseCode,
                $response->ResponseMessages->CreateItemResponseMessage[0]->MessageText
            );

            return $response;
        } catch (\Exception $e) {
            throw new Exception($e->detail->Message, 400);
        }
    }

    public function updateOnExchange() {
        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_UPDATE);

        $request = new UpdateItemType();
        $request->ConflictResolution = ConflictResolutionType::ALWAYS_OVERWRITE;

        $change = new ItemChangeType();
        $change->ItemId = new ItemIdType();
        $change->ItemId->Id = $this->getExternalId();
        $change->Updates = new NonEmptyArrayOfItemChangeDescriptionsType();
        $change->Updates->SetItemField = $this->createUpdateArray();

        $request->ItemChanges[] = $change;
        $response = $this->connector->client->request('UpdateItem', $request);

        $this->logger->logOutboundRecord(
            $this->spiceBean,
            $response->ResponseMessages->UpdateItemResponseMessage[0]->ResponseCode,
            $response->ResponseMessages->UpdateItemResponseMessage[0]->MessageText
        );

        return $response;
    }

    protected function addToDeleteRequest($request) {
        // TODO: Implement addToDeleteRequest() method.
    }

    protected function mapBeanToEWS($exchangeItem) {
//        $exchangeItem->Subject = $this->spiceBean->name;
//        $start = new DateTime($this->spiceBean->date_start, new DateTimeZone('UTC'));
//        $due   = new DateTime($this->spiceBean->date_due, new DateTimeZone('UTC'));
//        $exchangeItem->StartDate = $start->format('c');
//        $exchangeItem->DueDate   = $due->format('c');
//        $exchangeItem->Body      = new BodyType();
//        $exchangeItem->Body->_   = $this->spiceBean->description;
//        $exchangeItem->Body->BodyType = new BodyTypeType();
//        $exchangeItem->Body->BodyType->_ = BodyTypeType::TEXT;
//        $exchangeItem->Importance = $this->getBeanImportance();
//        $exchangeItem->Status = $this->getBeanStatus();

        // get mapping
        $fields = $this->createCreateArray();

        // enrich exchange object
        foreach($fields as $property => $value){
            $exchangeItem->$property = $value;
        }
    }

    protected function getSpiceCRMId($itemId) {
        $db = DBManagerFactory::getInstance();
        $sugarId = $db->fetchByAssoc($db->query("SELECT id FROM " . $this->tableName. " WHERE external_id='$itemId'"));
        return $sugarId ? $sugarId['id'] : false;
    }

    /**
     * createCreateArray
     *
     * Creates an array with the necessary EWS properties for the event create request.
     *
     * @return array
     */
    protected function createCreateArray()
    {
        $mapping = new SpiceCRMExchangeFieldMappingTask($this->spiceBean);
        return $mapping->createCreateArray();
    }

    /**
     * Creates an array with the necessary EWS properties for the event update request.
     *
     * @return array
     * @throws \Exception
     */
    protected function createUpdateArray()
    {
        $mapping = new SpiceCRMExchangeFieldMappingTask($this->spiceBean);
        return $mapping->createUpdateArray();
    }

    /**
     * @inheritDoc
     */
    protected function getExternalId() {
        return $this->spiceBean->external_id;
    }
}
