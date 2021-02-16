<?php
/**
 * Created by PhpStorm.
 * User: maretval
 * Date: 10.10.2018
 * Time: 15:26
 */

//check on function_exists for backwards compatibility
//runScheduledKReports might already exist under custom/Extensions/modules/Schedulers/Ext...
if(!function_exists('runScheduledKReports')) {
    $job_strings[98] = 'runScheduledKReports';

    function runScheduledKReports()
    {
        require_once('modules/KReports/Plugins/Integration/kscheduling/kschedulingcronhandler.php');
        $kreportscheduler = new kschedulingcronhandler();
        $kreportscheduler->initializeScheduledReports();
        $kreportscheduler->runScheduledReports();
    }
}