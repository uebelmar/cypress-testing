<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\SugarQueue\SugarJobQueue;

/**
 * CRON driver for job queue
 * @api
 */
class SugarCronJobs
{
    /**
     * Max number of jobs per cron run
     * @var int
     */
    public $max_jobs = 10;
    /**
     * Max time per cron run
     * @var int
     */
    public $max_runtime = 60;
    /**
     * Min time between cron runs
     * @var int
     */
    public $min_interval = 30;

    /**
     * Lock file to ensure the jobs aren't run too fast
     * @var string
     */
    public $lockfile;

    /**
     * Currently running job
     * @var SchedulersJob
     */
    public $job;

    /**
     * Is current queue run OK?
     * @var bool
     */
    public $runOk = true;

    /**
     * Should the driver print reports to stdout?
     * @var bool
     */
    public $verbose = false;

    /**
     * This allows to disable schedulers cycle, e.g. for testing
     * @var bool
     */
    public $disable_schedulers = false;

    public function __construct()
    {
        $this->queue = new SugarJobQueue();
        $this->lockfile = sugar_cached("modules/Schedulers/lastrun");
        if(!empty(SpiceConfig::getInstance()->config['cron']['max_cron_jobs'])) {
            $this->max_jobs = SpiceConfig::getInstance()->config['cron']['max_cron_jobs'];
        }
        if(!empty(SpiceConfig::getInstance()->config['cron']['max_cron_runtime'])) {
            $this->max_runtime = SpiceConfig::getInstance()->config['cron']['max_cron_runtime'];
        }
        if(isset(SpiceConfig::getInstance()->config['cron']['min_cron_interval'])) {
            $this->min_interval = SpiceConfig::getInstance()->config['cron']['min_cron_interval'];
        }
    }

    /**
     * Remember last time it was run
     */
    protected function markLastRun()
    {
        if(!file_put_contents($this->lockfile, time())) {
            LoggerManager::getLogger()->fatal('Scheduler cannot write PID file.  Please check permissions on '.$this->lockfile);
        }
    }

    /**
     * Check if we aren't running jobs too frequently
     * @return bool OK to run?
     */
    public function throttle()
    {
        if($this->min_interval == 0) {
            return true;
        }
        create_cache_directory($this->lockfile);
        if(!file_exists($this->lockfile)) {
            $this->markLastRun();
            return true;
        } else {
            $ts = file_get_contents($this->lockfile);
            $this->markLastRun();
            $now = time();
            if($now - $ts < $this->min_interval) {
                // run too frequently
                return false;
            }
        }
        return true;
    }

    /**
     * What to do if one of the jobs failed
     * @param SchedulersJob $job
     */
    protected function jobFailed($job = null)
    {
        $this->runOk = false;
        if(!empty($job)) {
            LoggerManager::getLogger()->fatal("Job {$job->id} ({$job->name}) failed in CRON run");
            if($this->verbose) {
                printf(translate('ERR_JOB_FAILED_VERBOSE', 'SchedulersJobs'), $job->id, $job->name);
            }
        }
    }

    /**
     * Shutdown handler to be called if something breaks in the middle of the job
     */
    public function unexpectedExit()
    {
        if(!empty($this->job)) {
            $this->jobFailed($this->job);
            $this->job->failJob(translate('ERR_FAILED', 'SchedulersJobs'));
            $this->job = null;
        }
    }

    /**
     * Return ID for this client
     * @return string
     */
    public function getMyId()
    {
        return 'CRON'. SpiceConfig::getInstance()->config['unique_key'].':'.getmypid();
    }

    /**
     * Execute given job
     * @param SchedulersJob $job
     */
    public function executeJob($job)
    {
        if(!$this->job->runJob()) {
            // if some job fails, change run status
            $this->jobFailed($this->job);
        }
        // If the job produced a session, destroy it - we won't need it anymore
        if(session_id()) {
            session_destroy();
        }
    }

    /**
     * Run CRON cycle:
     * - cleanup
     * - schedule new jobs
     * - execute pending jobs
     */
    public function runCycle()
    {
        // throttle
        if(!$this->throttle()) {
            LoggerManager::getLogger()->fatal("Job runs too frequently, throttled to protect the system.");
            return;
        }
        // clean old stale jobs
        if(!$this->queue->cleanup()) {
            $this->jobFailed();
        }
        // run schedulers
        if(!$this->disable_schedulers) {
            $this->queue->runSchedulers();
        }
        // run jobs
        $cutoff = time()+$this->max_runtime;
        register_shutdown_function(array($this, "unexpectedExit"));
        $myid = $this->getMyId();
        for($count=0;$count<$this->max_jobs;$count++) {
            $this->job = $this->queue->nextJob($myid);
            if(empty($this->job)) {
                return;
            }
            $this->executeJob($this->job);
            if(time() >= $cutoff) {
                break;
            }
        }
        $this->job = null;
    }

    /**
     * Check if the queue run was fine
     */
    public function runOk()
    {
        return $this->runOk;
    }
}
