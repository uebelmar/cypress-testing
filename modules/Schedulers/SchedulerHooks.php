<?php

use SpiceCRM\includes\database\DBManagerFactory;

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

class SchedulerHooks {

    function getTimeOfLastSuccessfulRun( &$bean, $event, $arguments ) {
        $db = DBManagerFactory::getInstance();

        $bean->last_successful_run = $db->getOne('SELECT execute_time FROM job_queue jq WHERE scheduler_id = "'.$db->quote( $bean->id ).'" AND status = "done" AND resolution = "success" AND deleted = 0 ORDER BY execute_time DESC LIMIT 1');
        return true;
    }

}
