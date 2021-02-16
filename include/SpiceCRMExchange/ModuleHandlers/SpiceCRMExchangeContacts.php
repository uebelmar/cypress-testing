<?php

namespace SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers;

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfItemChangeDescriptionsType;
use jamesiarmes\PhpEws\Enumeration\ConflictResolutionType;
use jamesiarmes\PhpEws\Enumeration\FileAsMappingType;
use jamesiarmes\PhpEws\Enumeration\MapiPropertyTypeType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\Request\UpdateItemType;
use jamesiarmes\PhpEws\Type\ContactItemType;
use jamesiarmes\PhpEws\Type\EmailAddressDictionaryType;
use jamesiarmes\PhpEws\Type\ExtendedPropertyType;
use jamesiarmes\PhpEws\Type\ItemChangeType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\PathToExtendedFieldType;
use jamesiarmes\PhpEws\Type\PhoneNumberDictionaryType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedPropertySetType;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\EwsConnectionException;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeFieldMappingContact;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeLogger;
use SpiceCRM\includes\ErrorHandlers\Exception;

class SpiceCRMExchangeContacts extends SpiceCRMExchangeBeans
{
    protected $moduleName     = 'Contacts';
    protected $itemName       = 'Contact';
    protected $tableName      = 'contacts';
    protected $pivotTableName = 'contacts_users';
    protected $pivotBeanId    = 'contact_id';
    public $external_id = null;

    // todo add the rest of the fields to the mapping
//    private $updateFieldMapping = [
//        'GivenName' => [
//            'itemField' => 'GivenName',
//            'beanField' => 'first_name',
//        ],
//        'Surname' => [
//            'itemField' => 'Surname',
//            'beanField' => 'last_name',
//        ],
//        'CompanyName' => [
//            'itemField' => 'CompanyName',
//            'beanField' => 'account_name',
//        ],
//        'BusinessPhone' => [
//            'beanField' => 'phone_work',
//            'type'      => 'phone',
//            'subtype'   => PhoneNumberKeyType::BUSINESS_PHONE,
//            'uri'       => DictionaryURIType::CONTACTS_PHONE_NUMBER,
//        ],
//        'MobilePhone' => [
//            'beanField' => 'phone_mobile',
//            'type'      => 'phone',
//            'subtype'   => PhoneNumberKeyType::MOBILE_PHONE,
//            'uri'       => DictionaryURIType::CONTACTS_PHONE_NUMBER,
//        ],
//        'HomePhone' => [
//            'beanField' => 'phone_home',
//            'type'      => 'phone',
//            'subtype'   => PhoneNumberKeyType::HOME_PHONE,
//            'uri'       => DictionaryURIType::CONTACTS_PHONE_NUMBER,
//        ],
//        'BusinessFax' => [
//            'beanField' => 'phone_fax',
//            'type'      => 'phone',
//            'subtype'   => PhoneNumberKeyType::BUSINESS_FAX,
//            'uri'       => DictionaryURIType::CONTACTS_PHONE_NUMBER,
//        ],
//        'Email1' => [
//            'beanField' => 'email1',
//            'type'      => 'email',
//            'subtype'   => EmailAddressKeyType::EMAIL_ADDRESS_1,
//        ],
//        'PrimaryAddress' => [
//            'type'    => 'address',
//            'subtype' => PhysicalAddressKeyType::HOME,
//            'fields'  => [
//                'Street'          => 'primary_address_street',
//                'City'            => 'primary_address_city',
//                'PostalCode'      => 'primary_address_postalcode',
//                'CountryOrRegion' => 'primary_address_country',
//            ],
//        ],
//        'OtherAddress' => [
//            'type'    => 'address',
//            'subtype' => PhysicalAddressKeyType::OTHER,
//            'fields'  => [
//                'Street'          => 'alt_address_street',
//                'City'            => 'alt_address_city',
//                'PostalCode'      => 'alt_address_postalcode',
//                'CountryOrRegion' => 'alt_address_country',
//            ],
//        ],
//        'Salutation' => [
//            'type'      => 'extended',
//            'tag'       => '0x3A45',
//            'subtype'   => MapiPropertyTypeType::STRING,
//            'beanField' => 'salutation',
//        ],
//    ];

    /**
     * SpiceCRMExchangeContacts constructor.
     * @param $user
     * @param $bean
     * @throws Exception
     */
    public function __construct($user, &$bean){
        parent::__construct($user, $bean);
        $this->external_id = $this->getExternalId();
    }

    /**
     * Creates a contact on Exchange for the given user.
     *
     * @return mixed
     * @throws Exception
     */
    public function createOnExchange() {
        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_CREATE);

        $request = new CreateItemType();
        $exchangecontact = new ContactItemType();

        $this->mapBeanToEWS($exchangecontact);

        $request->Items->Contact[] = $exchangecontact;

