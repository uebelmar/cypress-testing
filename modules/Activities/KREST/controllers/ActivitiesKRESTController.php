<?php

namespace SpiceCRM\modules\Activities\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceFTSManager\SpiceFTSActivityHandler;
use SpiceCRM\modules\SpiceACL\SpiceACL;

class ActivitiesKRESTController
{
    public function loadHistory($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $retArray = array();

        $getParams = $req->getQueryParams();
        $start = $getParams['start'] ?: 0;
        $limit = $getParams['limit'] ?: 5;

        $queryArray = [];
        $modules = ['Calls', 'Meetings', 'Tasks'];

        $filterObjects = json_decode($getParams['objects']);

        foreach($modules as $module){

            // check if a filter applies
            if($filterObjects && is_array($filterObjects) && count($filterObjects) > 0){
                if(in_array($module, $filterObjects) === false)
                    continue;
            }

            // get the query
            $seed = BeanFactory::getBean($module);
            if($seed && SpiceACL::getInstance()->checkAccess($module, 'list') && method_exists($seed, 'get_activities_query')){
                $query = $seed->get_activities_query($args['parentmodule'], $args['parentid'], $getParams['own']);
                if(is_array($query)){
                    $queryArray = array_merge($queryArray, $query);
                } elseif (!empty($query)){
                    $queryArray[] = $query;
                }
            }
        }

        //echo implode(' UNION ALL ', $queryArray);
        //return;

        $objects = $db->limitQuery('SELECT id, module FROM ('.implode(' UNION ', $queryArray) . ') unionresult ORDER BY sortdate ASC', $start, $limit);

        while ($object = $db->fetchByAssoc($objects)) {

            $bean = BeanFactory::getBean($object['module'], $object['id']);

            if($bean && !empty($bean->id)) {
                foreach ($bean->field_defs as $fieldname => $fielddata) {
                    if ($bean->$fieldname)
                        $object['data'][$fieldname] = $bean->$fieldname;
                }

                $aclActions = ['detail', 'edit', 'delete'];
                foreach ($aclActions as $aclAction) {
                    $object['data']['acl'][$aclAction] = $bean->ACLAccess($aclAction);
                }

                $retArray[] = $object;
            }
        }

        $count = 0;
        if ($getParams['count']) {
            $historyCount = $db->fetchByAssoc($db->query('select count(id) itemcount from ('.implode(' UNION ALL ', $queryArray) . ') unionresult'));
            $count = $historyCount['itemcount'];
        }

        return $res->withJson(array(
                'items' => array_reverse($retArray),
                'count' => $count
            )
        );

    }

    static function loadFTSActivities($req, $res, $args)
    {
        $postBody = $req->getParsedBody();

        $activitiyHandler = new SpiceFTSActivityHandler();
        $results = $activitiyHandler->loadActivities('Activities', $args['parentid'], $postBody['start'], $postBody['limit'], $postBody['searchterm'], $postBody['own'], json_decode($postBody['objects'], true));

        return $res->withJson($results);
    }

}
