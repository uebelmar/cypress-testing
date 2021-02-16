<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceDemoData\SpiceDemoDataGenerator;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();
/**
 * register the Extension
 */
$RESTManager->registerExtension('generadetdemodata', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/generadetdemodata',
        'class'       => SpiceDemoDataGenerator::class,
        'function'    => 'generateAll',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/generadetdemodata/{module',
        'class'       => SpiceDemoDataGenerator::class,
        'function'    => 'generateForModule',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
