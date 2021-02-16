<?php
namespace SpiceCRM\includes\SpiceCRMExchange\Mappings;

use Exception;
use jamesiarmes\PhpEws\Enumeration\DictionaryURIType;
use jamesiarmes\PhpEws\Enumeration\PhysicalAddressKeyType;
use jamesiarmes\PhpEws\Type\ContactItemType;
use jamesiarmes\PhpEws\Type\EmailAddressDictionaryEntryType;
use jamesiarmes\PhpEws\Type\EmailAddressDictionaryType;
use jamesiarmes\PhpEws\Type\ExtendedPropertyType;
use jamesiarmes\PhpEws\Type\PathToExtendedFieldType;
use jamesiarmes\PhpEws\Type\PhoneNumberDictionaryEntryType;
use jamesiarmes\PhpEws\Type\PhoneNumberDictionaryType;
use jamesiarmes\PhpEws\Type\PhysicalAddressDictionaryEntryType;
use jamesiarmes\PhpEws\Type\SetItemFieldType;

class SpiceCRMExchangeFieldMappingContact extends SpiceCRMExchangeFieldMapping
{

    /**
     * createCreateArray
     *
     * Creates an array with the necessary EWS properties for the event create request.
     *
     * @return array
     */
    public function createCreateArray() {
        $retArray = [];

        // retrieve mapping
        $fields = $this->getFieldMapping();
//        echo '<pre>'.print_r($fields, true);
//        die();
        // organize
        foreach ($fields as $key => $value) {
            // store bean value
            if (isset($value['beanField']) && isset($this->spiceBean->{$value['beanField']})) {
                $beanFieldValue = $this->spiceBean->{$value['beanField']};
            }

            // apply customFunction & overwrite bean value if defined so
            if(isset($value['customFunction'])){
                $beanFieldValue = $this->applyCustomFunctionToBean($this->spiceBean, $value['customFunction'], 'out');
            }

            // skip empty fields
            if (empty($this->spiceBean->{$value['beanField']})) continue;

            switch ($value['type']) {
                case 'datetime':
                    $retArray[$value['itemField']] = self::convertDateTimeToExchange($beanFieldValue);
                    break;
                case 'body':
                    $retArray[$value['itemField']] = self::generateBodyType($beanFieldValue, $value['bodytype']);
                    break;
                case 'email':
                    $retArray['EmailAddresses'][] = $this->generateEmailAddressDictionaryEntryType($value['bodytype'], $beanFieldValue);

                    break;
                case 'phone':
                    $retArray['PhoneNumbers'][] = $this->PhoneNumberDictionaryEntryType($value['subtype'], $beanFieldValue);
                    break;
                case 'address':
//                    echo '<pre>'.print_r($value, true);
//                    die();
                    $retArray['PhysicalAddresses'][] = $this->generatePhysicalAddressDictionaryEntryType(
                        $value['subtype'],
                        $value['fields']
                    );
                    break;
                case 'extended':
                    $retArray['ExtendedProperty'][] = $this->generateExtendedPropertyType(
                        $value['subtype'],
                        $this->spiceBean->{$value['beanField']},
                        $value['tag']
                    );
                    break;
                default:
                    $retArray[$value['itemField']] = self::generateSetUnindexedField($beanFieldValue);
                    break;
            }
        }

//        echo '<pre>'.print_r($fields, true);
//        die();
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
    public function createUpdateArray()
    {
        $retArray = [];

        // retrieve mapping
        $fields = $this->getFieldMapping($this->spiceBean);
//        echo '<pre>'.print_r($fields, true);
//        die();

        // organize
        foreach ($fields as $key => $value) {
            // store bean value
            if (isset($value['beanField']) && isset($this->spiceBean->{$value['beanField']})) {
                $beanFieldValue = $this->spiceBean->{$value['beanField']};
            }

            // apply customFunction & overwrite bean value if defined so
            if (isset($value['customFunction'])) {
                $beanFieldValue = $this->applyCustomFunctionToBean($this->spiceBean, $value['customFunction'], 'out');
            }

            // skip empty fields
            // if (empty($beanFieldValue) && !isset($value['fields'])) continue;

            switch ($value['type']) {
                case 'email':
                    $field = $this->generateEmailAddressFieldType(
                        $value['subtype'],
                        $beanFieldValue
                    );
                    $retArray[] = $field;
                    break;
                case 'phone':
                    $retArray[] = $this->generatePhoneNumberFieldType(
                        $value['subtype'],
                        $beanFieldValue,
                        $value['uri']
                    );
                    break;
                case 'address':
//                    echo '<pre>'.print_r($value, true);
//                    die();
                    if(is_array($value['fields'])) {
                        foreach ($value['fields'] as $itemField => $itemBean) {
                            $retArray[] = $this->generateAddressFieldType(
                                $value['subtype'],
                                $value['uri'],
                                $itemField,
                                $this->spiceBean->{$itemBean}
                            );
                        }
                    }
                    break;
                case 'extended':
                    $retArray[] = $this->generateExtendedPropertyFieldType(
                        $value['subtype'],
                        $beanFieldValue,
                        $value['tag']
                    );
                    break;
                default:
                    $retArray[] = $this->generateSetItemUnindexedFieldType(
                        $value['subtype'],
                        $value['itemField'],
                        $beanFieldValue
                    );
                    break;
            }
        }

        return $retArray;
    }

    /**
     * generates the set item object for a specific field. Used for the update method
     *
     * @param $fieldname the fieldname e.g. 'GivenName'
     * @param $value the value
     * @return SetItemFieldType
     */
    private function generateSetItemUnindexedFieldType($subtype,$fieldname, $value)
    {
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = "contacts:$fieldname";
        $field = $this->generateSetItemFieldType($subtype);
        $field->Contact = new ContactItemType();
        $field->Contact->$fieldname = self::convertTextToExchange($value);
        return $field;
    }

    /**
     * generateEmailAddressFieldType
     *
     * Generates an email address field for the update method.
     *
     * @param $subtype e.g. EmailAddressKeyType::EMAIL_ADDRESS_1
     * @param $value
     * @return SetItemFieldType
     */
    protected function generateEmailAddressFieldType($subtype, $value)
    {
        $field = new SetItemFieldType();
        $field->IndexedFieldURI = new PathToExtendedFieldType();
        $field->IndexedFieldURI->FieldURI = DictionaryURIType::CONTACTS_EMAIL_ADDRESS;
        $field->IndexedFieldURI->FieldIndex = $subtype;
        $field->Contact = new ContactItemType();
        $field->Contact->EmailAddresses = new EmailAddressDictionaryType();

        $entry = $this->generateEmailAddressDictionaryEntryType($subtype, $value);
        $field->Contact->EmailAddresses->Entry = $entry;

        return $field;
    }

    /**
     * generateEmailAddressDictionaryEntryType
     *
     * @param $subtype
     * @param $value
     * @return EmailAddressDictionaryEntryType
     */
    protected function generateEmailAddressDictionaryEntryType($subtype, $value)
    {
        $field = new EmailAddressDictionaryEntryType();
        $field->_ = $value;
        $field->Key = $subtype;
        return $field;
    }

    /**
     * generatePhoneNumberFieldType
     *
     * Generates a phone number field for the update method.
     *
     * @param $subtype e.g. PhoneNumberKeyType::BUSINESS_PHONE
     * @param $value
     * @param $uri
     * @return SetItemFieldType
     */
    private function generatePhoneNumberFieldType($subtype, $value, $uri)
    {
        $field = new SetItemFieldType();
        $field->IndexedFieldURI = new PathToExtendedFieldType();
        $field->IndexedFieldURI->FieldURI = $uri;
        $field->IndexedFieldURI->FieldIndex = $subtype;
        $field->Contact = new ContactItemType();
        $field->Contact->PhoneNumbers = new PhoneNumberDictionaryType();
        $field->Contact->PhoneNumbers->Entry[] = $this->PhoneNumberDictionaryEntryType($subtype, $value);
        return $field;
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
    protected function PhoneNumberDictionaryEntryType($type, $value){
        $phone = new PhoneNumberDictionaryEntryType();
        $phone->Key = $type;
        $phone->_ = $value;
        return $phone;
    }

    /**
     * generateAddressDictionaryEntryType
     *
     * Generates an address field for the update method.
     * e.g. change street value
     *
     * @param $subtype
     * @param $fields
     * @param $uri
     * @return PhysicalAddressDictionaryEntryType
     */
    private function generateAddressFieldType($subtype, $uri, $itemField, $value) {

        $field = new SetItemFieldType();
        $field->IndexedFieldURI->FieldURI = $uri.':'.$itemField;
        $field->IndexedFieldURI->FieldIndex = $subtype;
        $field->Contact = new ContactItemType();
        $address = new PhysicalAddressDictionaryEntryType();
        $address->Key = $subtype;
        $address->$itemField = self::convertTextToExchange($value);

        $field->Contact->PhysicalAddresses->Entry[] = $address;

        return $field;
    }


    /**
     * generatePhysicalAddressDictionaryEntryType
     *
     *
     *
     * @param $subtype
     * @param $fields
     * @return PhysicalAddressDictionaryEntryType
     */
    private function generatePhysicalAddressDictionaryEntryType($subtype, $fields)
    {
        $address = new PhysicalAddressDictionaryEntryType();
        $address->Key = PhysicalAddressKeyType::HOME;
        foreach($fields as $property=> $fieldname){
            $address->{$property} = self::convertTextToExchange($this->spiceBean->{$fieldname});
        }
        return $address;
    }

    /**
     * generateExtendedFieldType
     *
     * Generates an extended field.
     *
     * @param $subtype
     * @param $value
     * @param $tag
     * @return ExtendedPropertyType
     */
    private function generateExtendedPropertyType($subtype, $value, $tag) {
        $property = new ExtendedPropertyType();
        $property->ExtendedFieldURI = new PathToExtendedFieldType();
        $property->ExtendedFieldURI->PropertyTag = $tag;
        $property->ExtendedFieldURI->PropertyType = $subtype;
        $property->Value = $value;
        return $property;
    }

    /**
     * generateExtendedPropertyFieldType
     *
     * Generates an extended field for the update method.
     *
     * @param $subtype
     * @param $value
     * @param $tag
     * @return SetItemFieldType
     */
    private function generateExtendedPropertyFieldType($subtype, $value, $tag) {
        $contact = new ContactItemType();
        // Build the extended property and set it on the item.
        $property = $this->generateExtendedPropertyType($subtype, $value, $tag);
        $contact->ExtendedProperty = $property;

        // Build the set item field object and set the item on it.
        $field = new SetItemFieldType();
        $field->ExtendedFieldURI = new PathToExtendedFieldType();
        $field->ExtendedFieldURI->PropertyTag = $tag;
        $field->ExtendedFieldURI->PropertyType = $subtype;
        $field->Contact = $contact;

        return $field;
    }

}
