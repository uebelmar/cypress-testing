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
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\modules\KReports\KReportPluginManager;

require_once 'modules/KReports/KReport.php';
require_once 'modules/KReports/Plugins/Integration/kpublishing/kpublishing.php';

class pluginkpublishingcontroller
{

    public function __construct()
    {

    }

    function action_get_tabs()
    {
        include 'include/GroupedTabs/GroupedTabStructure.php';
        $grpStruc = new GroupedTabStructure();
        $grpStruc->get_tab_structure();

        $returnArray = array();
        if ($GLOBALS['tabStructure'])
            foreach ($GLOBALS['tabStructure'] as $tabItem) {
                $returnArray[] = array('tab' => $tabItem['label'], 'description' => translate($tabItem['label']));
            }
        return $returnArray;
    }

    function action_getreportvisualizationdata($requestParams)
    {
        $report = BeanFactory::getBean('KReports', $requestParams['record']);
        if ($report) {
            $pluginManager = new KReportPluginManager();

            $pluginData = $pluginManager->getPlugins();

            return array(
                'visualization_params' => $report->visualization_params,
                'plugindata' => $pluginData
            );
        } else {
            return array();
        }
    }

    function action_getreportpresentationdata($requestParams)
    {
        $report = BeanFactory::getBean('KReports', $requestParams['record']);
        if ($report) {
            $pluginManager = new KReportPluginManager();

            $pluginData = $pluginManager->getPlugins();

            return array(
                'report' => array(
                    'id' => $report->id,
                    'listfields' => $report->listfields,
                    'presentation_params' => $report->presentation_params,
                ),
                'plugindata' => $pluginData
            );
        } else {
            return array();
        }
    }

    function action_getDashletData($requestParams)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        $dashlets = $current_user->getPreference('dashlets', 'Home');

        $publishedReports = array();

        //2012-11-29 ... create a report Object required in PRO
        $thisReport = BeanFactory::getBean('KReports');

        $repQuery = 'SELECT * FROM kreports ';

        switch ($dashlets[$requestParams['dashletid']]['className']) {
            case 'KReportPresentationDashlet':
                $repQuery .= ' WHERE integration_params LIKE \'%"dashletPresentation":"on"%\' AND (integration_params LIKE \'%"kpublishing":"1"%\' OR integration_params LIKE \'%"kpublishing":1%\')AND deleted = 0';
                break;
            case 'KReportVisualizationDashlet':
                $repQuery .= ' WHERE integration_params LIKE \'%"dashletVisualization":"on"%\' AND (integration_params LIKE \'%"kpublishing":"1"%\' OR integration_params LIKE \'%"kpublishing":1%\')AND deleted = 0';
                break;
        }
		$repQuery.= " ORDER BY name ASC";
        $repObject = $db->query($repQuery);
        while ($repEntry = $db->fetchByAssoc($repObject)) {
            $publishedReports[] = array(
                'id' => $repEntry['id'],
                'name' => $repEntry['name']
            );
        }
        $filters = array();
        if (!empty($dashlets[$requestParams['dashletid']]['options']['report'])) {
            // get filters if there are any
            $savedReports = $db->query("SELECT id, name FROM kreportsavedfilters WHERE kreport_id = '" . $dashlets[$requestParams['dashletid']]['options']['report'] . "' AND (is_global = 1 OR assigned_user_id = '".$current_user->id."') AND deleted=0 ORDER BY name ASC");
            while ($savedReport = $db->fetchByAssoc($savedReports))
                $filters[] = $savedReport;
        }

