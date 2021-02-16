<?php

namespace SpiceCRM\includes\SpiceCRMExchange\Connectivity;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\LogicHook\LogicHook;

class SpiceCRMExchangeReSync
{

    /**
     * The function processing re-synchronization of calendar items
     * according to start - end date
     */
    public function processReSynchronizations()
    {
        $jobs = $this->getJobs();
        foreach($jobs as $job){
            $beans = $this->getCalendarBeans($job['module'], $job['user_id'], $job['date_start']);
            $this->resyncCalendarBeans($beans);
        }
    }

    public function getJobs($limit = -1){
        $jobs = [];
        $q = "SELECT jobs.*, sysm.module FROM sysexchangeuserresyncjobs jobs 
                INNER JOIN (select * from sysmodules UNION select * from syscustommodules) sysm ON sysm.id = jobs.sysmodule_id
                WHERE job_done = 0 ORDER BY date_modified ASC LIMIT $limit";
        if($res = DBManagerFactory::getInstance()->query($q)){
            while($row = DBManagerFactory::getInstance()->fetchByAssoc($res)){
                $jobs[] = $row;
            }
        }
        return $jobs;
    }

    public function getCalendarBeans($module, $user_id, $date_start){
        $bean = BeanFactory::getBean($module);
        $where = "assigned_user_id = '{$user_id}' AND date_start >= '{$date_start}'";
        $beans = $bean->get_full_list("", $where);
        return $beans;
    }

    public function resyncCalendarBeans($beans){
        foreach($beans as $bean){
            // trigger exchange hook?

            // get hooks
            $logichook = new LogicHook();
            $logichook->setBean($bean);
            $hooks = $logichook->getHooks('Calls');
            $process_hooks = [];
            foreach($hooks['before_save'] as $exchangehook){
                if(array_search('updateExchange', $exchangehook)) {
                    $process_hooks['before_save'][] = $exchangehook;
                }
            }

            if(empty($bean->external_id)){
                // create
                $logichook->process_hooks($process_hooks, 'before_save');
                $bean->processed = true;
                $bean->save(false);
            } else{
                // udpate
                $logichook->process_hooks($process_hooks, 'before_save');
            }
        }
    }

}
