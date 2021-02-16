<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceFavorites\KREST\controllers\SpiceFavoritesKRESTController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('spicefavorites', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/SpiceFavorites',
        'class'       => SpiceFavoritesKRESTController::class,
        'function'    => 'getFavorites',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/SpiceFavorites',
        'class'       => SpiceFavoritesKRESTController::class,
        'function'    => 'addFavorite',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/SpiceFavorites',
        'class'       => SpiceFavoritesKRESTController::class,
        'function'    => 'deleteFavorite',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);
