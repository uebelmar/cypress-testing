<?php

use SpiceCRM\includes\DialogMail\DialogMail;


/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

class DialogMailHooks
{
    public function handlerHooks(&$bean, $event, $arguments)
    {
        switch($event){
            case 'after_save':
                if ($bean->gdpr_marketing_agreement != 'g') return;
                $dialogMail = new DialogMail();
                $dialogMail->contactToDialogMail($bean->id);
                break;
            case 'after_delete':
                break;
        }

    }

}
