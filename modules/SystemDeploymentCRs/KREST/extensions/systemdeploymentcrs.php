<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\SystemDeploymentCRs\KREST\controllers\SystemDeploymentCrsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */

$RESTManager->registerExtension('deployment', '2.0', ['change_request_required' => SpiceConfig::getInstance()->config['workbench_edit_mode']['change_request_required'] ? true : false]);


$routes = [
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/getFiles',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getFiles',
        'description' => 'Get Files',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/getDetailFiles',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getDetailFiles',
        'description' => 'Get Detail Files',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/getCommits',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getCommits',
        'description' => 'Get Commits',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/getBranches',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getBranches',
        'description' => 'Get Branches',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/getTables',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getTables',
        'description' => 'Get Tables',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/getDetailDBEntries/{id}',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getDetailDBEntries',
        'description' => 'Get Detail DB Entries',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/active',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'active',
        'description' => 'Active',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/systemdeploymentcrs/active/{id}',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'setActive',
        'description' => 'Set Active',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/systemdeploymentcrs/active/{id}',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'deleteActive',
        'description' => 'Delete Active',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/sql/{id}',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getSql',
        'description' => 'Get Sql',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/getDBEntries',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'getDBEntries',
        'description' => 'Get DB Entries',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/systemdeploymentcrs/appConfig',
        'class'       => SystemDeploymentCrsController::class,
        'function'    => 'appConfig',
        'description' => 'Get Config',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);


//$RESTManager->app->group('/systemdeploymentcrs', function (RouteCollectorProxy $group) {

//    $group->get('/getFiles', function ($req, $res, $args) {
//        $getParams = $_GET;
//        $cr = BeanFactory::getBean('SystemDeploymentCRs');
//        $files = $cr->getFiles($getParams);
//        return $res->withJson($files);
//    });

//    $group->get('/getDetailFiles', function ($req, $res, $args) {
//        $getParams = $_GET;
//        $cr = BeanFactory::getBean('SystemDeploymentCRs');
//        $files = $cr->getDetailFiles($getParams);
//        return $res->withJson($files);
//    });

//    $group->get('/getCommits', function ($req, $res, $args) {
//        $getParams = $_GET;
//        $cr = BeanFactory::getBean('SystemDeploymentCRs');
//        $commits = $cr->getCommits($getParams);
//        return $res->withJson($commits);
//    });

//    $group->get('/getBranches', function ($req, $res, $args) {
//        $getParams = $_GET;
//        $cr = BeanFactory::getBean('SystemDeploymentCRs');
//        $branches = $cr->getBranches($getParams);
//        return $res->withJson(array('list' => $branches));
//    });

//    $group->get('/getTables', function ($req, $res, $args) {
//        $getParams = $_GET;
//        $cr = BeanFactory::getBean('SystemDeploymentCRs');
//        $branches = $cr->getTables($getParams);
//        return $res->withJson(array('list' => $branches));
//    });

//    $group->get('/getDetailDBEntries/{id}',  function($req, $res, $args) {
//        if($cr = BeanFactory::getBean('SystemDeploymentCRs', $args['id']))
//            $files = $cr->getDetailDBEntries();
//        return $res->withJson($files);
//    });

//    $group->group('/active', function (RouteCollectorProxy $group) {
//        $group->get('', function ($req, $res, $args) {
//            if ($_SESSION['SystemDeploymentCRsActiveCR']) {
//                $cr = BeanFactory::getBean('SystemDeploymentCRs', $_SESSION['SystemDeploymentCRsActiveCR']);
//            }
//            return $res->withJson([
//                'id' => $_SESSION['SystemDeploymentCRsActiveCR'] ?: '',
//                'name' => $cr->name ?: ''
//            ]);
//        });
//        $group->post('/{id}',  function($req, $res, $args) {
//            $_SESSION['SystemDeploymentCRsActiveCR'] = $args['id'];
//            return $res->withJson(['status' => 'success']);
//        });
//        $group->delete('', function ($req, $res, $args) {
//            unset($_SESSION['SystemDeploymentCRsActiveCR']);
//            return $res->withJson(['status' => 'success']);
//        });
//    });

//    $group->get('/sql/{id}',  function($req, $res, $args) {
//        $cr = BeanFactory::getBean('SystemDeploymentCRs', $args['id']);
//        $sql = $cr->getDBEntriesSQL();
//        return $res->withJson(['sql' => $sql]);
//    });

//    $group->get('/getDBEntries', function ($req, $res, $args) {
//        $getParams = $_GET;
//        $cr = BeanFactory::getBean('SystemDeploymentCRs');
//        $files = $cr->getDBEntries($getParams);
//        return $res->withJson($files);
//    });

//    $group->get('/appConfig', function ($req, $res, $args) {
//        $getParams = $_GET;
//        $cr = BeanFactory::getBean('SystemDeploymentCRs');
//        $conf = $cr->getAppConfig();
//        return $res->withJson($conf);
//    });

//});
