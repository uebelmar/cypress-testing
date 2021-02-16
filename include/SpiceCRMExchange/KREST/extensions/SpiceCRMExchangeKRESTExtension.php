<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceCRMExchange\KREST\controllers\SpiceCRMExchangeKRESTController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * restrict routes to authenticated users
 */
if(!SpiceCRM\includes\authentication\AuthenticationController::getInstance()->isAuthenticated()) return;

/**
 * register the Extension
 */

$RESTManager->registerExtension('ewsconfig', '1.0', ['subscriptiontimeout' =>  SpiceConfig::getInstance()->config['SpiceCRMExchange']['PushSubscriptionRequest']['StatusFrequency'] ?: 15]);

switch($RESTManager->app::VERSION) {
    case '2.6.1': // slim 2 - just 1 route needed
        $SpiceCRMExchangeKRESTController = new SpiceCRMExchangeKRESTController();
        $RESTManager->app->group('/spicecrmexchange', function () use ($RESTManager, $SpiceCRMExchangeKRESTController) {
            $RESTManager->app->group('/subscriptions', function () use ($RESTManager, $SpiceCRMExchangeKRESTController) {
                $RESTManager->app->group('/:userid', function () use ($RESTManager, $SpiceCRMExchangeKRESTController) {
                    $RESTManager->app->post('/:folderid', function ($userid, $folderid) use ($RESTManager, $SpiceCRMExchangeKRESTController) {
                        $args = array('userid' => $userid, 'folderid' => $folderid);
                        $response = $SpiceCRMExchangeKRESTController->subscribeSlim2($args);
                        echo $response;
                    });
                });
            });
        });
        break;
    case '3.9.2': // slim 3
        $RESTManager->app->group('/spicecrmexchange', function () {
            $this->group('/subscriptions', function () {
                $this->post('/force', [new SpiceCRMExchangeKRESTController(), 'forceResync']);
                $this->group('/{userid}', function () {
                    $this->get('', [new SpiceCRMExchangeKRESTController(), 'getSubscriptions']);
                    $this->post('/initialize', [new SpiceCRMExchangeKRESTController(), 'initializeSync']);
                    $this->post('/{folderid}', [new SpiceCRMExchangeKRESTController() , 'subscribe']);
                    $this->delete('/{folderid}', [new SpiceCRMExchangeKRESTController() , 'unsubscribe']);
                });
            });

            $this->group('/config', function() {
                $this->get('', [new SpiceCRMExchangeKRESTController(), 'getConfiguration']);
                $this->group('/{userid}', function() {
                    $this->get('', [new SpiceCRMExchangeKRESTController(), 'getConfiguration']);
                    $this->post('/{sysmoduleid}', [new SpiceCRMExchangeKRESTController(), 'syncModule']);
                    $this->delete('/{sysmoduleid}', [new SpiceCRMExchangeKRESTController(), 'unsyncModule']);
                });
            });

            $this->group('/events', function() {
                $this->get('', [new SpiceCRMExchangeKRESTController(), 'getEwsEvents']);
            });
        });
        break;
    default: // slim 4
        $routes = [
            [
                'method'      => 'post',
                'route'       => '/spicecrmexchange/subscriptions/force',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'forceResync',
                'description' => 'Force resync of subscriptions',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'get',
                'route'       => '/spicecrmexchange/subscriptions/{userid}',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'getSubscriptions',
                'description' => 'Get subscriptions',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'post',
                'route'       => '/spicecrmexchange/subscriptions/{userid}/initialize',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'initializeSync',
                'description' => 'Initialize subscription sync.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'post',
                'route'       => '/spicecrmexchange/subscriptions/{userid}/{folderid}',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'subscribe',
                'description' => 'Subscribe a folder.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'delete',
                'route'       => '/spicecrmexchange/subscriptions/{userid}/{folderid}',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'unsubscribe',
                'description' => 'Unsubscribe a folder.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'get',
                'route'       => '/spicecrmexchange/config',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'getConfiguration',
                'description' => 'Configuration getter.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'get',
                'route'       => '/spicecrmexchange/config/{userid}',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'getConfiguration',
                'description' => 'Configuration getter for a user.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'post',
                'route'       => '/spicecrmexchange/config/{userid}/{sysmoduleid}',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'syncModule',
                'description' => 'Syncing a module.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'delete',
                'route'       => '/spicecrmexchange/config/{userid}/{sysmoduleid}',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'unsyncModule',
                'description' => 'Unsyncing a module.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
            [
                'method'      => 'get',
                'route'       => '/spicecrmexchange/events',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'getEwsEvents',
                'description' => 'Get EWS events.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
        ];
        $RESTManager->registerRoutes($routes);
        break;
}
