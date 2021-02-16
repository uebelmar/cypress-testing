<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\google\KREST\controllers\GoogleApiController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('google_api', '1.0', ['key' => SpiceConfig::getInstance()->config['googleapi']['mapskey']? 'xxx' : '']);

$routes = [
    [
        'method'      => 'get',
        'route'       => '/googleapi/places/search/{term}/{locationbias}',
        'class'       => GoogleApiController::class,
        'function'    => 'GoogleApiSearch',
        'description' => 'start a search',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/googleapi/places/autocomplete/{term}',
        'class'       => GoogleApiController::class,
        'function'    => 'GoogleApiAutocomplete',
        'description' => 'get the autocompletion',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/googleapi/places/{placeid}',
        'class'       => GoogleApiController::class,
        'function'    => 'GoogleApiGetPlaceDetails',
        'description' => 'get the details of a place',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

