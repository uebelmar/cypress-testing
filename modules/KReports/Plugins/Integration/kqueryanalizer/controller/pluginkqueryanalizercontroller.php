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

namespace SpiceCRM\modules\KReports\Plugins\Integration\kqueryanalizer\controller;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SqlFormatter;

require_once('modules/KReports/Plugins/Integration/kqueryanalizer/SqlFormatter.php');

class pluginkqueryanalizercontroller {

    /**
     * KReporter 4 Backend compatibility method
     * @param $params Array
     */
    public function action_get_sql($params) {
        $db = DBManagerFactory::getInstance();

        $thisReport = BeanFactory::getBean('KReports', $params['record']);

        // changed to be in the body
        // 2013-09-30 changed to request bidy BUG #503
        if (isset($params['whereOverride']) && $params['whereOverride'] != '') {
            $thisReport->whereOverride = json_decode(html_entity_decode($params['whereOverride']), true);
        }

        //echo $thisReport->get_report_main_sql_query('', true, '');
        $mainQuery = $thisReport->get_report_main_sql_query(true, '', '');

        $respArray = array(
            //2012-11-28 srip unicode characters with the pregreplace [^(\x20-\x7F)]* from the string ..
            'main' => preg_replace('/\n|\r|[^(\x20-\x7F)]*/', '', $mainQuery),
            'formatted' => SqlFormatter::format($mainQuery, false),
            'highlighted' => SqlFormatter::format($mainQuery),
            'count' => $thisReport->kQueryArray->countSelectString,
            'total' => $thisReport->kQueryArray->totalSelectString,
        );

        // process the describe
        $descObj = $db->query('DESCRIBE ' . $respArray['main']);
        while ($descRow = $db->fetchByAssoc($descObj)) {
            $respArray['descResult'][] = base64_encode(json_encode($descRow, JSON_FORCE_OBJECT));
        }

        return $respArray;
    }


    public function get_sql($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $params = $req->getParsedBody();

        $thisReport = BeanFactory::getBean('KReports', $params['record']);

        // changed to be in the body
        // 2013-09-30 changed to request bidy BUG #503 
        if (isset($params['whereOverride']) && $params['whereOverride'] != '') {
            $thisReport->whereOverride = json_decode(html_entity_decode($params['whereOverride']), true);
        }

        //echo $thisReport->get_report_main_sql_query('', true, '');
        $mainQuery = $thisReport->get_report_main_sql_query(true, '', '');

        $respArray = array(
            //2012-11-28 srip unicode characters with the pregreplace [^(\x20-\x7F)]* from the string .. 
            'main' => preg_replace('/\n|\r|[^(\x20-\x7F)]*/', '', $mainQuery),
            'formatted' => SqlFormatter::format($mainQuery, false),
            'highlighted' => SqlFormatter::format($mainQuery),
            'count' => $thisReport->kQueryArray->countSelectString,
            'total' => $thisReport->kQueryArray->totalSelectString,
        );

        // process the describe
        $descObj = $db->query('DESCRIBE ' . $respArray['main']);
        while ($descRow = $db->fetchByAssoc($descObj)) {
            $respArray['descResult'][] = base64_encode(json_encode($descRow, JSON_FORCE_OBJECT));
        }

        return $res->withJson($respArray);
    }

}
