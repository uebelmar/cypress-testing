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
use SpiceCRM\modules\KReports\KReport;

class pluginkpdrilldowncontroller {

   public function __construct() {
      
   }

    public function action_load_visualization() {
        require_once('modules/KReports/KReport.php');
        require_once('modules/KReports/KReportVisualizationManager.php');

        $thisReport = new KReport();
        $thisReport->retrieve($_REQUEST['popupreportid']);

        // retrieve the mapping and add the filters
        $parentReport = new KReport();
        $parentReport->retrieve($_REQUEST['parentreportid']);
        $integration_params = json_decode(html_entity_decode($parentReport->integration_params));
        $mappingData = null;
        foreach ($integration_params->kpdrilldown as $thisDrilldown) {
            if ($thisDrilldown->linkid == $_REQUEST['drilldownid']) {
                $mappingData = $thisDrilldown->mappingdata;
                break;
            }
        }

        //maretval: get genuine whereconditions from parent report
        $parentReport_whereconditions = json_decode(html_entity_decode($parentReport->whereconditions));

        //maretval overwrite where conditions: if where conditions are dynamically set in parent report overwrite values set in $parentReport_whereconditions
        if (isset($_REQUEST['parentWhereConditions']) && !empty($_REQUEST['parentWhereConditions'])) {
            $parentWhereConditions = json_decode(html_entity_decode($_REQUEST['parentWhereConditions']));
            if (is_array($parentWhereConditions)) {
                foreach ($parentWhereConditions as $parentWhereConditionIndex => $parentWhereConditionData) {
                    foreach ($parentReport_whereconditions as $parentReport_whereConditionIndex => $parentReport_whereconditionData) {
                        if ($parentReport_whereconditionData->fieldid == $parentWhereConditionData->fieldid) {
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->operator = $parentWhereConditionData->operator;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->value = $parentWhereConditionData->value;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->valueto = $parentWhereConditionData->valueto;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->valuekey = $parentWhereConditionData->valuekey;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->valuetokey = $parentWhereConditionData->valuetokey;
                        }
                    }
                }
            }
        }

        $dynamicoptions = array();
        $dynamicoptionsi = 0;

        // handle our where conditions
        $whereconditions = json_decode(html_entity_decode($thisReport->whereconditions));
        if (is_array($mappingData)) {
            $recordData = json_decode(html_entity_decode($_REQUEST['recorddata']));
            foreach ($mappingData as $thisMappingEntry) {
                if ($thisMappingEntry->mappedid != '') {
                    $jumpOffLoop = false;
                    reset($whereconditions);
                    foreach ($whereconditions as $whereConditionIndex => $whereconditionData) {
                        if ($whereconditionData->fieldid == $thisMappingEntry->whereid) {
                            if ($thisMappingEntry->operator != "reference") {

                                $whereconditions[$whereConditionIndex]->operator = $thisMappingEntry->operator; //added maretval; ORIGINAL: 'equals'
                                $whereconditions[$whereConditionIndex]->value = $recordData->{$thisMappingEntry->mappedid};
                                $whereconditions[$whereConditionIndex]->valuekey = '';
                                $dynamicoptions[$dynamicoptionsi]['fieldid'] = $thisMappingEntry->whereid;
                                $dynamicoptions[$dynamicoptionsi]['operator'] = $thisMappingEntry->operator;
                                $dynamicoptions[$dynamicoptionsi]['value'] = $recordData->{$thisMappingEntry->mappedid};
                                $dynamicoptionsi++;
                                $recordData->{$thisMappingEntry->mappedid};
                                $jumpOffLoop = true;
                            }
                            //added maretval: map referenced field with operator and value from parent report
                            elseif ($thisMappingEntry->operator == "reference") {
                                if (is_array($parentReport_whereconditions)) {
                                    foreach ($parentReport_whereconditions as $parentReport_whereConditionIndex => $parentReport_whereconditionData) {

                                        if ($parentReport_whereconditionData->reference == $thisMappingEntry->mappedid) {
                                            $whereconditions[$whereConditionIndex]->operator = $parentReport_whereconditionData->operator;
                                            $whereconditions[$whereConditionIndex]->value = $parentReport_whereconditionData->value;
                                            $whereconditions[$whereConditionIndex]->valueto = $parentReport_whereconditionData->valueto;
                                            $whereconditions[$whereConditionIndex]->valuekey = $parentReport_whereconditionData->valuekey;
                                            $whereconditions[$whereConditionIndex]->valuetokey = $parentReport_whereconditionData->valuetokey;

                                            $dynamicoptions[$dynamicoptionsi]['fieldid'] = $thisMappingEntry->whereid;
                                            $dynamicoptions[$dynamicoptionsi]['operator'] = $parentReport_whereconditionData->operator;
                                            $dynamicoptions[$dynamicoptionsi]['value'] = $parentReport_whereconditionData->value;
                                            $dynamicoptions[$dynamicoptionsi]['valueto'] = $parentReport_whereconditionData->valueto;
                                            $dynamicoptions[$dynamicoptionsi]['valuetokey'] = $parentReport_whereconditionData->valuetokey;

                                            $dynamicoptionsi++;
                                            $recordData->{$thisMappingEntry->mappedid};
                                            $jumpOffLoop = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($jumpOffLoop)
                            break;
                        //end
                    }
                }
            }
        }

        $thisReport->whereconditions = json_encode($whereconditions);

        $thisVisualizationManager = new KReportVisualizationManager();
        $visContent = $thisVisualizationManager->renderVisualization(html_entity_decode($thisReport->visualization_params, ENT_QUOTES, 'UTF-8'), $thisReport, array('parentbean' => $parentBean));

        $visItems = array();
        foreach ($thisVisualizationManager->itemData as $thisItemIndex => $thisItemData)
            $visItems[] = $thisItemData['divID'];

        echo json_encode(array(
            'visContent' => $visContent,
            'visItems' => $visItems
        ));
    }

    public function action_loadReport($requestParams) {
        $db = DBManagerFactory::getInstance();

        $thisReport = BeanFactory::getBean('KReports', $requestParams['popupreportid']);

        // retrieve the mapping and add the filters
        $parentReport = BeanFactory::getBean('KReports', $requestParams['parentreportid']);
        $integration_params = json_decode(html_entity_decode($parentReport->integration_params));
        $mappingData = null;
        foreach ($integration_params->kpdrilldown as $thisDrilldown) {
            if ($thisDrilldown->linkid == $requestParams['drilldownid']) {
                $mappingData = $thisDrilldown->mappingdata;
                break;
            }
        }

        //maretval: get genuine whereconditions from parent report
        $parentReport_whereconditions = json_decode(html_entity_decode($parentReport->whereconditions)); //added maretval
        //maretval overwrite where conditions: if where conditions are dynamically set in parent report overwrite values set in $parentReport_whereconditions
        if (isset($requestParams['parentWhereConditions']) && !empty($requestParams['parentWhereConditions'])) {
            $parentWhereConditions = json_decode(html_entity_decode($requestParams['parentWhereConditions']));
            if (is_array($parentWhereConditions)) {
                foreach ($parentWhereConditions as $parentWhereConditionIndex => $parentWhereConditionData) {
                    foreach ($parentReport_whereconditions as $parentReport_whereConditionIndex => $parentReport_whereconditionData) {
                        if ($parentReport_whereconditionData->fieldid == $parentWhereConditionData->fieldid) {
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->operator = $parentWhereConditionData->operator;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->value = $parentWhereConditionData->value;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->valueto = $parentWhereConditionData->valueto;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->valuekey = $parentWhereConditionData->valuekey;
                            $parentReport_whereconditions[$parentReport_whereConditionIndex]->valuetokey = $parentWhereConditionData->valuetokey;
                        }
                    }
                }
            }
        }

        $dynamicoptions = array();
        $dynamicoptionsi = 0;
        $whereconditions = json_decode(html_entity_decode($thisReport->whereconditions));
        if (is_array($mappingData)) {
            $recordData = json_decode(html_entity_decode(base64_decode($requestParams['recorddata'])));
            foreach ($mappingData as $thisMappingEntry) {
                if ($thisMappingEntry->mappedid != '') {
                    $jumpOffLoop = false;
                    reset($whereconditions);
                    foreach ($whereconditions as $whereConditionIndex => $whereconditionData) {
                        if ($whereconditionData->fieldid == $thisMappingEntry->whereid) {
                            if ($thisMappingEntry->operator != "reference") {
                                $whereconditions[$whereConditionIndex]->operator = $thisMappingEntry->operator; //added maretval; ORIGINAL: 'equals'
                                $whereconditions[$whereConditionIndex]->value = $recordData->{$thisMappingEntry->mappedid};
                                $whereconditions[$whereConditionIndex]->valuekey = $recordData->{$thisMappingEntry->mappedid};
                                $dynamicoptions[$dynamicoptionsi]['fieldid'] = $thisMappingEntry->whereid;
                                $dynamicoptions[$dynamicoptionsi]['operator'] = $thisMappingEntry->operator;
                                $dynamicoptions[$dynamicoptionsi]['value'] = $recordData->{$thisMappingEntry->mappedid};
                                $dynamicoptions[$dynamicoptionsi]['valuekey'] = $recordData->{$thisMappingEntry->mappedid};
                                $dynamicoptionsi++;
                                $recordData->{$thisMappingEntry->mappedid};
                                $jumpOffLoop = true;
                            }
                            //added maretval: map referenced field with operator and value from parent report
                            elseif ($thisMappingEntry->operator == "reference") {
                                if (is_array($parentReport_whereconditions)) {
                                    foreach ($parentReport_whereconditions as $parentReport_whereConditionIndex => $parentReport_whereconditionData) {

                                        if ($parentReport_whereconditionData->reference == $thisMappingEntry->mappedid) {
                                            $whereconditions[$whereConditionIndex]->operator = $parentReport_whereconditionData->operator;
                                            $whereconditions[$whereConditionIndex]->value = $parentReport_whereconditionData->value;
                                            $whereconditions[$whereConditionIndex]->valueto = $parentReport_whereconditionData->valueto;
                                            $whereconditions[$whereConditionIndex]->valuekey = $parentReport_whereconditionData->valuekey;
                                            $whereconditions[$whereConditionIndex]->valuetokey = $parentReport_whereconditionData->valuetokey;

                                            $dynamicoptions[$dynamicoptionsi]['fieldid'] = $thisMappingEntry->whereid;
                                            $dynamicoptions[$dynamicoptionsi]['operator'] = $parentReport_whereconditionData->operator;
                                            $dynamicoptions[$dynamicoptionsi]['value'] = $parentReport_whereconditionData->value;
                                            $dynamicoptions[$dynamicoptionsi]['valueto'] = $parentReport_whereconditionData->valueto;
                                            $dynamicoptions[$dynamicoptionsi]['valuekey'] = $parentReport_whereconditionData->valuekey;
                                            $dynamicoptions[$dynamicoptionsi]['valuetokey'] = $parentReport_whereconditionData->valuetokey;

                                            $dynamicoptionsi++;
                                            $recordData->{$thisMappingEntry->mappedid};
                                            $jumpOffLoop = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($jumpOffLoop)
                            break;
                        //end
                    }
                }
            }
        }

        $thisReport->whereconditions = json_encode($whereconditions);

        // set start and limit if not set
        if (!isset($_REQUEST['start']))
            $_REQUEST['start'] = 0;
        if (!isset($_REQUEST['limit']))
            $_REQUEST['limit'] = 0;

        // set the override Where if set in the request
        if (isset($_REQUEST['whereConditions'])) {
            $thisReport->whereOverride = json_decode(html_entity_decode($_REQUEST['whereConditions']), true);
        }

        // set request Paramaters
        $reportParams = array('noFormat' => true, 'start' => isset($_REQUEST['start']) ? $_REQUEST['start'] : 0, 'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0);

        // see if we should sort
        if (isset($_REQUEST['sort']) && isset($_REQUEST['dir'])) {
            $reportParams['sortseq'] = $_REQUEST['dir'];
            $reportParams['sortid'] = $_REQUEST['sort'];
        } elseif (isset($_REQUEST['sort'])) {
            $sortParams = json_decode(html_entity_decode($_REQUEST['sort']));
            $reportParams['sortid'] = $sortParams[0]->property;
            $reportParams['sortseq'] = $sortParams[0]->direction;
        }

        $totalArray = array();
        if ($thisDrilldown->linktype != 'LINK')
            $totalArray['records'] = $thisReport->getSelectionResults($reportParams, '0', false, $drillDownFilter);
        else
            $totalArray['records'] = array();

        // rework ... load from kQuery fieldArray
        $fieldArr = array();

        //2012-12-01 added link array to add to metadata for buiilding links in the frontend
        $linkArray = $thisReport->buildLinkArray($thisReport->kQueryArray->queryArray['root']['kQuery']->fieldArray);

        foreach ($thisReport->kQueryArray->queryArray['root']['kQuery']->fieldArray as $fieldid => $fieldname) {
            $thisFieldArray = array('name' => $fieldname);
            if (isset($linkArray[$fieldid]))
                $thisFieldArray['linkInfo'] = json_encode($linkArray[$fieldid]);
            $fieldArr[] = $thisFieldArray;
        }

        $totalArray['metaData'] = array(
            'totalProperty' => 'count',
            'root' => 'records',
            'fields' => $fieldArr,
            'dynamicoptions' => json_encode($whereconditions), //added maretval
        );

        // do a count
        if ($thisDrilldown->linktype != 'LINK')
            $totalArray['count'] = $thisReport->getSelectionResults(array('start' => $_REQUEST['start'], 'limit' => $_REQUEST['limit']), isset($_REQUEST['snapshotid']) ? $_REQUEST['snapshotid'] : '0', true);
        else
            $totalArray['count'] = 0;

        // jscon encode the result and return it
        return $totalArray;
    }

   public function action_getdisplaycolumns($requestData) {
      require_once('modules/KReports/KReportPresentationManager.php');

      if (!empty($requestData['popupreportid'])) {
         require_once('modules/KReports/KReport.php');
         $thisReport = BeanFactory::getBean('KReports', $requestData['popupreportid']);
         $presentationManager = new KReportPresentationManager();
         $presPlugin = $presentationManager->getPresentationPlugin($thisReport);
         return $presPlugin->buildColumnArray($thisReport);
      } else
         return array();
   }

   public function action_getreports() {
      // get a Bean for Kreports
      // $report = \SpiceCRM\data\BeanFactory::getBean('KReports');
      require_once('modules/KReports/KReport.php');
      $report = new KReport();

      // see if we have a Where Clause
      $addWhere = '';
      if (!empty($_REQUEST['filter']))
         $addWhere = "kreports.name like '%" . $_REQUEST['filter'] . "%'";

      // get all Beans
      $reportList = $report->get_list("kreports.name", $addWhere, 0, 20);

      // an emtpy return Array
      $repArray = array();

      // loop over the array
      foreach ($reportList['list'] as $thisReport) {
         $repArray[] = array(
             'id' => $thisReport->id,
             'name' => $thisReport->name
         );
      }

      // echo the JSON
      return $repArray;
   }

   public function action_getwherefields($getParams) {
      require_once('modules/KReports/KReport.php');
      $thisReport = new KReport();
      $thisReport->retrieve($getParams['wherereportid']);

      $whereFields = json_decode(html_entity_decode($thisReport->whereconditions));

      $wherefieldArray = array();

      foreach ($whereFields as $thisWhereField) {
         if ($thisWhereField->usereditable == 'yes')
            $wherefieldArray[] = array(
                'fieldid' => $thisWhereField->fieldid,
                'name' => $thisWhereField->name
            );
      }

      return $wherefieldArray;
   }

    public function action_storeDynamicoptions($requestParams){
        if(isset($requestParams['source']) && $requestParams['source'] == 'kpdrilldown'){
            $requestParams['dynamicoptions'] = json_decode(html_entity_decode($requestParams['dynamicoptions']), true);
            $_SESSION['kreporter'][$requestParams['popupreportid']] = $requestParams;
        }
        else{
            unset($_SESSION['kreporter'][$requestParams['popupreportid']]);
        }
        return true;
    }

    public function action_getStoredDynamicoptions($requestParams){
        $dynamicoptions = array();
        if(isset($_SESSION['kreporter'][$requestParams['reportId']]) && !empty($_SESSION['kreporter'][$requestParams['reportId']]['dynamicoptions'])){
            $dynamicoptions = $_SESSION['kreporter'][$requestParams['reportId']]['dynamicoptions'];
        }
        return json_encode($dynamicoptions);
    }

}
