<?php
namespace SpiceCRM\includes\SpiceCRMGsuite\ModuleHandlers;
use Exception;
use SpiceCRM\includes\SpiceCRMGsuite\Mappings\SpiceCRMGsuiteMappingEvent;
use SpiceCRM\modules\GoogleCalendar\GoogleCalendarEvent;
use SpiceCRM\data\SugarBean;
use DateTimeZone;
use DateTime;

abstract class SpiceCRMGsuiteEvents
{
    protected $spiceBean;

    public function __construct($bean) {
        $this->spiceBean = $bean;
    }

    /**
     * Creates an array with the necessary properties for the Google event create request.
     *
     * @return array
     */
    protected function createCreateArray() {
        $mapping = new SpiceCRMGsuiteMappingEvent($this->spiceBean);
        return $mapping->createCreateArray();
    }

    /**
     * Maps a Google event into a Sugar Bean.
     *
     * @param GoogleCalendarEvent $event
     */
    protected function mapToBean(GoogleCalendarEvent $event) {
        $mapping = new SpiceCRMGsuiteMappingEvent($this->spiceBean);
        return $mapping->mapToBean($event);
    }

    /**
     * Custom function for calculating the start date of an event.
     * Used in the conversion from a Sugar Bean into a Google Event.
     *
     * @return string
     */
    public function getStartDate_out() {
        return $this->getRfc3339Date($this->spiceBean->date_start);
    }

    /**
     * Custom function for calculating the end date of an event.
     * Used in the conversion from a Sugar Bean into a Google Event.
     *
     * @return string
     */
    public function getEndDate_out() {
        return $this->getRfc3339Date($this->spiceBean->date_end);
    }

    /**
     * Converts a date into the RFC3339 format used by Google Calendar.
     *
     * @param $date
     * @return string
     */
    private function getRfc3339Date($date) {
        $timeZone = new DateTimeZone('UTC');
        $convertedDate = DateTime::createFromFormat('Y-m-d H:i:s', $date, $timeZone);
        return $convertedDate->format(DateTime::RFC3339);
    }

    /**
     * Custom function for calculating the start date of an event.
     * Used in the conversion from a Google Event into a Sugar Bean.
     *
     * @param GoogleCalendarEvent $event
     * @return string
     */
    public function getStartDate_in(GoogleCalendarEvent $event) {
        return $this->getDbDateFormat($event->start->dateTime);
    }

    /**
     * Custom function for calculating the end date of an event.
     * Used in the conversion from a Google Event into a Sugar Bean.
     *
     * @param GoogleCalendarEvent $event
     * @return string
     */
    public function getEndDate_in(GoogleCalendarEvent $event) {
        return $this->getDbDateFormat($event->end->dateTime);
    }

    /**
     * Converts an RFC3339 format date into a database format date.
     *
     * @param $date
     * @return string
     * @throws Exception
     */
    private function getDbDateFormat($date) {
        global $timedate;
        $convertedDate = new DateTime($date);
        $convertedDate->setTimezone(new DateTimeZone('UTC'));
        return $convertedDate->format(($timedate->get_db_date_time_format()));
    }

    /**
     * Converts a Sugar Bean into a Google Calendar Event.
     *
     * @return GoogleCalendarEvent
     */
    public function beanToGsuite() {
        $fields = $this->createCreateArray();

        // todo move this class into include
        $event = new GoogleCalendarEvent($fields);

        return $event;
    }

    /**
     * Converts a Google Calendar Event into a Sugar Bean.
     *
     * @throws Exception
     */
    public function gsuiteToBean(GoogleCalendarEvent $event) {
        $this->mapToBean($event);

//        global $timedate;
//        $startDate = new \DateTime($event->start->dateTime);
//        $startDate->setTimezone(new DateTimeZone("UTC"));
//        $endDate = new \DateTime($event->end->dateTime);
//        $endDate->setTimezone(new DateTimeZone("UTC"));
//
//        $diff = $endDate->diff($startDate);
//
//        $this->spiceBean->duration_hours = $diff->h;
//        $this->spiceBean->duration_minutes = $diff->i;
        $this->spiceBean->save();
    }
}
