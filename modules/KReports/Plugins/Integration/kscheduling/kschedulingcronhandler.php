<?php
/* * *******************************************************************************
* This file is part of KReporter. KReporter is an enhancement developed
* by aac services k.s.. All rights are (c) 2016 by aac services k.s.
*
* This Version of the KReporter is licensed software and may only be used in
* alignment with the License Agreement received with this Software.
* This Software is copyrighted and may not be further distributed without
* witten consent of aac services k.s.
*
* You can contact us at info@kreporter.org
******************************************************************************* */


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\KReports\KReport;

require_once('modules/KReports/KReport.php');

// the export helpers
if (file_exists('modules/KReports/Plugins/Integration/kexcelexport/kexcelexport.php'))
    require_once('modules/KReports/Plugins/Integration/kexcelexport/kexcelexport.php');

// the export helpers
if (file_exists('modules/KReports/Plugins/Integration/kpdfexport/kpdfexport.php'))
    require_once('modules/KReports/Plugins/Integration/kpdfexport/kpdfexport.php');


// include the mailer
//require_once('include/SugarPHPMailer.php');

// for the cron handling
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/FieldInterface.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/AbstractField.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/DayOfMonthField.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/DayOfWeekField.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/HoursField.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/MinutesField.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/MonthField.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/YearField.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/FieldFactory.php');
require_once ('modules/KReports/Plugins/Integration/kscheduling/cron/CronExpression.php');

class kschedulingcronhandler {

    // queries all reports with schedules and sees if a planned entry is in the table ...
    // if not creates an entry for the scheduler
    // or delete an entry
    public function initializeScheduledReports() {
        $db = DBManagerFactory::getInstance();
        
        // get all new
        $scheduledReportsObj = $db->query("SELECT * FROM kreports WHERE (integration_params like '%\"kscheduling\":\"1\"%' OR integration_params like '%\"kscheduling\":1%') AND deleted = 0");
        while ($thisReport = $db->fetchByAssoc($scheduledReportsObj)) {
            $integrationparams = json_decode(html_entity_decode($thisReport['integration_params'], ENT_QUOTES), true);
            foreach ($integrationparams['kscheduling'] as $schedulerIndex => $schedulerData) {
                if ($db->getRowCount($db->query("SELECT * FROM kreportschedulers WHERE report_id='" . $thisReport['id'] . "' AND job_id = '" . $schedulerData['schedulerid'] . "' AND status='P'")) == 0) {
                    $this->setNextSchedulerDate($thisReport['id'], $schedulerData);
                }
            }
        }
        
        // remove all that have been disabled but are still scheduled .. reduces load
        $scheduledReportsObj = $db->query("SELECT kreportschedulers.* FROM kreports INNER JOIN kreportschedulers ON kreports.id = kreportschedulers.report_id WHERE (integration_params like '%\"kscheduling\":\"0\"%' OR integration_params like '%\"kscheduling\":0%') AND kreportschedulers.status='P' AND deleted = 0");
        while ($thisReportSchedule = $db->fetchByAssoc($scheduledReportsObj)) {
            $db->query("UPDATE kreportschedulers SET status='X' WHERE id='" . $thisReportSchedule['id'] . "'");
        }

    }

