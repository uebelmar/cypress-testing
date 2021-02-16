<?php
namespace SpiceCRM\includes\SpiceCRMExchange\KREST\controllers;

use DateTime;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceCRMExchange\FolderHandlers\ExchangeCalendar;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeConnector;
use SpiceCRM\includes\SpiceCRMExchange\SpiceCRMExchangeLogger;
use SpiceCRM\includes\SpiceCRMExchange\Mappings\SpiceCRMExchangeModules;
use SpiceCRM\includes\SpiceCRMExchange\Connectivity\SpiceCRMExchangeSubscriptions;
use SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers\SpiceCRMExchangeMeetings;
use SpiceCRM\includes\SpiceCRMExchange\Connectivity\SpiceCRMExchangeUserSyncConfig;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;
use User;

class SpiceCRMExchangeKRESTController
{
    private $ewsModules = [
        [
            'module' => 'Meetings',
            'table'  => 'meetings',
            'class'  => SpiceCRMExchangeMeetings::class,
        ],
    ];

    /**
     * Adds a new Exchange subscription.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function subscribe($req, $res, $args) {
        $seedUser = BeanFactory::getBean('Users', $args['userid']);

        $thisSubscription = new SpiceCRMExchangeSubscriptions($seedUser, $args['folderid']);
        $response = $thisSubscription->subscribe();

        return $res->withJson(['code' => $response]);
    }

    /**
     * for old KREST using slim 2
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function subscribeSlim2($args) {
        $seedUser = BeanFactory::getBean('Users', $args['userid']);
        $thisSubscription = new SpiceCRMExchangeSubscriptions($seedUser, $args['folderid']);
        $response = $thisSubscription->subscribe();
        return json_encode(['code' => $response]);
    }


    /**
     * Removes an Exchange subscription.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function unsubscribe($req, $res, $args) {
        $seedUser = BeanFactory::getBean('Users', $args['userid']);

        $thisSubscription = new SpiceCRMExchangeSubscriptions($seedUser, $args['folderid']);
        $response = $thisSubscription->unsubscribe();

        return $res->withJson(['code' => $response]);
    }

    /**
     * returns the active subscriptions for the user
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getSubscriptions($req, $res, $args){
        $db = DBManagerFactory::getInstance();
        $retArray = [];
        $subscriptions = $db->query("SELECT * FROM sysexchangeusersubscriptions WHERE user_id='{$args['userid']}'");
        while($subscription = $db->fetchByAssoc($subscriptions)){
            $retArray[] = $subscription;
        }
        return $res->withJson($retArray);
    }

    /**
     * Returns the Exchange sync config for the given User.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getConfiguration($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        // check if we have a userid if not take current user
        $user_id = $args['userid'] ?: $current_user->id;

        $userSyncConfig = new SpiceCRMExchangeUserSyncConfig($user_id);
        $moduleSyncConfig = new SpiceCRMExchangeModules();

        return $res->withJson([
            'modules' => $moduleSyncConfig->getExchangeMappedModules(),
            'userconfig' => $userSyncConfig->getConfig(),
            'subscriptions' => $this->getUserSubscriptions($user_id)
        ]);
    }

    private function getUserSubscriptions($userid){
        $db = DBManagerFactory::getInstance();
        $subscriptions = [];
        $subscriptionsObject = $db->query("SELECT * FROM sysexchangeusersubscriptions WHERE user_id='$userid'");
        while($subscription = $db->fetchByAssoc($subscriptionsObject)){
            $subscriptions[] = $subscription;
        }
        return $subscriptions;
    }

    /**
     * Syncs a module with Exchange for the given User.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public function syncModule($req, $res, $args) {
        $userSyncConfig = new SpiceCRMExchangeUserSyncConfig($args['userid']);
        $userSyncConfig->syncModuleInExchange($args['sysmoduleid']);
        return $res->withJson([
            'userconfig' => $userSyncConfig->getConfig(),
            'subscriptions' => $this->getUserSubscriptions($args['userid'])
        ]);
    }

    /**
     * Unsyncs a module with Exchange for the given User.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public function unsyncModule($req, $res, $args) {
        $userSyncConfig = new SpiceCRMExchangeUserSyncConfig($args['userid']);
        $userSyncConfig->unsyncModuleInExchange($args['sysmoduleid']);
        return $res->withJson([
            'userconfig' => $userSyncConfig->getConfig(),
            'subscriptions' => $this->getUserSubscriptions($args['userid'])
        ]);
    }

    /**
     * Handles the incoming sync requests from the Exchange server.
     *
     * @param $req
     * @param $res
     * @param $args
     * @throws \Exception
     */
    public function handle($req, $res, $args) {
        $authController= AuthenticationController::getInstance();
        $GLOBALS['isKRESTExchange'] = true;

        $notification = SpiceCRMExchangeConnector::extractNotification($req->getBody()->__toString());

        // todo check if subscriptionID present otherwise throw exception
        $subscriptionId = $notification->SubscriptionId->__toString();
        $previousWatermark = $notification->PreviousWatermark->__toString();

        // check the subscription
        $subscription = SpiceCRMExchangeSubscriptions::checkSubscription($subscriptionId);
        if ($subscription !== false) {

            $authController->setCurrentUser(BeanFactory::getBean('Users', $subscription['user_id']));
            $current_user = $authController->getCurrentUser();

            if ($this->checkForHeartbeat($current_user, $notification) == false) {
                foreach ($notification->CreatedEvent as $createdEvent) {
                    $this->handleEvent($subscription, $createdEvent);
                }
                foreach ($notification->ModifiedEvent as $modifiedEvent) {
                    $this->handleEvent($subscription, $modifiedEvent);
                }
            }
        }

        SpiceCRMExchangeConnector::respondToEws($subscription);
    }

