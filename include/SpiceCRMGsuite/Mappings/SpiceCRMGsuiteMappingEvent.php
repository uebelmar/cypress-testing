<?php
namespace SpiceCRM\includes\SpiceCRMGsuite\Mappings;

use SpiceCRM\modules\GoogleCalendar\GoogleCalendarEvent;

class SpiceCRMGsuiteMappingEvent extends SpiceCRMGsuiteMapping
{
    /**
     * Creates an array with the necessary properties for the Google event create request.
     *
     * @return array
     */
    public function createCreateArray() {
        $fields = $this->getFieldMapping();

        $retArray = $this->getFieldValues($fields);

        return $retArray;
    }

    /**
     * Maps a Google event into a Sugar Bean.
     *
     * @param GoogleCalendarEvent $event
     */
    public function mapToBean(GoogleCalendarEvent $event) {
        $fields = $this->getFieldMapping();

        $this->getBeanValues($event, $fields);
    }

    /**
     * Fills out the Bean attributes with values from a Google event.
     *
     * @param GoogleCalendarEvent $event
     * @param $fields
     */
    private function getBeanValues(GoogleCalendarEvent $event, $fields) {
        foreach ($fields as $key => $field) {
            if (isset($field['beanField'])) {
                $beanFieldValue = $event->{$key};
            }

            // apply customFunction to value if defined so
            if(isset($field['customFunction'])){
                $beanFieldValue = $this->applyCustomFunctionToEvent(
                    $event,
                    $field['customFunction'],
                    'in'
                );
            }

            if (isset($field['fields'])) {
                $beanFieldValue = $this->getBeanValues($event, $field['fields']);
            }

            // skip empty fields
            if (empty($beanFieldValue)) {
                continue;
            }

            switch ($field['type']) {
                default:
                    $this->spiceBean->{$field['beanField']} = $beanFieldValue;
                    break;
            }
        }
    }

    /**
     * Fills out Google event attributes with values from a Sugar Bean.
     *
     * @param $fields
     * @return array
     */
    private function getFieldValues($fields) {
        $retArray= [];

        foreach ($fields as $key => $field) {
            // store bean value
            if (isset($field['beanField'])) {
                $beanFieldValue = $this->spiceBean->{$field['beanField']};
            }

            // apply customFunction to value if defined so
            if(isset($field['customFunction'])){
                $beanFieldValue = $this->applyCustomFunctionToBean(
                    $this->spiceBean,
                    $field['customFunction'],
                    'out'
                );
            }

            if (isset($field['fields'])) {
                $beanFieldValue = $this->getFieldValues($field['fields']);
            }

            // skip empty fields
            if (empty($beanFieldValue)) {
                continue;
            }

            switch ($field['type']) {
                default:
                    $retArray[$key] = $beanFieldValue;
                    break;
            }
        }

        return $retArray;
    }
}
