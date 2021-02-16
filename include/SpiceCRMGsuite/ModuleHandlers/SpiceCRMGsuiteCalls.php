<?php
namespace SpiceCRM\includes\SpiceCRMGsuite\ModuleHandlers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\GoogleCalendar\GoogleCalendarEvent;

class SpiceCRMGsuiteCalls extends SpiceCRMGsuiteEvents
{
    /**
     * Override for the bean to gsuite mapping.
     * In case of a new Call, do not send it to gsuite if the status is "held".
     *
     * @return GoogleCalendarEvent|null
     */
    public function beanToGsuite()
    {
        if ($this->spiceBean->status == 'Held' && empty($this->spiceBean->external_id)) {
            return null;
        }

        return parent::beanToGsuite();
    }

    /**
     * Assembles the location string for the Google Calendar Event
     *
     * @return string
     */
    public function getEventLocation_out() {
        $contact = BeanFactory::getBean('Contacts', $this->spiceBean->contact_id);
        return $contact->full_name . ', ' . $contact->phone_work . ', ' . $contact->phone_mobile;
    }
}