        try {
            $response = $this->connector->client->request('CreateItem', $request);

            $this->logger->logOutboundRecord(
                $this->spiceBean,
                $response->ResponseMessages->CreateItemResponseMessage[0]->ResponseCode,
                $response->ResponseMessages->CreateItemResponseMessage[0]->MessageText
            );

            $this->setSyncedUserData(
                $response->ResponseMessages->CreateItemResponseMessage[0]->Items->Contact[0]->ItemId
            );

            return [
                'status'  => 'success',
                'message' => $response->ResponseMessages->CreateItemResponseMessage[0]->MessageText,
            ];
        } catch (\Exception $e) {
            throw new Exception($e->detail->Message, 400);
        }
    }

    /**
     * Deletes a contact from Exchange.
     *
     * @return mixed|void
     * @throws Exception
     * @throws EwsConnectionException
     */
    public function deleteOnExchange()
    {
        $response = parent::deleteOnExchange();

        if ($response->ResponseMessages->DeleteItemResponseMessage[0]->ResponseClass == 'Success') {
            $this->unsetSyncedUserData();
        }

    }

    public function getSpiceCRMId($itemid)
    {

    }

    /**
     * updates a contact for a given user on Exchange
     *
     * @return mixed
     * @throws Exception
     * @throws EwsConnectionException
     */
    function updateOnExchange()
    {
//        $this->logger->logOutboundRecord($this->spiceBean, SpiceCRMExchangeLogger::REQUEST_TYPE_UPDATE);

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
            __CLASS__.'::'. __FUNCTION__. ' '.$response->ResponseMessages->UpdateItemResponseMessage[0]->ResponseCode,
            $response->ResponseMessages->UpdateItemResponseMessage[0]->MessageText
        );

        return [
            'status'  => 'success',
            'message' => $response->ResponseMessages->UpdateItemResponseMessage[0]->MessageText,
        ];
    }

    /**
     * map a conmtact to the excahnge conmtact passed in
     * ToDo: add flexible mapping
     *
     * @param $exchangecontact
     */
    protected function mapBeanToEWS($exchangecontact)
    {
//        $exchangecontact->GivenName = $this->spiceBean->first_name;
//        $exchangecontact->Surname = $this->spiceBean->last_name;
        $exchangecontact->FileAsMapping = FileAsMappingType::FIRST_SPACE_LAST;
//        $exchangecontact->CompanyName = $this->spiceBean->account_name;

//        $exchangecontact->EmailAddresses = new EmailAddressDictionaryType();

        // Set an email address.
//        $email = new EmailAddressDictionaryEntryType();
//        $email->Key = EmailAddressKeyType::EMAIL_ADDRESS_1;
//        $email->_ = $this->spiceBean->email1;
//        $exchangecontact->EmailAddresses->Entry[] = $email;

        // Set contact title as an extended property.
//        $property = new ExtendedPropertyType();
//        $property->ExtendedFieldURI = new PathToExtendedFieldType();
//        $property->ExtendedFieldURI->PropertyTag = '0x3A45';
//        $property->ExtendedFieldURI->PropertyType = MapiPropertyTypeType::STRING;
//        $property->Value = $this->spiceBean->salutation;
//        $exchangecontact->ExtendedProperty[] = $property;

        $property = new ExtendedPropertyType();
        $property->ExtendedFieldURI = new PathToExtendedFieldType();
        $property->ExtendedFieldURI->DistinguishedPropertySetId = DistinguishedPropertySetType::PUBLIC_STRINGS;
        $property->ExtendedFieldURI->PropertyType = MapiPropertyTypeType::STRING;
        $property->ExtendedFieldURI->PropertyName = 'SpiceCRMID';
        $property->Value = $this->spiceBean->id;
        $exchangecontact->ExtendedProperty[] = $property;

        // Set an address.
//        $address = new PhysicalAddressDictionaryEntryType();
//        $address->Key = PhysicalAddressKeyType::HOME;
//        $address->Street = $this->spiceBean->primary_address_street;
//        $address->City = $this->spiceBean->primary_address_city;
//        $address->PostalCode = $this->spiceBean->primary_address_postalcode;
//        $address->CountryOrRegion = $this->spiceBean->primary_address_country;
//        $exchangecontact->PhysicalAddresses->Entry[] = $address;

        // Set an alternative address.
//        $address2 = new PhysicalAddressDictionaryEntryType();
//        $address2->Key = PhysicalAddressKeyType::OTHER;
//        $address2->Street = $this->spiceBean->alt_address_street;
//        $address2->City = $this->spiceBean->alt_address_city;
//        $address2->PostalCode = $this->spiceBean->alt_address_postalcode;
//        $address2->CountryOrRegion = $this->spiceBean->alt_address_country;
//        $exchangecontact->PhysicalAddresses->Entry[] = $address2;

        // Set phone numbers
//        $exchangecontact->PhoneNumbers = new PhoneNumberDictionaryType();
//        $exchangecontact->PhoneNumbers->Entry[] = $this->createPhoneDictionaryEntry(PhoneNumberKeyType::BUSINESS_PHONE, $this->spiceBean->phone_work);
//        $exchangecontact->PhoneNumbers->Entry[] = $this->createPhoneDictionaryEntry(PhoneNumberKeyType::MOBILE_PHONE, $this->spiceBean->phone_mobile);
//        $exchangecontact->PhoneNumbers->Entry[] = $this->createPhoneDictionaryEntry(PhoneNumberKeyType::HOME_PHONE, $this->spiceBean->phone_home);
//        $exchangecontact->PhoneNumbers->Entry[] = $this->createPhoneDictionaryEntry(PhoneNumberKeyType::BUSINESS_FAX, $this->spiceBean->phone_fax);


        // Set the contact body (this is the "Notes" field in Outlook).
//        $exchangecontact->Body = new BodyType();
//        $exchangecontact->Body->BodyType = BodyTypeType::TEXT;
//        $exchangecontact->Body->_ = $this->spiceBean->description;


        // get mapping
        $fields = $this->createCreateArray();

        // enrich exchange object
        foreach($fields as $property => $value){
            if(empty($value)) continue;
            switch($property){
                case 'EmailAddresses';
                    $exchangecontact->EmailAddresses = new EmailAddressDictionaryType();
                    $exchangecontact->EmailAddresses->Entry = $value;
                    break;
                case 'PhoneNumbers';
                    $exchangecontact->PhoneNumbers = new PhoneNumberDictionaryType();
                    $exchangecontact->PhoneNumbers->Entry = $value;
                    break;
                case 'PhysicalAddresses';
                    $exchangecontact->PhysicalAddresses->Entry = $value;
                    break;
                case 'ExtendedProperty';
                    $exchangecontact->ExtendedProperty = $value;
                    break;
                default:
                    $exchangecontact->{$property} = $value;
            }
        }
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
        $mapping = new SpiceCRMExchangeFieldMappingContact($this->spiceBean);
        return $mapping->createCreateArray();
    }

    /**
     * generates the update array
     *
     * Creates an array with the necessary EWS properties for the event update request.
     *
     * @return array
     * @throws \Exception
     */
    protected function createUpdateArray()
    {
        $mapping = new SpiceCRMExchangeFieldMappingContact($this->spiceBean);
        return $mapping->createUpdateArray();
    }


    /**
     * @param $request
     * @return mixed
     */
    protected function addToDeleteRequest($request)
    {
        return $request;
    }


    /**
     * setSyncedUserData
     *
     * Adds an entry into the contacts_users table thus enabling the synchronization of the given contact for the given user.
     *
     * @param $responseObject
     * @return bool|resource
     */
    private function setSyncedUserData($responseObject)
    {
        // todo check if an entry for this bean/user pair already exist to avoid duplicates
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
                    " SET `external_data`='$extData', `external_id`='{$responseObject->Id}', `date_modified`='$dbNow' WHERE `id`='{$pivot_id}'");
            } else {
                // insert into meetings_users
                return $db->query("INSERT INTO " . $this->pivotTableName .
                    " (`id`, `" . $this->pivotBeanId . "`, `user_id`, `external_data`, `external_id`, `date_modified`, `deleted`) VALUES ('" .
                    create_guid() . "', '{$this->spiceBean->id}', '{$this->user->id}', '$extData', '{$responseObject->Id}', '$dbNow', 0 )");

            }
        }

    }
    /**
     * unsetSyncedUserData
     *
     * Removes the entry in pivot table thus stopping the syncing of the given bean for the given user.
     *
     * @return bool|resource
     */
    private function unsetSyncedUserData()
    {
        $db = DBManagerFactory::getInstance();
        $sql = "DELETE FROM " . $this->pivotTableName . " WHERE " . $this->pivotBeanId . " = '" . $this->spiceBean->id . "'" .
            " AND user_id = '" . $this->user->id . "'";
        return $db->query($sql);
    }

    /**
     * getExternalId
     *
     * If available returns the exchange ID of a bean, that is stores as JSON in the pivot table.
     *
     * @return mixed
     * @throws Exception
     */
    protected function getExternalId()
    {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT external_data FROM " . $this->pivotTableName . " WHERE " . $this->pivotBeanId . " = '" .
            $this->spiceBean->id . "' AND user_id = '" . $this->user->id . "' ORDER BY date_modified DESC";
        $res = $db->query($sql);
        $row =  $db->fetchByAssoc($res);
        $json = json_decode(html_entity_decode($row['external_data']));

        if (!$json) {
            // External Data empty is not an error. Just a fact that user hasn't synced contact yet
            return $json;
            // throw new Exception('External Data is empty');
        }

        return $json->Id;
    }

    protected function findByExternalId($externalId) {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT * 
                FROM `contacts_users` `cu`
                WHERE `cu`.`external_id`='" . $externalId . "'";
        $query = $db->query($sql);
        $result = $db->fetchByAssoc($query);
        return $result;
    }

    public function existsBeanWithExternalId($externalId) {
        $result = $this->findByExternalId($externalId);

        if ($result != null && $result['external_id'] == $externalId) {
            return true;
        }

        return false;
    }

    public static function staticExistsBeanWithExternalId($externalId) {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT * 
                FROM `contacts_users` `cu`
                WHERE `cu`.`external_id`='" . $externalId . "'";
        $query = $db->query($sql);
        $result = $db->fetchByAssoc($query);

        if ($result != null && $result['external_id'] == $externalId) {
            return true;
        }

        return false;
    }
}
