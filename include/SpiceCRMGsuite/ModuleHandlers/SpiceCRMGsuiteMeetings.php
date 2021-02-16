<?php
namespace SpiceCRM\includes\SpiceCRMGsuite\ModuleHandlers;

use SpiceCRM\data\BeanFactory;

class SpiceCRMGsuiteMeetings extends SpiceCRMGsuiteEvents
{
    /**
     * Assembles the location string for the Google Calendar Event
     *
     * @return string
     */
    public function getEventSummary_out() {
        if(!empty($this->spiceBean->parent_id)){
            $parent = BeanFactory::getBean($this->spiceBean->parent_type, $this->spiceBean->parent_id);
            return "[{$parent->get_summary_text()}] {$this->spiceBean->name}";
        } else {
            $this->spiceBean->name;
        }
    }

    /**
     * Assembles the location string for the Google Calendar Event
     *
     * @return string
     */
    public function getEventSummary_in($event) {
        if(substr($event->summary, 0, 1) == '['){
            $end = strpos($event->summary, ']');
            if($end){
                $this->spiceBean->name = trim(substr($event->summary, $end + 1));
            }
        } else {
            $this->spiceBean->name = $this->event->summary;
        }
    }
}
