<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\includes\MailChimp\KREST\controllers\MailChimpController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('mailchimp', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/MailChimp/CampaignTasks/{id}/report',
        'class'       => MailChimpController::class,
        'function'    => 'getReport',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/MailChimp/CampaignTasks/{id}/analytics',
        'class'       => MailChimpController::class,
        'function'    => 'getAnalytics',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/MailChimp/CampaignTasks/{id}/createCampaign',
        'class'       => MailChimpController::class,
        'function'    => 'createCampaign',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'put',
        'route'       => '/MailChimp/CampaignTasks/{id}/setCampaign',
        'class'       => MailChimpController::class,
        'function'    => 'setCampaignContent',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
