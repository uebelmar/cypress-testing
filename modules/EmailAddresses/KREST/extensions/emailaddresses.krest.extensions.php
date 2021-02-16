<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\EmailAddresses\KREST\controllers\EmailadressKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('emailaddresses', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/EmailAddresses/{searchterm}',
        'class'       => EmailadressKRESTController::class,
        'function'    => 'searchMailAdress',
        'description' => 'searches for emails ',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],    [
        'method'      => 'post',
        'route'       => '/EmailAddress/searchBeans',
        'class'       => EmailadressKRESTController::class,
        'function'    => 'getMailText',
        'description' => 'get and parse the body of an email',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];


$RESTManager->registerRoutes($routes);