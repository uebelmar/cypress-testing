<?php
namespace SpiceCRM\includes\SpiceCRMGsuite\Mappings;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\modules\GoogleCalendar\GoogleCalendarEvent;
use SpiceCRM\data\SugarBean;

class SpiceCRMGsuiteMapping
{
    protected $spiceBean;

    public function __construct($bean) {
        $this->spiceBean = $bean;
    }

    /**
     * Retrieves field configuration from the database.
     *
     * @return mixed
     */
    public function getFieldMapping() {
        return $this->getSegmentItems($this->spiceBean->module_name);
    }

    /**
     * Retrieve all field configuration from database.
     * Build arrays per module.
     *
     * @param $module
     * @param string $syncdirection
     * @return mixed
     */
    public function getSegmentItems($module, $syncdirection = 'outbound') {
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

        return $retArray[$module];

    }

    /**
     * Builds the array in the metadata structure used to create Gsuite objects.
     *
     * @param $elements
     * @param string $parentId
     * @return array
     */
    private function buildFieldsArray(&$elements, $parentId = '') {
        $branch = [];

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
     * Applies a custom function to a Sugar Bean.
     *
     * @param SugarBean $bean
     * @param $customFunction
     * @param $direction
     * @return null
     */
    protected function applyCustomFunctionToBean(SugarBean &$bean, &$customFunction, $direction) {
        $customFunctionParts = $this->checkCustomFunction($customFunction, $direction);
        if ($customFunctionParts) {
            $class = $customFunctionParts['class'];
            $method = $customFunctionParts['method'];
            $fieldClass = new $class($bean);
            return $fieldClass->$method();
        }
        return null;
    }

    /**
     * Applies a custom function to a Google Event attribute.
     *
     * @param GoogleCalendarEvent $event
     * @param $customFunction
     * @param $direction
     * @return null
     */
    protected function applyCustomFunctionToEvent(GoogleCalendarEvent $event, &$customFunction, $direction) {
        $customFunctionParts = $this->checkCustomFunction($customFunction, $direction);
        if ($customFunctionParts) {
            $class = $customFunctionParts['class'];
            $method = $customFunctionParts['method'];
            $fieldClass = new $class($this->spiceBean);
            return $fieldClass->$method($event);
        }
        return null;
    }

    /**
     * todo check class existence
     *
     * @param $customFunction
     * @param $direction
     * @return array|bool
     */
    public function checkCustomFunction($customFunction, $direction) {
        // grab class & method name
        $parts = explode('->', $customFunction);
        if (count($parts) == 2 ) {
            $class = $parts[0];
            $method = $parts[1];
            if(method_exists($class, $method.'_'.$direction)){
                return ['class' => $class, 'method' => $method.'_'.$direction];
            }
        }
        return false;
    }
}
