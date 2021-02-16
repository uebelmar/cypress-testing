<?php
namespace SpiceCRM\modules\GoogleCalendar;

use DateTime;
use Exception;
use ReflectionException;

class GoogleCalendarRestHandler {

    /**
     * GoogleCalendarRestHandler constructor.
     */
    public function __construct() {
        // todo check if logged in with google account
    }

    /**
     * saveBeanMappings
     *
     * Saves the Bean to Google Calendar mapping list.
     *
     * @param $params
     * @throws Exception
     */
    public function saveBeanMappings($params) {
        $gsuiteConfig = GSuiteUserConfig::getCurrentUserConfig();
        $gsuiteConfig->saveBeanMappings($params['bean_mappings']);
    }

    /**
     * getBeans
     *
     * Returns all Beans that implement the GoogleCalendarEvent Interface.
     *
     * @return array
     * @throws ReflectionException
     */
    public function getBeans() {
        $implementations = GoogleCalendar::getEventImplementations();

        if (empty($implementations)) {
            $response['result'] = false;
        } else {
            $response = [
                'result' => true,
                'beans'  => $implementations,
            ];
        }

        return $response;
    }

    /**
     * getCalendars
     *
     * Returns a list of all calendars for the current user's Google Calendar Account.
     *
     * @param $params
     * @return array
     * @throws Exception
     */
    public function getCalendars($params) {
        $calendar  = new GoogleCalendar();
        $calendars = $calendar->getAllCalendars();

        if (empty($calendars)) {
            $response['result'] = false;
        } else {
            $response = [
                'result'    => true,
                'calendars' => $calendars,
            ];
        }

        return $response;
    }

    /**
     * getBeanMappings
     *
     * Returns a list of Bean mappings, that is which Bean is being saved as en event in which Google Calendar.
     *
     * @param $params
     * @return array
     * @throws Exception
     */
    public function getBeanMappings($params) {
        $gsuiteConfig = GSuiteUserConfig::getCurrentUserConfig();
        $mappings = $gsuiteConfig->beanMappings;

        if (empty($mappings)) {
            $response = [
                'result' => false,
            ];
        } else {
            $response = [
                'result'        => true,
                'bean_mappings' => $mappings,
            ];
        }

        return $response;
    }

    /**
     * synchronize
     *
     * Synchronizes the Events between SpiceCRM and Google Calendar.
     *
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function synchronize($params) {
        $calendar = new GoogleCalendar();
        $result   = $calendar->synchronize();
        return $result;
    }

    public function startSync() {
        $calendar = new GoogleCalendar();
        try {
            $result   = $calendar->startSync();
            return $result;
        } catch (Exception $e) {
            return $e;
        }

    }

    public function updateFromGcal($params) {

    }

    /**
     * getGoogleEvents
     *
     * Returns all Events from Google Calendar for a given time period.
     * By default it removes duplicates that are already present in the Spice DB.
     * In order not to remove the duplicates remove_duplicates parameter should be set to false.
     *
     * @param $params
     * @return array
     */
    public function getGoogleEvents($params) {
        $startDate = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            date('Y-m-01 00:00:00')
        );
        if (isset($params['startdate'])) {
            $startDate = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                date('Y-m-d 00:00:00', strtotime(urldecode($params['startdate'])))
            );
        }

        $endDate = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            date('Y-m-t 23:59:59')
        );
        if (isset($params['enddate'])) {
            $endDate = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                date('Y-m-d 23:59:59', strtotime(urldecode($params['enddate'])))
            );
        }

        $removeDuplicates = true;
        if (isset($params['remove_duplicates'])) {
            $removeDuplicates = (bool) $params['remove_duplicates'];
        }

        $calendar = new GoogleCalendar();

        try {
            $results = $calendar->getGoogleEvents($startDate, $endDate);

            if ($removeDuplicates) {
                $results = $calendar->removeDuplicates($results);
            }

            return [
                'result' => true,
                'events' => $results,
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }
}
