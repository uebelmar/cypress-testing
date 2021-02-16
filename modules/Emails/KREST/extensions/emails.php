<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Emails\KREST\controllers\EmailsKRESTController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('emails', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Emails/saveGSuiteEmailWithBeans',
        'class'       => EmailsKRESTController::class,
        'function'    => 'saveGSuiteEmailWithBeans',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/saveOutlookEmailWithBeans',
        'class'       => EmailsKRESTController::class,
        'function'    => 'saveOutlookEmailWithBeans',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/getemail',
        'class'       => EmailsKRESTController::class,
        'function'    => 'getEmail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/saveOutlookAttachments',
        'class'       => EmailsKRESTController::class,
        'function'    => 'saveOutlookAttachments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/saveGSuiteAttachments',
        'class'       => EmailsKRESTController::class,
        'function'    => 'saveOutlookAttachments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/search',
        'class'       => EmailsKRESTController::class,
        'function'    => 'search',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/{id}/setstatus/{status}',
        'class'       => EmailsKRESTController::class,
        'function'    => 'setStatus',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/{id}/setopenness/{openness}',
        'class'       => EmailsKRESTController::class,
        'function'    => 'setOpenness',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/{id}/process',
        'class'       => EmailsKRESTController::class,
        'function'    => 'process',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/msg',
        'class'       => EmailsKRESTController::class,
        'function'    => 'createEmailFromMSGFile',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/msg/{attachmentId}/preview',
        'class'       => EmailsKRESTController::class,
        'function'    => 'previewMsgFromAttachment',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

