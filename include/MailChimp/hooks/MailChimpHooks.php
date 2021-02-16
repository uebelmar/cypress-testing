<?php

use SpiceCRM\includes\MailChimp\MailChimp;


/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
class MailChimpHooks
{
    public function handlerHooks(&$bean, $event, $arguments)
    {
        switch ($event) {
            case 'after_save':
                if ($bean->gdpr_marketing_agreement != 'g') {
                    $mailChimp = new MailChimp();
                    $mailChimp->deleteFromMailChimp($bean->id);
                } else {
                    $mailChimp = new MailChimp();
                    $mailChimp->contactToMailChimp($bean->id);
                }
                break;
            case 'before_delete':
                $mailChimp = new MailChimp();
                $mailChimp->deleteFromMailChimp($bean->id);
                break;
        }

    }

}
