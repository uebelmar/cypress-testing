<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\modules\Campaigns\KREST\controllers\SubscriptionController;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\RESTManager;
use Slim\Routing\RouteCollectorProxy;

require_once 'modules/Campaigns/utils.php';

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('campaigns', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/newsletters/subscriptions/{contactid}',
        'class'       => SubscriptionController::class,
        'function'    => 'getSubscriptionList',
        'description' => 'Logs inbound Mailgun messages',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/newsletters/subscriptions/{contactid}',
        'class'       => SubscriptionController::class,
        'function'    => 'changeSubscriptionType',
        'description' => 'subscrib or unsubscribe',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);