        return array(
            'dashlet' => $dashlets[$requestParams['dashletid']],
            'reports' => $publishedReports,
            'filters' => $filters
        );

    }

    function action_getPublishedReports($requestParams)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $publishedReports = array();

        $thisReport = BeanFactory::getBean('KReports');
        $repQuery = 'SELECT * FROM kreports ';
        switch ($requestParams['reporttype']) {
            case 'KReportPresentationDashlet':
                $repQuery .= ' WHERE integration_params LIKE \'%"dashletPresentation":"on"%\' AND (integration_params LIKE \'%"kpublishing":"1"%\' OR integration_params LIKE \'%"kpublishing":1%\')AND deleted = 0';
                break;
            case 'KReportVisualizationDashlet':
                $repQuery .= ' WHERE integration_params LIKE \'%"dashletVisualization":"on"%\' AND (integration_params LIKE \'%"kpublishing":"1"%\' OR integration_params LIKE \'%"kpublishing":1%\')AND deleted = 0';
                break;
        }


        $repObject = $db->query($repQuery);
        while ($repEntry = $db->fetchByAssoc($repObject)) {
            $publishedReports[] = array(
                'id' => $repEntry['id'],
                'name' => $repEntry['name']
            );
        }

        if (count($publishedReports) == 0) {
            $repQuery = str_replace('"', '&quot;', $repQuery);
            $repObject = $db->query($repQuery);
            while ($repEntry = $db->fetchByAssoc($repObject)) {
                $publishedReports[] = array(
                    'id' => $repEntry['id'],
                    'name' => $repEntry['name']
                );
            }
        }

        return $publishedReports;

    }

    function action_setDashletData($requestParams)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        $dashlets = $current_user->getPreference('dashlets', 'Home');

        $dashlets[$requestParams['dashletid']]['options'] = array(
            'title' => $requestParams['title'],
            'report' => $requestParams['report'],
            'reportfilter' => $requestParams['reportfilter']
        );

        $current_user->setPreference('dashlets', $dashlets, 0, 'Home');

        return array(
            'dashlet' => $dashlets[$requestParams['dashletid']]
        );

    }

    function action_getPublishedDashlets($requestParams)
    {

        $db = DBManagerFactory::getInstance();

        $publishedReports = array();

        //2012-11-29 ... create a report Object required in PRO
        $thisReport = BeanFactory::getBean('KReports');

        $repQuery = 'SELECT * FROM kreports ';

        // $repQuery .= ' WHERE integration_params LIKE \'%"dashletPresentation":"on"%\' AND integration_params LIKE \'%"kpublishing":"1"%\' AND deleted = 0';
        $repQuery .= ' WHERE integration_params LIKE \'%"dashletPresentation":"on"%\' AND (integration_params LIKE \'%"kpublishing":"1"%\' OR integration_params LIKE \'%"kpublishing":1%\')AND deleted = 0';

        $repObject = $db->query($repQuery);
        while ($repEntry = $db->fetchByAssoc($repObject)) {
            $publishedReports[] = array(
                'id' => $repEntry['id'],
                'name' => $repEntry['name']
            );
        }

        return array(
            'reports' => $publishedReports
        );
    }

    function action_getreportpresentationndata2($requestParams)
    {
        $kpublishing = new kpublishing();
        $thisReport = BeanFactory::getBean('KReports', $requestParams['record']);
        if ($thisReport) {
            require_once 'modules/KReports/KReportPresentationManager.php';
            $thisPresManager = new KReportPresentationManager();

            // see if the Presdentation Plugin provides a separate export, if not take the default
            $exportData = $thisPresManager->getPresentationExport($thisReport, '', false);
            if (!$exportData)
                $exportData = $kpublishing->getDefaultPresentationExport($thisReport, '');

            echo json_encode($exportData);
        } else {
            return array();
        }
    }

    function action_getReportFilters($requestParams)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $filters = array();

        // get filters if there are any
        $savedReports = $db->query("SELECT id, name FROM kreportsavedfilters WHERE kreport_id = '" . $requestParams['reportId'] . "' AND (is_global = 1 OR assigned_user_id = '".$current_user->id."') ORDER BY name ASC");
        while ($savedReport = $db->fetchByAssoc($savedReports))
            $filters[] = $savedReport;

        return array(
            'filters' => $filters
        );
    }
}
