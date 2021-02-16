<?php

namespace SpiceCRM\modules\ServiceOrders\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceFTSManager\ElasticHandler;
use SpiceCRM\includes\SpiceFTSManager\SpiceFTSBeanHandler;
use SpiceCRM\includes\SpiceFTSManager\SpiceFTSUtils;
use SpiceCRM\includes\SysModuleFilters\SysModuleFilters;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\modules\SpiceACL\SpiceACL;

class ServiceOrdersKRESTController
{


    public function discoverparent($req, $res, $args)
    {
        $result = [];
        // load the parent bean
        $parent = BeanFactory::getBean($args['parentType'], $args['parentId']);
        $modulehandler = new ModuleHandler();
        if($parent->servicelocation_id){
            $servicelocation = BeanFactory::getBean('ServiceLocations', $parent->servicelocation_id);
            $result['servicelocation'] = $modulehandler->mapBeanToArray('ServiceLocations', $servicelocation);
        }
        if($parent->consumer_id){
            $consumer = BeanFactory::getBean('Consumers', $parent->consumer_id);
            $result['consumer'] = $modulehandler->mapBeanToArray('Consumers', $consumer);
        }
        if($parent->account_id){
            $account = BeanFactory::getBean('Accounts', $parent->account_id);
            $result['account'] = $modulehandler->mapBeanToArray('Accounts', $account);
        }

        return $res->withJson($result);
    }


    /**
     * get planner users records with their service orders
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getPlannerRecords($req, $res, $args)
    {
        $params = $req->getQueryParams();
        $users = $this->getUsers($params['timelineUsersFilter']);
        $result = $this->getUsersServiceOrders($users, $params['start'], $params['end'], $params['timelineOrdersFilter']);
        return $res->withJson($result);
    }

    /**
     * get users list
     * @param $moduleFilter
     * @return array|null
     */
    private function getUsers($moduleFilter)
    {
        $bean = BeanFactory::getBean('Users');
        $sysModuleFilters = new SysModuleFilters();
        $whereClause = $sysModuleFilters->generareWhereClauseForFilterId($moduleFilter, '', $bean);
        $list = $bean->get_full_list('last_name', $whereClause);
        $ModuleHandler = new ModuleHandler();
        $users = [];

        foreach ($list as $user) {
            $users[] = $ModuleHandler->mapBeanToArray('Users', $user, [], false, false, true);
        }
        return $users;
    }

    /**
     * get users service orders
     * @param $users
     * @param $start
     * @param $end
     * @param $moduleFilter
     * @return mixed
     */
    private function getUsersServiceOrders($users, $start, $end, $moduleFilter)
    {
        $db = DBManagerFactory::getInstance();
        $start = $db->quote($start);
        $end = $db->quote($end);

        return array_map(
            function ($user) use ($end, $start, $moduleFilter) {
                $user['events'] = $this->getServiceOrders($start, $end, $user['id'], $moduleFilter);
                $user['unavailable'] = [
                    ['from' => '2020-06-28 00:00:00', 'to' => '2020-06-29 07:00:00'],
                    ['from' => '2020-06-29 12:00:00', 'to' => '2020-06-29 13:00:00'],
                    ['from' => '2020-06-29 17:00:00', 'to' => '2020-06-30 07:00:00'],
                    ['from' => '2020-06-30 12:00:00', 'to' => '2020-06-30 13:00:00'],
                ];
                return $user;
            },
            $users);
    }

    /**
     * loads the service orders for the input user
     * @param $startDate
     * @param $endDate
     * @param $userId
     * @param $moduleFilter
     * @return array
     */
    private function getServiceOrders($startDate, $endDate, $userId, $moduleFilter)
    {

        // check acl access for the user as well as if a filter object is set
        if (!SpiceACL::getInstance()->checkAccess('ServiceOrders', 'list', $userId)) {
            return [];
        }

        // if access is granted build the module query
        $beanHandler = new SpiceFTSBeanHandler('ServiceOrders');
        $sysModuleFilters = new SysModuleFilters();
        $moduleQuery = $beanHandler->getModuleSearchQuery('');

        $filterDef = $sysModuleFilters->generareElasticFilterForFilterId($moduleFilter);
        $moduleQuery['bool']['filter']['bool']['must'][] = $filterDef;

        // date range filter
        $moduleQuery['bool']['filter']['bool']['must'][] = [
            'bool' => [
                'must' => [
                    ['range' => ['date_start' => ['lte' => $endDate]]],
                    ['range' => ['date_end' => ['gte' => $startDate]]]
                ]
            ]
        ];

        $queryModule = SpiceFTSUtils::getIndexNameForModule('ServiceOrders');

        $moduleQuery['bool']['filter']['bool']['must'][] = ['term' => ['_index' => $queryModule]];
        $moduleQuery['bool']['filter']['bool']['must'][] = [
            'bool' => [
                'should' => [
                    ['term' => ["_activityparticipantids" => $userId]]
                ],
                'minimum_should_match' => 1
            ]
        ];

        // collect all modules we should query for building the search URL listing the indexes

        // if we do not have any modules to query .. return an empty response
        if (!$queryModule) {
            return ['totalcount' => 0, 'aggregates' => [], 'items' => []];
        }

        // build the complete query
        $query = [
            "query" => [
                'bool' => [
                    'should' => [$moduleQuery]
                ]
            ],
            "sort" => [
                ['date_start' => ['order' => 'asc']]
            ],
        ];

        $elasticHandler = new ElasticHandler();
        $results = json_decode($elasticHandler->query('POST', $queryModule . '/_search', null, $query), true);


        $moduleHandler = new ModuleHandler();

        $items = [];
        foreach ($results['hits']['hits'] as &$hit) {
            $seed = BeanFactory::getBean($elasticHandler->getHitModule($hit), $hit['_id']);
            foreach ($seed->field_name_map as $field => $fieldData) {
                $hit['_source'][$field] = html_entity_decode($seed->$field, ENT_QUOTES);
            }

            $hit['_source']['emailaddresses'] = $moduleHandler->getEmailAddresses($elasticHandler->getHitModule($hit), $hit['_id']);

            $hit['acl'] = $seed->getACLActions();

            // unset hidden fields
            foreach ($hit['acl_fieldcontrol'] as $field => $control) {
                if ($control == 1 && isset($hit['_source'][$field])) unset($hit['_source'][$field]);
            }

            $items[] = [
                'id' => $seed->id,
                'module' => 'ServiceOrders',
                'start' => $hit['_source']['date_start'],
                'end' => $hit['_source']['date_end'],
                'data' => $moduleHandler->mapBeanToArray($elasticHandler->getHitModule($hit), $seed, [], false, false, true)
            ];
        }

        return $items;
    }

}
