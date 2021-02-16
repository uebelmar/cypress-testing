<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\GoogleLanguage\KREST\controllers\GcnlController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('google_language', '1.0', SpiceConfig::getInstance()->config['googlelanguage']);


$routes = [
    [
        'method'      => 'post',
        'route'       => '/google/language/analyzesentiment',
        'class'       => GcnlController::class,
        'function'    => 'GcnlAnalyzeSentTime',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