    /**
     * Handles the incoming sync requests from the Exchange server.
     * for old KREST using slim 2
     * @param $req
     * @param $res
     * @param $args
     * @throws \Exception
     */

    public function handleSlim2($body) {
        $authController= AuthenticationController::getInstance();
        $GLOBALS['isKRESTExchange'] = true;

        $notification = SpiceCRMExchangeConnector::extractNotification($body);

        // todo check if subscriptionID present otherwise throw exception
        $subscriptionId = $notification->SubscriptionId->__toString();
        $previousWatermark = $notification->PreviousWatermark->__toString();

        // check the subscription
        $subscription = SpiceCRMExchangeSubscriptions::checkSubscription($subscriptionId);
        if ($subscription !== false) {
            $authController->setCurrentUser(BeanFactory::getBean('Users', $subscription['user_id']));
            $current_user = $authController->getCurrentUser();

            if ($this->checkForHeartbeat($current_user, $notification) == false) {
                foreach ($notification->CreatedEvent as $createdEvent) {
                    $this->handleEvent($subscription, $createdEvent);
                }
                foreach ($notification->ModifiedEvent as $modifiedEvent) {
                    $this->handleEvent($subscription, $modifiedEvent);
                }
            }
        }

        SpiceCRMExchangeConnector::respondToEws($subscription);
    }

