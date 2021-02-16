<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceUI\SpiceUIRESTHandler;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\includes\SpiceUI\KREST\controllers\SpiceUIConfigTransfer;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('configtransfer', '1.0');

/**
 * restrict routes to authenticated users
 */

$routes = [
    [
        'method'      => 'get',
        'route'       => '/configtransfer/tablenames',
        'class'       => SpiceUIConfigTransfer::class,
        'function'    => 'getSelectableTablenames',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/configtransfer/data/export',
        'class'       => SpiceUIConfigTransfer::class,
        'function'    => 'exportFromTables',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/configtransfer/data/import',
        'class'       => SpiceUIConfigTransfer::class,
        'function'    => 'importToTables',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => true],
    ],
];

$RESTManager->registerRoutes($routes);
