<?php
namespace SpiceCRM\modules\ServiceTickets\mailboxprocessors;

use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Emails\Email;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\Mailboxes\processors\Processor;

class TicketMailboxAssignProcessor extends Processor
{
    /**
     *
     * the contact found
     * @var
     */
    private $contact;

    /**
     * the tieckt either created or matched
     * @var
     */
    private $ticket;

    /**
     * the pattern to match for finding a ticekt number
     *
     * @var mixed|string
     */
    private $pattern;

    public function __construct(Email $email)
    {
        

        // get the pattern from the config
        $this->pattern = SpiceConfig::getInstance()->config['servicemanagement']['ticketnumberpattern'] ?: '/[0-9]{10}/';

        parent::__construct($email);
    }

    /**
     * run the assignment
     *
     * @return bool
     */
    public function process()
    {
        if ($this->loadTicket()) {
            $this->updateTicket();
            return true;
        }
        return false;
    }

    /**
     * updateTicket
     *
     * Updates the status of the ticket
     */
    private function updateTicket()
    {
        // update the email and link it to the ticket
        $this->email->parent_type = 'ServiceTickets';
        $this->email->parent_id = $this->ticket->id;
        $this->email->openness = 'system_closed';
        $this->email->status = 'unread';
        $this->email->save(false, true);

        // set the notification status and resave the ticket
        $this->ticket->save();
    }

    /**
     * findTicketNumber
     *
     * Parses the subject and body of the email in search for valid ticket numbers
     *
     * @return null|string
     */
    private function loadTicket()
    {
        $this->ticket = BeanFactory::getBean('ServiceTickets');
        if (preg_match($this->pattern, $this->email->name, $matches)) {
            foreach ($matches as $match) {
                if ($this->ticket->retrieve_by_string_fields(['serviceticket_number' => $match])) {
                    return true;
                }
            }
        }

        if (preg_match($this->pattern, $this->email->body, $matches)) {
            foreach ($matches as $match) {
                if ($this->ticket->retrieve_by_string_fields(['serviceticket_number' => $match])) {
                    return true;
                }
            }
        }

        // set the ticket to null
        $this->ticket = null;

        // return false
        return false;
    }
}