    /**
     * @param $subscription
     * @param $modifiedEvent
     */
    public function handleEvent($subscription, $modifiedEvent) {

        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        // todo deal with the delete events
        if ($modifiedEvent->ItemId) {
            $itemId = (string)$modifiedEvent->ItemId['Id'];
            // important! it may happen when ews send information before it was sent in exchange itself
            if (!$itemId) {
                return;
            }
            $changeKey = (string)$modifiedEvent->ItemId['ChangeKey'];

            $logger = new SpiceCRMExchangeLogger();
            $logger->logInboundRecord($itemId);

            $connector = new SpiceCRMExchangeConnector($current_user);
            $ewsItem = $connector->getItem($itemId, $changeKey);

// Do not use this code! It will delete a created bean because Excahnge doesn't seem to know that the exchange ID
// is already allocated at that time. Therefore no item found and bean would be deleted
//            if ($ewsItem->ResponseCode = 'ErrorItemNotFound') {
//                $this->markBeanDeleted($itemId);
//            }

            try {
                $ewsModule = $this->findModuleForEws($ewsItem);
            } catch (\Exception $e) {
                $ewsModule = $this->findModuleHandlerBySubcription($subscription, $itemId);
            }

            if (!$ewsModule) {
                return;
            }

            // todo make it work with contacts
            $ewsBean = new $ewsModule($current_user);
            $stopExchangeHooks = true;
            $bean = $ewsBean->mapEWSToBean($ewsItem);
            $logger->logInboundRecord($itemId, $bean->id, $bean->object_name, json_encode($ewsItem));

            // set processed to true so Hooks will not fire
            //$bean->processed = true;

            // workaround remove ews hooks
//                        if(isset(\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['SpiceCRMExchange']['hooksprocessed']) && isset(\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['SpiceCRMExchange']['exclude_hooks'])){
//                            $bean->processed = \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['SpiceCRMExchange']['hooksprocessed'];
//                            $bean->exclude_hooks = \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['SpiceCRMExchange']['exclude_hooks'][$bean->module_dir];
//                            $bean->stopExchangeHooks = $stopExchangeHooks;
//                            file_put_contents('ews.log', "bean->stopExchangeHooks is set to true\n", FILE_APPEND);
//                        }
            // save bean
            $bean->save();
            $bean->call_custom_logic('after_save_completed', '');

            // ToDo: cleanup and move to separate class or leav in hooks
            // check if we shoudl send an update to the socket
            if (SpiceConfig::getInstance()->config['core']['socket_id'] && SpiceConfig::getInstance()->config['core']['socket_backend']) {
                $body = ['sysid' => SpiceConfig::getInstance()->config['core']['socket_id'], 'room' => 'beanupdates', 'message' => ['i' => $bean->id, 'm' => $bean->module_dir, 's' => session_id()]];
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => SpiceConfig::getInstance()->config['core']['socket_backend'],
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => json_encode($body),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                    ]
                ]);

                $response = curl_exec($curl);
                if (!$response) {
                    $error = curl_error($curl);
                }

