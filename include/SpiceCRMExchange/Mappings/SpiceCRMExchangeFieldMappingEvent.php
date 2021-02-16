<?php
namespace SpiceCRM\includes\SpiceCRMExchange\Mappings;

use Exception;
use jamesiarmes\PhpEws\Type\CalendarItemType;
use jamesiarmes\PhpEws\Type\SetItemFieldType;

class SpiceCRMExchangeFieldMappingEvent extends SpiceCRMExchangeFieldMapping
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
            $beanFieldValue = $this->spiceBean->{$value['beanField']};

            // apply customFunction to value if defined so
            if(isset($value['customFunction'])){
                $beanFieldValue = $this->applyCustomFunctionToBean($this->spiceBean, $value['customFunction'], 'out');
            }

            // skip empty fields
            if (empty($beanFieldValue)) continue;

            switch ($value['type']) {
                case 'datetime':
                    $retArray[$value['itemField']] = self::convertDateTimeToExchange($beanFieldValue);
                    break;
                case 'body':
                    $retArray[$value['itemField']] = self::generateBodyType($beanFieldValue, $value['bodytype']);
                    break;
                case 'reminder':
                    // check on value and change Object passed if necessary
                    $reminder = self::generateReminder($value['itemField'], $beanFieldValue);
                    $retArray[$reminder['itemField']] = $reminder['value'];
                    break;
                default:
                    $retArray[$value['itemField']] = self::generateSetUnindexedField($beanFieldValue);
                    break;
            }
        }

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
    public function createUpdateArray() {
        $retArray = [];

        // retrieve mapping
        $fields = $this->getFieldMapping();
//        echo '<pre>'.print_r($fields, true);
//        die();

        // organize
        foreach ($fields as $key => $value) {
            // store bean value
            $beanFieldValue = $this->spiceBean->{$value['beanField']};

            // apply customFunction to value if defined so
            if(isset($value['customFunction'])){
                $beanFieldValue = $this->applyCustomFunctionToBean($this->spiceBean, $value['customFunction'], 'out');
            }

            // skip empty fields
            // if (empty($beanFieldValue)) continue;

            switch ($value['type']) {
                case 'datetime':
                    $retArray[] = $this->generateDateTimeField(
                        $value['itemField'],
                        $value['beanField'],
                        $value['subtype']
                    );
                    break;
                case 'body':
                    $retArray[] = $this->generateSetItemBodyFieldType(
                        $beanFieldValue,
                        $value['subtype'],
                        $value['bodytype']
                    );
                    break;
                case 'reminder':
                    $retArray[] = $this->generateSetItemReminderIsSetFieldType(
                        $beanFieldValue,
                        'item:ReminderIsSet'
                    );
                    $retArray[] = $this->generateSetItemReminderBeforeStartFieldType(
                        $beanFieldValue,
                        $value['subtype']
                    );
                    break;
                default:
                    $retArray[] = $this->generateSetItemUnindexedFieldType(
                        $value['itemField'],
                        $beanFieldValue,
                        $value['subtype']
                    );
                    break;
            }
        }

//        echo '<pre>'.print_r($retArray, true);
//        die();
        return $retArray;
    }

    /**
     * createUpdateArrayEWSToBean
     *
     * Creates an array with the necessary ews to bean mapping the event inbound update request.
     *
     * @return array
     * @throws Exception
     */
    public function createArrayEWSToBean(){
        // retrieve mapping
        $fields = $this->getFieldMappingEWSToBean();
        return $fields;
    }

    /**
     * Generates the set item object a property of CalendarItem
     * @param $fieldname
     * @param $value
     * @param $subtype
     * @return SetItemFieldType
     */
    private function generateSetItemUnindexedFieldType($fieldname, $value, $subtype) {
        $field = $this->generateSetItemFieldType($subtype);
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->$fieldname = $this->convertTextToExchange($value);
        return $field;
    }

    /**
     * generateDateTimeField
     *
     * Generates the set item object for date fields.
     *
     * @param $itemField
     * @param $beanField
     * @param $subtype
     * @return SetItemFieldType
     * @throws Exception
     */
    private function generateDateTimeField($itemField, $beanField, $subtype) {
        // Set the updated start time.
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = $subtype;
        $field = self::generateSetItemFieldType($subtype);

        $field->CalendarItem = new CalendarItemType();
//        $dateTime = new DateTime($this->spiceBean->$beanField, new DateTimeZone('UTC'));
//        $field->CalendarItem->$itemField = $dateTime->format('c');
        $field->CalendarItem->$itemField = self::convertDateTimeToExchange($this->spiceBean->$beanField);

        return $field;
    }

    /**
     * generates the set item object for Body field. Used for the update method
     *
     * @param $value the value
     * @param $subtype FieldURI type.
     * @return SetItemFieldType
     */
    private function generateSetItemBodyFieldType($value, $subtype, $bodytype) {
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = $subtype;
        $field = self::generateSetItemFieldType($subtype);
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->Body = self::generateBodyType($value, $bodytype);
        return $field;
    }

    /**
     * @param $value
     * @param $subtype
     * @return ReminderBeforeStart Item
     */
    private function generateSetItemReminderBeforeStartFieldType($value, $subtype) {
        $field = $this->generateSetItemFieldType($subtype);
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->ReminderMinutesBeforeStart = self::convertSecondsToMinutes($value);
        return $field;
    }

    /**
     * @param $value
     * @param $subtype
     * @return ReminderIsSet Item
     */
    private function generateSetItemReminderIsSetFieldType($value, $subtype) {
        $field = $this->generateSetItemFieldType($subtype);
        $field->CalendarItem = new CalendarItemType();
        $field->CalendarItem->ReminderIsSet  = ($value > 0 ? true : false);
        return $field;
    }


}