    // runs all currently overdue reports
    public function runScheduledReports() {
        $db = DBManagerFactory::getInstance();

        $schedulers = array();

        $thisReport = new KReport();

        //2013-01-12 respect current system settings re timezone
        //$now = new DateTime('now', new DateTimeZone('UTC'));
        $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));


        // get all overdue entries
        $scheduledJobjsObj = $db->query("SELECT * FROM kreportschedulers WHERE status = 'P' AND timestamp <= '" . $now->format('Y-m-d H:i:s') . "' ORDER BY report_id");
        while ($thisScheduledJob = $db->fetchByAssoc($scheduledJobjsObj)) {

            // get the report if the id has changed
            if ($thisReport->id != $thisScheduledJob['report_id']) {
                $thisReport->retrieve($thisScheduledJob['report_id']);

                // get the scheduler lines
                $integrationparams = json_decode(html_entity_decode($thisReport->integration_params, ENT_QUOTES), true);
                // check if the scheduler is active
                if ($integrationparams['activePlugins']['kscheduling'] == '1')
                {
                    foreach ($integrationparams['kscheduling'] as $schedulerIndex => $schedulerData) {
                        $schedulers[$schedulerData['schedulerid']] = $schedulerData;
                    }
                }
            }

            if (!empty($schedulers[$thisScheduledJob['job_id']]) && $schedulers[$thisScheduledJob['job_id']]['scheduleraction'] != '' && method_exists($this, $schedulers[$thisScheduledJob['job_id']]['scheduleraction'])) {

                // execute
                // $this->sendMail('info@kreporter.org', $this->{$thisScheduledJob['scheduleraction']}());
                // $this->{$thisScheduledJob['scheduleraction']}($thisReport);
                $this->{$schedulers[$thisScheduledJob['job_id']]['scheduleraction']}($thisScheduledJob, $schedulers[$thisScheduledJob['job_id']]);
                $this->setSchedulerStatus($thisScheduledJob['id'], 'X');
                
                // 2013-06-25 do not set the scheduler dat .. done anyway automatically.
                //$this->setNextSchedulerDate($thisScheduledJob['report_id'], $schedulers[$thisScheduledJob['job_id']]);
            }
            else
                $this->setSchedulerStatus($thisScheduledJob['id'], 'D');
        }
    }

    private function CSV($jobData, $schedulerData) {
        $thisReport = new KReport();
        $thisReport->retrieve($jobData['report_id']);
        if (!empty($schedulerData['schedulersendto']))
            $this->sendMail($jobData['report_id'], $schedulerData['schedulersendto'], $thisReport->createCSV(), 'csv');
        if (!empty($schedulerData['schedulersendlist'])){
            $recipients = $this->getSchedulersendlistRecipients($schedulerData['schedulersendlist']);
            $this->sendMail($jobData['report_id'], implode(",", $recipients), $thisReport->createCSV(), 'csv');
        }
        if (!empty($schedulerData['schedulersavetoaction']))
            $this->saveFile($jobData['report_id'], $schedulerData, $thisReport->createCSV(), 'csv');
    }

    private function EXCEL($jobData, $schedulerData) {
        $exporter = new kexcelexport();
        if (!empty($schedulerData['schedulersendto']))
            $this->sendMail($jobData['report_id'], $schedulerData['schedulersendto'], $exporter->exportToExcel($jobData['report_id']), 'xlsx');
        if (!empty($schedulerData['schedulersendlist'])){
            $recipients = $this->getSchedulersendlistRecipients($schedulerData['schedulersendlist']);
            $this->sendMail($jobData['report_id'], implode(",", $recipients), $exporter->exportToExcel($jobData['report_id']), 'xlsx');
        }
        if (!empty($schedulerData['schedulersavetoaction']))
            $this->saveFile($jobData['report_id'], $schedulerData, $exporter->exportToExcel($jobData['report_id']), 'xlsx');
    }

    private function PDF($jobData, $schedulerData) {
        $exporter = new kpdfexport();
        $thisReport = new KReport();
        $thisReport->retrieve($jobData['report_id']);
        if (!empty($schedulerData['schedulersendto']))
            $this->sendMail($jobData['report_id'], $schedulerData['schedulersendto'], $exporter->exportToPDF($thisReport), 'pdf');
        if (!empty($schedulerData['schedulersendlist'])){
            $recipients = $this->getSchedulersendlistRecipients($schedulerData['schedulersendlist']);
            $this->sendMail($jobData['report_id'], implode(",", $recipients), $exporter->exportToPDF($thisReport), 'pdf');
        }
        if (!empty($schedulerData['schedulersavetoaction']))
            $this->saveFile($jobData['report_id'], $schedulerData, $exporter->exportToPDF($thisReport), 'pdf');
    }

    private function SNAPSHOT($jobData, $schedulerData) {
        $thisReport = new KReport();
        $thisReport->retrieve($jobData['report_id']);
        $thisReport->takeSnapshot();
    }

    private function TARGETLIST($jobData, $schedulerData) {
        if (file_exists('modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlisthandler.php')) {
            require_once('modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlisthandler.php');

            $thisReport = new KReport();
            $thisReport->retrieve($jobData['report_id']);

            $integrationsettings = json_decode(html_entity_decode($thisReport->integration_params));

            if ($integrationsettings->activePlugins->ktargetlistexport == 1) {
                // initiate the handler
                $thisTargetListHandler = new KReportTargetListHandler($thisReport, ($integrationsettings->ktargetlistexport->targetlist_create_direct == true ? false : true));

                $thisTargetListHandler->handle_update_request($integrationsettings->ktargetlistexport->targetlist_update_action, $integrationsettings->ktargetlistexport->targetlist_id, $integrationsettings->ktargetlistexport->targetlist_create_direct);
            }
        }
    }

    private function getSchedulersendlistRecipients($dlistid){
        require_once 'modules/KReports/KReportRESTHandler.php';
        $recipients = array();
        if(!empty($dlistid)){
            $krest = new KReporterRESTHandler();
            $dlistEntry = $krest->getDList($dlistid);
            $dlistdata = json_decode(html_entity_decode($dlistEntry[0]['dlistdata'], ENT_QUOTES, 'UTF-8'));

            if(isset($dlistdata->users) && !empty($dlistdata->users)){
                $params = array("userids" => json_encode($dlistdata->users));
                $users = $krest->getUsers($params);

                if(!empty($users)){
                    foreach($users as $user){
                        if(!empty($user['email1']) && !in_array($user['email1'], $recipients))
                            $recipients[] = $user['email1'];
                    }
                }
            }
            
            if(isset($dlistdata->contacts) && !empty($dlistdata->contacts)){
                $params = array("contactids" => json_encode($dlistdata->contacts));
                $contacts = $krest->getContacts($params);

                if(!empty($contacts)){
                    foreach($contacts as $contact){
                        if(!empty($contact['email1']) && !in_array($contact['email1'], $recipients))
                            $recipients[] = $contact['email1'];
                    }
                }
            }

            if(isset($dlistdata->kreports) && !empty($dlistdata->kreports)){
                $params = array("kreportids" => json_encode($dlistdata->kreports));
                $kreports = $krest->getKReports($params);

                if(!empty($kreports)){
                    foreach($kreports as $kreport){
                        $krep = BeanFactory::getBean('KReports', $kreport['id']);
                        $krep_results = $krep->getSelectionResults(array());
                        
                        if(is_array($krep_results)){
                            foreach($krep_results as $idx => $entry){
                                $record = BeanFactory::getBean($entry['sugarRecordModule'], $entry['sugarRecordId']);
                                if(empty($record->email1)){
                                    $emails = $record->get_linked_beans('email_addresses_primary', 'EmailAddress');      
                                    if(!empty($emails))
                                        if(!in_array($emails[0]->email_address, $recipients))
                                            $recipients = $emails[0]->email_address;
                                }else{
                                    if(!in_array($record->email1, $recipients))
                                        $recipients[] = $record->email1;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $recipients;
    }
    
    private function sendMail($reportId, $recipients, $attachement, $attachementType) {

       
       
        // load the report and build a display_link
        $thisReport = new KReport();
        $thisReport->retrieve($reportId);
        $thisReport->displaylink = SpiceConfig::getInstance()->config['site_url'] . '/index.php?module=KReports&action=DetailView&record=' . $reportId;

        // initialize mailer
//        $email = new SugarPHPMailer ();
//        $email->setMailer();
        $defaultMailbox = Mailbox::getDefaultMailbox();
        $defaultMailboxSettings = json_decode(html_entity_decode($defaultMailbox->settings, ENT_QUOTES, 'UTF-8'));
        $email = BeanFactory::getBean( 'Emails' );
        $email->mailbox_id = $defaultMailbox->id;

        // handle email addresses
        if (strpos($recipients, ',') !== false)
            $addressArray = explode(',', $recipients);
        elseif (strpos($recipients, ';') !== false)
            $addressArray = explode(';', $recipients);
        else
            $addressArray [] = $recipients;

        // add all addresses
        foreach ($addressArray as $thisEmailAddress) {
            // trim whitespaces
            $thisEmailAddress = trim($thisEmailAddress);
            // add the address
//            $email->addAddress($thisEmailAddress);
            $email->addEmailAddress( 'to', $thisEmailAddress );
        }

        // add the from address
        $email->addEmailAddress( 'from', $defaultMailboxSettings->imap_pop3_username );

        // add content
        $email->name = $thisReport->name;
        $email->body = $thisReport->name;

        // @todo: see if you can define and retrieve a Spice e-mail template
        $kreportEmailTemplate = SpiceConfig::getInstance()->config['KReports']['emailTemplate'];
//        if ($kreportEmailTemplate != '') {
//            $template = new EmailTemplate ();
//            $template->retrieve($kreportEmailTemplate);
//            $email->Subject = $template->subject; //$emailTemplateDetail['subject'];
//            $email->Body = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>' . html_entity_decode($template->body_html);
//            $email->Body = str_replace(array('%7B', '%7D', '%7B&', '%7D&'), array('{', '}', '{', '}'), $email->Body);
//
//            $email->isHTML(true);
//        } else {
//            $email->Subject = $kreportEmailSubject != '' ? $kreportEmailSubject : 'KReports Scheduler';
//            $email->Body = $kreportEmailBody != '' ? $kreportEmailBody : $thisReport->name;
//        }

        // replace all variables in teh Subject
//        preg_match_all('/\{(kreport_[\s\S]*?)\}/', $email->Subject, $kreportMatches);
//        foreach ($kreportMatches[1] as $matchId => $thisMatch) {
//            $matchArray = explode('_', $thisMatch, 2);
//            if (property_exists($thisReport, $matchArray[1]))
//                $email->Subject = str_replace($kreportMatches[0][$matchId], $thisReport->{$matchArray[1]}, $email->Subject);
//        }
//
//         // replace all variables in the Body
//        preg_match_all('/\{(kreport_[\s\S]*?)\}/', $email->Body, $kreportMatches);
//        foreach ($kreportMatches[1] as $matchId => $thisMatch) {
//            $matchArray = explode('_', $thisMatch, 2);
//            if (property_exists($thisReport, $matchArray[1]))
//                $email->Body = str_replace($kreportMatches[0][$matchId], $thisReport->{$matchArray[1]}, $email->Body);
//        }

        // get the base email settings for the system
//        $emailSettingsRes = $GLOBALS ['db']->query("SELECT * FROM config WHERE category='notify' AND name like 'from%'");
//        while ($emailDetails = $GLOBALS ['db']->fetchByAssoc($emailSettingsRes)) {
//            switch ($emailDetails ['name']) {
//                case 'fromname' :
//                    $email->FromName = $emailDetails ['value'];
//                    break;
//                case 'fromaddress' :
//                    $email->From = $emailDetails ['value'];
//                    break;
//            }
//        }

        // add the senders
//        $email->Sender = $email->From;
//        $email->addReplyTo($email->From, $email->FromName);

        // add the Attachement
//        $email->addStringAttachment($attachement, 'kreport.' . $attachementType);

        // @todo: ADD THE ATTACHMENT on the fly! phpmailer no longer in use!

        // save e-mail and content to spiceattachments for now. THen use regular sendEmail() method
        $email->id = create_guid();
        $email->parent_id = $thisReport->id;
        $email->parent_type = $thisReport->module_dir;
        if(!$email->save(false)){
            LoggerManager::getLogger()->fatal('KReporter sending email failed with error: could not save Email on file '.__FILE__.', line '.__LINE__.'.');
        }

        $fileArray = [
            'filename' => 'kreport_'.date('Y-m-d_H-i-s').'.'.$attachementType,
            'file' => base64_encode($attachement),
            'filemimetype' => NULL // use $attachementType in saveAttachmentHashFiles
        ];
        $attached = SpiceCRM\includes\SpiceAttachments\SpiceAttachments::saveAttachmentHashFiles('Emails', $email->id, $fileArray);


        // send the email
        $sendResults = $email->sendEmail();
        if ( isset( $sendResults['errors'] ) ) {
            LoggerManager::getLogger()->fatal('KReporter sending email failed with error: Mailbox on file '.__FILE__.', line '.__LINE__.'.');
            LoggerManager::getLogger()->fatal( $sendResults );
        }
//        if(!$email->send())
//            \SpiceCRM\includes\Logger\LoggerManager::getLogger()->fatal('KReporter sending email failed with error: "' . $email->ErrorInfo . '"');

        return;
    }

    
    private function saveFile($reportId, $schedulerData, $content, $fileext){
        if (!empty($schedulerData['schedulersavetoaction'])){
            //generate file name
            $fileext = ".".$fileext;
            $fpmode = "w+";
            $filename = (!empty($schedulerData['schedulersaveto']) ? $schedulerData['schedulersaveto'] : "kreport");
            $dateformat = (!empty(SpiceConfig::getInstance()->config['KReports']['Plugins']['Integration']['kscheduling']['schedulerdateformat']) ? SpiceConfig::getInstance()->config['KReports']['Plugins']['Integration']['kscheduling']['schedulerdateformat'] : 'Y-m-d_H-i-s');
            if($schedulerData['schedulersavetoaction'] == "ADD") {
                $filename.= "_".$reportId."_".date($dateformat).$fileext;
            }
            elseif($schedulerData['schedulersavetoaction'] == "REPLACE") {
                $filename.= "_".$reportId.$fileext;
            }
            //write file
            if(!$fp = fopen(SpiceConfig::getInstance()->config['KReports']['Plugins']['Integration']['kscheduling']['schedulersavetopath'].$filename, $fpmode))
                LoggerManager::getLogger()->fatal("Could not save file from kreport scheduler in ".__FILE__." on line ".__LINE__);
            else {
                fwrite($fp, $content);
                fclose($fp);
            }
        }
    }

    private function setSchedulerStatus($schedulerId, $status) {
        $db = DBManagerFactory::getInstance();
        $db->query("UPDATE kreportschedulers SET
                        status = '$status'
                    WHERE id='$schedulerId'");
    }

    private function setNextSchedulerDate($reportId, $schedulerData) {
        $db = DBManagerFactory::getInstance();
        $nextDate = $this->parseRunData($schedulerData['min'] . ' ' . $schedulerData['hrs'] . ' ' . $schedulerData['day'] . ' ' . $schedulerData['month'] . ' ' . $schedulerData['weekday']);
        $db->query("INSERT INTO kreportschedulers SET
                        id = '" . create_guid() . "',
                        report_id  = '" . $reportId . "',
                        job_id = '" . $schedulerData['schedulerid'] . "',
                        timestamp = '" . $nextDate . "',
                        status = 'P'");
    }

    public function parseRunData($cronExpression = '* * * * *') {
        //2013-01-12 .. make sure we syncronize TimeZones
        $timeZone = new DateTimeZone(date_default_timezone_get());
        $now = new DateTime('now', $timeZone);
        $thisCronExpression = CronExpression::factory($cronExpression);
        $nextRunDate = $thisCronExpression->getNextRunDate($now, 0, true)->format('Y-m-d H:i:s');

        return $nextRunDate;
    }

}
