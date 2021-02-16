<?php
namespace SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers;

use jamesiarmes\PhpEws\Enumeration\DisposalType;
use jamesiarmes\PhpEws\Request\DeleteItemType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\PhoneNumberDictionaryEntryType;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\EwsConnectionException;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeConnector;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeLogger;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;

abstract class SpiceCRMExchangeBeans
{
    /**
     * @var SpiceCRMExchangeConnector
     */
    public $connector;

    /**
     * @var the user on CRM .. full user bean
     */
    protected $user;

    /**
     * @var SugarBean Bean on CRM ... full bean
     */
    public $spiceBean;

    /**
     * @var string name of the SpiceCRM module.
     */
    protected $moduleName;

    /**
     * @var SpiceCRMExchangeLogger The logger for the communication with the Exchange server.
     */
    protected $logger;

    /**
     * @var bool A flag that is set to true if the corresponding EWS object doesn't have the _id attribute set.
     */
    public $needsUpdate = false;

    protected $itemName;

    protected $tableName;

    protected $updateFieldMapping;

    public function __construct($user, &$bean) {
        $this->logger = new SpiceCRMExchangeLogger();

        // set the user
        $this->user = $user;

        // create a client and impersonate the set user
        $this->connector = new SpiceCRMExchangeConnector($user);

        // set the bean .. if string is passed in assume it is a guid and load the bean
        if (is_string($bean)) {
            $this->spiceBean = BeanFactory::getBean($this->moduleName, $bean, ['encode' => false]);
        } else {
            $this->spiceBean = $bean;
        }
    }

    /**
     * createPhoneDictionaryEntry
     *
     * Creates a phone dictionary item for the exchange item.
     *
     * @param $type one value of PhoneNumberKeyType
     * @param $value the phone number
     * @return PhoneNumberDictionaryEntryType
     */
    protected function createPhoneDictionaryEntry($type, $value){
        $phone = new PhoneNumberDictionaryEntryType();
        $phone->Key = $type;
        $phone->_ = $value;
        return $phone;
    }

    protected abstract function createOnExchange();

    protected abstract function updateOnExchange();

    /**
     * Deletes a contact from Exchange.
     *
     * @return mixed
     * @throws Exception
     * @throws EwsConnectionException
     */
    public function deleteOnExchange() {
        $request = $this->getDeleteRequest();

        $response = $this->connector->client->request('DeleteItem', $request);

        return $response;
    }

    /**
     * getDeleteRequest
     *
     * Creates an EWS request for deleting a meeting from the Exchange server.
     * It just returns the request object and doesn't send it to EWS.
     *
     * @return DeleteItemType
     * @throws Exception
     */
    public function getDeleteRequest() {
        $ewsId = $this->getExternalId();

        $request             = new DeleteItemType();
        $request->DeleteType = DisposalType::HARD_DELETE;
//        $request->ItemIds    = new ItemIdType($ewsId);
        $request->ItemIds->ItemId[0] = new ItemIdType();
        $request->ItemIds->ItemId[0]->Id = $ewsId;

        $request = $this->addToDeleteRequest($request);

        return $request;
    }

    protected abstract function addToDeleteRequest($request);

    protected abstract function mapBeanToEWS($exchangeItem);

    protected abstract function getSpiceCRMId($itemId);

    protected abstract function createUpdateArray();

    /**
     * getChangeKey
     *
     * Returns the EWS change key stored as JSON in the external_data field.
     *
     * @return mixed
     */
    public function getChangeKey() {
        $externalData = json_decode(html_entity_decode($this->spiceBean->external_data));
        return $externalData->ChangeKey;
    }

    /**
     * getExternalId
     *
     * If available returns the exchange ID of a bean, that is stores as JSON in the pivot table.
     *
     * @return mixed
     * @throws Exception
     */
    protected abstract function getExternalId();

    /**
     * findByExternalId
     *
     * Searches for a bean with the given external EWS ID.
     *
     * @param $externalId
     * @return SugarBean
     */
    protected function findByExternalId($externalId) {
        $bean = BeanFactory::getBean($this->moduleName);
        $bean->retrieve_by_string_fields(['external_id' => $externalId]);

        return $bean;
    }

    public function existsBeanWithExternalId($externalId) {
        $bean = $this->findByExternalId($externalId);

        if ($bean->external_id == $externalId) {
            return true;
        }

        return false;
    }
}
