<?php
namespace SpiceCRM\includes\SpiceCRMExchange\Mappings;

use jamesiarmes\PhpEws\Type\BodyType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\SetItemFieldType;
use DateTime;
use DateTimeZone;
use SpiceCRM\includes\database\DBManagerFactory;

class SpiceCRMExchangeFieldMapping
{
    public $spiceBean;

    public function __construct($spiceBean)
    {
        $this->spiceBean = $spiceBean;
//        if(empty($this->spiceBean->module_name)) {
//            $this->spiceBean->module_name = $this->spiceBean->module_dir;
//        }
    }

    /**
     * checks if a class method is available
     * method shall be non-static
     * returns array is found; false if not
     * @param $customFunction
     * @param $direction
     * @return array|bool
     */
    public function checkCustomFunction($customFunction, $direction){
        // grab class & method name
        $parts = explode('->', $customFunction);
        if(count($parts) == 2 ){
            $class = $parts[0];
            $method = $parts[1];
            if(method_exists(new $class(), $method.'_'.$direction)){
                return ['class' => $class, 'method' => $method.'_'.$direction];
            }
        }
        return false;
    }



    /**
     * apply custom function to bean property
     * @param $value
     * @param $mapping
     * @param $bean
     */
    public function applyCustomFunctionToBean(&$bean, &$customFunction, $direction){
        $customFunctionParts = $this->checkCustomFunction($customFunction, $direction);
        if($customFunctionParts) {
            $class = $customFunctionParts['class'];
            $method = $customFunctionParts['method'];
            $fieldClass = new $class;
            return $fieldClass->$method($bean);
        }
        return null;
    }

    /**
     * retrieve all field configuration from database
     * build arrays per module
     *
     * @param $module
     * @param $syncdirection inbound or outbound
     * @return mixed
     */
    public function getSegmentItems($module, $syncdirection = 'outbound')
    {
        $db = DBManagerFactory::getInstance();
        // $syncdirection where where
        $syncwhere = " AND items.$syncdirection = 1 ";

        $segmentitems = $db->query("
            SELECT modules.module,
            items.id, items.attribute_field attribute_field, items.value_field value_field, items.parent_id,
            segments.exchange_segment
            FROM sysexchangemappingsegments segments
            LEFT JOIN sysexchangemappingsegmentitems items ON items.segment_id = segments.id
            LEFT JOIN sysmodules modules ON segments.sysmodule_id = modules.id
            WHERE modules.module = '$module' AND segments.active = 1 AND items.active = 1 AND items.deleted = 0 $syncwhere
            ORDER BY attribute_field, value_field
            ");

        while ($item = $db->fetchByAssoc($segmentitems)) {
            $rows[] = $item;
        }
//echo '<pre>'.print_r($rows, true);
//die();

        //Overwrite with custom
        $segmentitems = $db->query("
            SELECT modules.module,
            items.id, items.attribute_field attribute_field, items.value_field value_field, items.parent_id,
            segments.exchange_segment
            FROM  ( SELECT * FROM sysexchangemappingsegments UNION SELECT * FROM sysexchangemappingcustomsegments) segments
            LEFT JOIN sysexchangemappingcustomsegmentitems items ON items.segment_id = segments.id
            LEFT JOIN ( SELECT * FROM sysmodules UNION SELECT * FROM syscustommodules) modules ON segments.sysmodule_id = modules.id
            WHERE modules.module = '$module' AND segments.active = 1 AND items.active = 1 AND items.deleted = 0 $syncwhere
            ORDER BY attribute_field, value_field
            ");
        while ($item = $db->fetchByAssoc($segmentitems)) {
            $rows[] = $item;
        }

        // build array
        $retArray = $this->buildFieldsArray($rows);
//        echo '<pre>'.print_r($retArray, true);
//        die();
        return $retArray[$module];

    }


    /**
     * buildFieldsArray
     *
     * Builds the array in the medata structure used to create exchange objects
     *
     * @param $elements
     * @param string $parentId
     * @return array
     */
    private function buildFieldsArray(&$elements, $parentId = '') {

        $branch = array();

        foreach ($elements as &$element) {

            if ($element['parent_id'] == $parentId) {
                $children = $this->buildFieldsArray($elements, $element['id']);
                if ($children) {
                    $element['value_field'] = $children[$element['module']][$element['exchange_segment']];
                }
                $branch[$element['module']][$element['exchange_segment']][$element['attribute_field']] = $element['value_field'];
                unset($element);
            }
        }
        return $branch;
    }


    /**
     * getFieldMapping
     *
     * retrieves configuration from database for outbound
     *
     * @return array
     */
    public function getFieldMapping(){
        return $this->getSegmentItems($this->spiceBean->module_name);
    }

    /**
     * getFieldMappingInbound
     *
     * retrieves configuration from database for inbound
     *
     * @return array
     */
    public function getFieldMappingEWSToBean(){
        return $this->getSegmentItems($this->spiceBean->module_name, 'inbound');
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function convertDateTimeToExchange($value){
        $dateTime = new DateTime($value, new DateTimeZone('UTC'));
        return $dateTime->format('c');
    }

    /**
     * Some chars like ' might be ascii encoded &#039;
     * Always apply html decode
     * @param $value
     * @return string
     */
    public static function convertTextToExchange($value){
        return html_entity_decode($value, ENT_QUOTES);
    }

    /**
     * @param $value
     * @return false|integer
     */
    public static function convertSecondsToMinutes($value){
        return round(($value/60));
    }

    /**
     * creates a new field item
     * @param $subtype
     * @return SetItemFieldType
     */
    public static function generateSetItemFieldType($subtype){
        $field = new SetItemFieldType();
        $field->FieldURI = new PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = $subtype;
        return $field;
    }


    /**
     * @param $value
     * @return string
     */
    public static function generateSetUnindexedField($value){
        return self::convertTextToExchange($value);
    }

    /**
     * @param $bodytype
     * @param $value
     */
    public static function generateBodyType($value, $bodytype){
        $body = new BodyType();
        $body->_ = self::convertTextToExchange($value);
        $body->BodyType = $bodytype;
        return $body;
    }

    /**
     * @param $itemField
     * @param $value
     * @return array
     */
    public static function generateReminder($itemField, $value){
        if($value > 0){
            $value = self::convertSecondsToMinutes($value);
        }
        else{
            $itemField = 'ReminderIsSet';
            $value = false;
        }

        return ['itemField' => $itemField, 'value' => $value];
    }

}
