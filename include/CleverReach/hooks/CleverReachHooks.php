<?php

use SpiceCRM\includes\CleverReach\CleverReach;


/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
class CleverReachHooks
{
    public function handlerHooks(&$bean, $event, $arguments)
    {
        switch ($event) {
            case 'after_save':
                if ($bean->gdpr_marketing_agreement != 'g') {
                    $cleverReach = new CleverReach();
                    $cleverReach->deleteFromCleverReach($bean->id);
                } else {
                    $cleverReach = new CleverReach();
                    $cleverReach->contactToCleverReach($bean->id);
                }
                break;
            case 'before_delete':
                $cleverReach = new CleverReach();
                $cleverReach->deleteFromCleverReach($bean->id);
                break;
//            case 'after_retrieve':
//                $cleverReach = new \SpiceCRM\includes\CleverReach\CleverReach();
//                $cleverReach->getUpdatesFromCleverReach($bean);
//                break;
        }

    }

}
