<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceLanguages\KREST\controllers\SpiceLanguageController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('syslanguages', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/syslanguages/labels',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageSaveLabel',
        'description' => 'saves the labels',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/syslanguages/labels/{id}/[{environment}]',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageDeleteLabel',
        'description' => 'deletes a label name',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/syslanguages/labels/search/{search_term}',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageSearchLabel',
        'description' => 'search for a label',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/syslanguages/labels/{label_name}',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageGetLabel',
        'description' => 'gets a label by name',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/syslanguages/load/{language}',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageLoadDefault',
        'description' => 'loads the default language',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/syslanguages/setdefault/{language}',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageSetDefault',
        'description' => 'sets a default language',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/syslanguages/filesToDB',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageTransferToDB',
        'description' => 'transfers value from a file to a database',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/syslanguage/{language}/{scope}/labels/untranslated',
        'class'       => SpiceLanguageController::class,
        'function'    => 'LanguageGetRawLabels',
        'description' => 'et the untranslated labels',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
