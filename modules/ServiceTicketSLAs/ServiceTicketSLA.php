<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\ServiceTicketSLAs;

use SpiceCRM\data\SugarBean;

class ServiceTicketSLA extends SugarBean
{
    public $module_dir = 'ServiceTicketSLAs';
    public $object_name = 'ServiceTicketSLA';
    public $table_name = 'serviceticketslas';

    public $startDate;

    var $respond_until;
    var $resolve_until;

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function determineSLAforTicket(&$ticket)
    {
        $this->startDate = $ticket->date_entered ?: '';

        // ToDo: build rules for determination properly
        $this->retrieve_by_string_fields(['is_default' => 1]);
        $ticket->serviceticketsla_id = $this->id;
        $ticket->serviceticketsla_name = $this->name;

        // determine times and set them as well
        $this->determineSLADates($ticket->serviceticket_type, $ticket->serviceticket_class);
        if($this->resolve_until) $ticket->resolve_until = $this->resolve_until;
        if($this->respond_until) $ticket->respond_until = $this->respond_until;

        return $this;
    }

    public function determineSLADates($serviceticket_type, $serviceticket_class)
    {
        global $timedate;

        // get the candidates for the time set
        $this->load_relationship('serviceticketslatimes');
        $linkedTime = $this->get_linked_beans('serviceticketslatimes', 'ServiceTicketSLATime',[], 0, -1, 0, "serviceticket_type='$serviceticket_type' AND serviceticket_class='$serviceticket_class'");
        if(count($linkedTime) == 0){
            $linkedTime = $this->get_linked_beans('serviceticketslatimes', 'ServiceTicketSLATime',[], 0, -1, 0, "serviceticket_type='*' AND serviceticket_class='*'");
        }

        if(count($linkedTime) > 0){
            $slaTime = $linkedTime[0];

            $this->respond_until = $slaTime->timeToResponse($this->startDate, $this->servicecalendar_id);
            $this->resolve_until = $slaTime->timeToResolution($this->startDate, $this->servicecalendar_id);
        }
    }
}
