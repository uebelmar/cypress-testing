<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\GoogleOAuth\KREST\controllers\GoogleOauthController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('google_oauth', '1.0', [
    'clientid'      => SpiceConfig::getInstance()->config['googleapi']['clientid'],
    'serviceaccess' => isset(SpiceConfig::getInstance()->config['googleapi']['serviceuserkey'])
]);

$routes = [
    [
        'method'      => 'get',
        'route'       => '/google_oauth/token',
        'class'       => GoogleOauthController::class,
        'function'    => 'GoogleAuthSetToken',
        'description' => 'saves an authorisation token',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/google_oauth/credentials',
        'class'       => GoogleOauthController::class,
        'function'    => 'GoogleAuthGetCredential',
        'description' => 'authenticates with an password',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/google_oauth/archive_email',
        'class'       => GoogleOauthController::class,
        'function'    => 'GoogleAuthArchiveMail',
        'description' => 'archives an email',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

