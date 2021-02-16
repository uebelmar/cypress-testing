<?php
namespace SpiceCRM\includes\SpiceCRMExchange\Mappings;

use Exception;
use jamesiarmes\PhpEws\Enumeration\ImportanceChoicesType;
use jamesiarmes\PhpEws\Enumeration\TaskStatusType;
use jamesiarmes\PhpEws\Type\SetItemFieldType;
use jamesiarmes\PhpEws\Type\TaskType;

class SpiceCRMExchangeFieldMappingTask extends SpiceCRMExchangeFieldMapping
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
//        echo print_r($fields, true);
//        die();
        // organize
        foreach ($fields as $key => $value) {
            if (empty($this->spiceBean->{$value['beanField']})) continue;

            switch ($value['type']) {
                case 'datetime':
                    $retArray[$value['itemField']] = self::convertDateTimeToExchange($this->spiceBean->{$value['beanField']});
                    break;
                case 'body':
                    $retArray[$value['itemField']] = self::generateBodyType($this->spiceBean->{$value['beanField']}, $value['bodytype']);
                    break;
                case 'reminder':
                    // check on value and change Object passed if necessary
                    $reminder = self::generateReminder($value['itemField'], $this->spiceBean->{$value['beanField']});
                    $retArray[$reminder['itemField']] = $reminder['value'];
                    break;
                case 'status':
                    if(!empty($this->spiceBean->{$value['beanField']})){
                        $retArray[$value['itemField']] = self::generateStatus($this->spiceBean->{$value['beanField']});
                    }
                    break;
                case 'importance':
                    if(!empty($this->spiceBean->{$value['beanField']})){
                        $retArray[$value['itemField']] = self::generateImportance($this->spiceBean->{$value['beanField']});
                    }
                    break;
                default:
                    $retArray[$value['itemField']] = self::generateSetUnindexedField($this->spiceBean->{$value['beanField']});
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

        foreach ($fields as $key => $value) {
            if (empty($this->spiceBean->{$value['beanField']})) continue;

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
                        $this->spiceBean->{$value['beanField']},
                        $value['subtype'],
                        $value['bodytype']
                    );
                    break;
                case 'reminder':
                    // check on value and change Object passed if necessary
                    $reminder = $this->generateReminder($value['itemField'], $this->spiceBean->{$value['beanField']});
                    $retArray[$reminder['itemField']] = $reminder['value'];
                    break;
                case 'status':
                    $retArray[] = $this->generateSetItemStatusFieldType(
                        $value['itemField'],
                        $this->spiceBean->{$value['beanField']},
                        $value['subtype']

                    );
                    break;
                case 'importance':
                    $retArray[] = $this->generateSetItemImportanceFieldType(
                        $value['itemField'],
                        $this->spiceBean->{$value['beanField']},
                        $value['subtype']
                    );
                    break;
                default:
                    $retArray[] = $this->generateSetItemUnindexedFieldType(
                        $value['itemField'],
                        $this->spiceBean->{$value['beanField']},
                        $value['subtype']
                    );
                    break;
            }
        }
//        echo print_r($retArray, true);
//        die();
        return $retArray;
    }




    private function generateSetItemUnindexedFieldType($fieldname, $value, $subtype) {
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = "$subtype";
        $field = $this->generateSetItemFieldType($subtype);
        $field->Task = new TaskType();
        $field->Task->$fieldname = $this->convertTextToExchange($value);
        return $field;
    }

    private function generateDateTimeField($itemField, $beanField, $subtype) {
        // Set the updated start time.
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = $subtype;
        $field = $this->generateSetItemFieldType($subtype);
        $field->Task = new TaskType();
//        $dateTime = new DateTime($this->spiceBean->$beanField, new DateTimeZone('UTC'));
//        $field->Task->$itemField = $dateTime->format('c');
        $field->Task->$itemField = $this->convertDateTimeToExchange($this->spiceBean->$beanField);

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
        $field = $this->generateSetItemFieldType($subtype);
        $field->Task = new TaskType();
        $field->Task->Body = self::generateBodyType($value, $bodytype);

        return $field;
    }

//    /**
//     * @param $bodytype
//     * @param $value
//     */
//    public static function generateBodyType($value, $bodytype){
//        $body = new BodyType();
//        $body->_ = self::convertTextToExchange($value);
//        $body->Body->BodyType    = new BodyType();
//        $body->Body->BodyType->_ = $bodytype;
//        return $body;
//    }

    private function generateSetItemStatusFieldType($itemField, $value, $subtype){
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = "$subtype";
        $field = $this->generateSetItemFieldType($subtype);
        $field->Task = new TaskType();
        $field->Task->$itemField = $this->generateStatus($value);
        return $field;
    }

    private function generateSetItemImportanceFieldType($itemField, $value, $subtype){
//        $field = new SetItemFieldType();
//        $field->FieldURI = new PathToUnindexedFieldType();
//        $field->FieldURI->FieldURI = "$subtype";
        $field = $this->generateSetItemFieldType($subtype);
        $field->Task = new TaskType();
        $field->Task->$itemField = $this->generateImportance($value);
        return $field;
    }

    /**
     * generateImportance
     *
     * Maps the priority of the bean to the EWS task importance.
     *
     * @return string
     */
    private function generateImportance($value) {
        switch ($value) {
            case 'High':
                return ImportanceChoicesType::HIGH;
            case 'Low':
                return ImportanceChoicesType::LOW;
            case 'Medium':
            default:
                return ImportanceChoicesType::NORMAL;
        }
    }

    /**
     * Maps the status of the bean to EWS task status
     *
     * @return string
     */
    private function generateStatus($value) {
        switch ($value) {
            case 'In Progress':
                return TaskStatusType::IN_PROGRESS;
            case 'Completed':
                return TaskStatusType::COMPLETED;
            case 'Pending Input':
                return TaskStatusType::WAITING_ON_OTHERS;
            case 'Deferred':
                return TaskStatusType::DEFERRED;
            case 'Not Started':
            default:
                return TaskStatusType::NOT_STARTED;
        }
    }
}
