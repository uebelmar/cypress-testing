<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\EmailTemplates\KREST\controllers\EmailTemplatesController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('emailtemplates', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/EmailTemplates/{module}',
        'class'       => EmailTemplatesController::class,
        'function'    => 'LoadEmailTemplate',
        'description' => 'loads an email template',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/EmailTemplates/parse/{id}/{module}/{parent}',
        'class'       => EmailTemplatesController::class,
        'function'    => 'FormatEmail',
        'description' => 'formats the email',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/EmailTemplates/liveCompile/{module}/{parent}',
        'class'       => EmailTemplatesController::class,
        'function'    => 'GetEmailBody',
        'description' => 'gets the body of an email',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],

];
$RESTManager->registerRoutes($routes);

