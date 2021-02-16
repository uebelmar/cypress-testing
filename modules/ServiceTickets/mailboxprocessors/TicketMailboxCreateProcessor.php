<?php
namespace SpiceCRM\modules\ServiceTickets\mailboxprocessors;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\Emails\Email;
use SpiceCRM\modules\Mailboxes\processors\Processor;

class TicketMailboxCreateProcessor extends Processor
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


    public function __construct(Email $email)
    {
        parent::__construct($email);
    }

    /**
     * run the assignment
     *
     * @return bool
     */
    public function process()
    {
        if ($this->hasContact()) {
            $this->createTicket();
            return true;
        }
        return false;
    }

    /**
     * createTicket
     *
     * Creates a new ticket and assigns it to the email
     */
    private function createTicket()
    {
        $this->ticket = BeanFactory::newBean('ServiceTickets');
        $this->ticket->serviceticket_status = 'New';
        $this->ticket->name = $this->email->name;
        $this->ticket->description = $this->email->body;
        $this->ticket->contact_id = $this->contact->id;
        $this->ticket->account_id = $this->contact->account_id;
        $this->ticket->id = create_guid();
        $this->ticket->new_with_id = true;

        // update the email and link it to the ticket
        $this->email->parent_type = 'ServiceTickets';
        $this->email->parent_id = $this->ticket->id;
        $this->email->openness = 'system_closed';
        $this->email->status = 'unread';
        $this->email->save(false, true);

        // save the ticket
        $this->ticket->save();
    }

    /**
     * hasContact
     *
     * Checks if there is any contact linked to the email
     *
     * @return bool
     */
    private function hasContact()
    {
        $contacts = $this->email->get_linked_beans('contacts', 'Contact');
        if (count($contacts) > 0) {
            $this->contact = $contacts[0];
            return true;
        }
        return false;
    }


}
