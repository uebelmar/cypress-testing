<?php
namespace SpiceCRM\modules\ServiceOrders\mailboxprocessors;

use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Emails\Email;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\Mailboxes\processors\Processor;

class OrderMailboxAssignProcessor extends Processor
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
    private $order;

    /**
     * the pattern to match for finding a ticekt number
     *
     * @var mixed|string
     */
    private $pattern;

    public function __construct(Email $email)
    {
        

        // get the pattern from the config
        $this->pattern = SpiceConfig::getInstance()->config['servicemanagement']['ordernumberpattern'] ?: '/0002[0-9]{6}/';

        parent::__construct($email);
    }

    /**
     * run the assignment
     *
     * @return bool
     */
    public function process()
    {
        if ($this->loadOrder()) {
            $this->updateOrder();
            return true;
        } // else nothing happens with no contact
        return false;
    }

    /**
     * updateOrder
     *
     * assigned the email and Updates the status of the order
     */
    private function updateOrder()
    {
        // update the email and link it to the ticket
        $this->email->parent_type = 'ServiceOrders';
        $this->email->parent_id = $this->order->id;
        $this->email->openness = 'system_closed';
        $this->email->status = 'unread';
        $this->email->save(false, true);

        // set the notification status and resave the ticket
        $this->order->save();
    }


    /**
     * findOrderNumber
     *
     * Parses the subject and body of the email in search for valid order numbers
     *
     * @return null|string
     */
    private function loadOrder()
    {
        $this->order = BeanFactory::getBean('ServiceOrders');
        if (preg_match($this->pattern, $this->email->name, $matches)) {
            foreach ($matches as $match) {
                if ($this->order->retrieve_by_string_fields(['serviceorder_number' => $match])) {
                    return true;
                }
            }
        }

        if (preg_match($this->pattern, $this->email->body, $matches)) {
            foreach ($matches as $match) {
                if ($this->order->retrieve_by_string_fields(['serviceorder_number' => $match])) {
                    return true;
                }
            }
        }

        // set the ticket to null
        $this->order = null;

        // return false
        return false;
    }
}
