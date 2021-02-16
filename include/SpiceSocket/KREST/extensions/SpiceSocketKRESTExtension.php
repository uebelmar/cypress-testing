<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('socket', '1.0', [
    'socket_frontend' => SpiceConfig::getInstance()->config['core']['socket_frontend'],
    'socket_id'       => SpiceConfig::getInstance()->config['core']['socket_id']
]);


