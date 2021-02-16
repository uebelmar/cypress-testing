<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Currencies\KREST\controllers\CurrenciesKRESTcontroller;

/**
 * get a Rest Manager Instance
 */

$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('currencies', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/currencies',
        'class'       => CurrenciesKRESTcontroller::class,
        'function'    => 'getCurrencies',
        'description' => 'gets the default currency',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/currencies/defaultcurrency',
        'class'       => CurrenciesKRESTcontroller::class,
        'function'    => 'getDefaultCurrency',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/currencies/add',
        'class'       => CurrenciesKRESTcontroller::class,
        'function'    => 'addCurrency',
        'description' => 'adds a currency to a user',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);