                curl_close($curl);
            }

            // save the new bean ID back to EWS
            $ewsBean->spiceBean = $bean;
            if ($ewsBean->needsUpdate) {
                $ewsBean->updateOnExchange();
            }

            $logger->logInboundRecord($bean->external_id, $bean->id, get_class($bean));
        }
    }

    /**
     * Forces subscription resynchronization.
     *
     * @param $req
     * @param $res
     * @param $args
     * @throws \Exception
     */
    public function forceResync($req, $res, $args) {
        $body = $req->getParsedBody();
        $subscriptionId = $body['subscription_id'];

        $db = DBManagerFactory::getInstance();
        $sql = "SELECT * FROM sysexchangeusersubscriptions WHERE subscriptionid='{$subscriptionId}'";
        $query = $db->query($sql);
        $result = $db->fetchByAssoc($query);

        $user = BeanFactory::getBean('Users', $result['user_id']);
        $subscription = new SpiceCRMExchangeSubscriptions($user, $result['folder_id']);
        $subscription->forceResync();
        // todo send back the same things as in the subscribe endpoint
    }

    /**
     * Checks if the incoming notification is a Heartbeat notification.
     * If it's the case, the last_active date of the subscription gets updated.
     *
     * @param User $user
     * @param $notification
     * @return bool
     */
    private function checkForHeartbeat(User $user, $notification) {
        if (isset($notification->StatusEvent)) {
            SpiceCRMExchangeSubscriptions::updateHeartbeat(
                $notification->SubscriptionId,
                $notification->StatusEvent->Watermark
            );
            return true;
        }

        return false;
    }

    /**
     * findModuleForEws
     *
     * Tries to find the module the incoming EWS object id belongs to.
     *
     * @param $itemId
     * @return mixed
     * @throws \Exception
     */
    private function findModuleForEws($item) {
        if (!empty($item->ExtendedProperty)) {
            $extendedFields = json_decode($item->ExtendedProperty[0]->Value);

            if (isset($extendedFields->_module)) {
                return SpiceCRMExchangeModules::getModuleHandler($extendedFields->_module);
            }
        }
        throw new Exception('Module not found');
//        return $this->findBeanByExternalId($item->ItemId->Id);
    }

    private function findModuleHandlerBySubcription($subscription, $externalId) {
        $db = DBManagerFactory::getInstance();

        $sql = "SELECT `module_handler`
                FROM sysexchangeusersubscriptions `seus`
                JOIN sysexchangemappingmodules `semm` ON `semm`.`exchange_object`=`seus`.`folder_id`
                WHERE `seus`.`subscriptionid`='" . $subscription['subscriptionid'] . "'
                AND `seus`.`user_id`='" . $subscription['user_id'] . "'";
        $query = $db->query($sql);
        while ($row = $db->fetchRow($query)) {
            $user = BeanFactory::getBean('Users', $subscription['user_id']);
            if ($row['module_handler'] == "\SpiceCRM\includes\SpiceCRMExchange\ModuleHandlers\SpiceCRMExchangeContacts") {
                $existsBeanWithExternalId = $row['module_handler']::staticExistsBeanWithExternalId($externalId);
            } else {
                $moduleHandler = new $row['module_handler']($user);
                $existsBeanWithExternalId = $moduleHandler->existsBeanWithExternalId($externalId);
            }

            if ($existsBeanWithExternalId) {
                return $row['module_handler'];
            }
        }
    }

    /**
     * In case no module name is provided by the EWS notification a search is conducted by iterating over all
     * synchronizable Spice modules and looking for a bean with external_id equal to the ID of the EWS item.
     *
     * todo additional Contacts handling
     *
     * @param $externalId
     * @return mixed
     * @throws \Exception
     */
    private function findBeanByExternalId($externalId) {
        $mappingModules = new SpiceCRMExchangeModules();
        foreach ($mappingModules->getExchangeMappedModules() as $ewsModule) {
            $bean = BeanFactory::getBean(SpiceCRMExchangeModules::getModuleName($ewsModule['sysmodule_id']));
            $bean->retrieve_by_string_fields(['external_id' => $externalId]);
            if ($bean->external_id != '') {
                return $ewsModule['module_handler'];
            }
        }


        $logger = new SpiceCRMExchangeLogger();
        $logger->logInboundRecord(
            $externalId,
            '',
            '',
            'No module for the EWS ID: ' . $externalId
        );

        throw new Exception('No module for the EWS ID: ' . $externalId);
    }

    /**
     * Returns the EWS calendar items for a specific timeframe excluding the ones that are already mapped to
     * existing beans.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getEwsEvents($req, $res, $args) {
        $params = $req->getQueryParams();

        $startDate = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            date('Y-m-01 00:00:00')
        );
        if (isset($params['startdate'])) {
            $startDate = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                date('Y-m-d 00:00:00', strtotime(urldecode($params['startdate'])))
            );
        }

        $endDate = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            date('Y-m-t 23:59:59')
        );
        if (isset($params['enddate'])) {
            $endDate = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                date('Y-m-d 23:59:59', strtotime(urldecode($params['enddate'])))
            );
        }

        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $calendar = new ExchangeCalendar($current_user);

        try {
            $results = $calendar->getEwsEvents($startDate, $endDate);

            return $res->withJson([
                'result' => true,
                'events' => $results,
            ]);
        } catch (\Exception $e) {
            return $res->withJson([
                'result' => false,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    public function initializeSync($req, $res, $args) {
        $body     = $req->getParsedBody();
        $user     = BeanFactory::getBean('Users', $args['userid']);
        $calendar = new ExchangeCalendar($user);
        $endDate  = $body['end_date'] ?? null;
        $beanList = $calendar->getBeansForQueue($body['module_name'], $body['start_date'], $endDate);
        $result   = $calendar->fillUpSyncTable($body['module_name'], $beanList);
        return $res->withJson($result);
    }

    private function markBeanDeleted($exchangeId) {
        $mappingModules = new SpiceCRMExchangeModules();
        foreach ($mappingModules->getExchangeMappedModules() as $ewsModule) {
            $bean = BeanFactory::getBean(SpiceCRMExchangeModules::getModuleName($ewsModule['sysmodule_id']));
            $bean->retrieve_by_string_fields(['external_id' => $exchangeId]);
            if (!empty($bean->id)) {
                $bean->mark_deleted($bean->id);
                $logger = new SpiceCRMExchangeLogger();
                $logger->logInboundRecord(
                    $exchangeId,
                    $bean->id,
                    $bean->object_name,
                    'Deleted Item in EWS. Related Bean marked deleted.'
                );
                break; // no need to loop further. Bean was found and set deleted.
            }
            unset($bean);
        }
    }
}
