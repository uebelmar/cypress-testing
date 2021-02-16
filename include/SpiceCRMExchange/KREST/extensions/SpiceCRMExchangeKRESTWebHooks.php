<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use Slim\Routing\RouteCollectorProxy;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceCRMExchange\KREST\controllers\SpiceCRMExchangeKRESTController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

switch($RESTManager->app::VERSION) {
    case '2.6.1': // slim 2 - just 1 route needed
        $SpiceCRMExchangeKRESTController = new SpiceCRMExchangeKRESTController();
        $RESTManager->app->group('/ewswebhooks', function () use ($RESTManager, $SpiceCRMExchangeKRESTController) {
            $this->post('/handler', function () use ($RESTManager, $SpiceCRMExchangeKRESTController){
                $response = $SpiceCRMExchangeKRESTController->handleSlim2($RESTManager->app->request->getBody());
//                echo $response;
            });
        });
        break;
    case '3.9.2': // slim 3
        $RESTManager->app->group('/ewswebhooks', function () {
            $this->post('/handler', [new SpiceCRMExchangeKRESTController() , 'handle']);
        });
        break;
    default : // slim 4
        $routes = [
            [
                'method'      => 'post',
                'route'       => '/ewswebhooks/handler',
                'class'       => SpiceCRMExchangeKRESTController::class,
                'function'    => 'handle',
                'description' => 'Handles incoming EWS events.',
                'options'     => ['noAuth' => true, 'adminOnly' => false],
            ],
        ];

        $RESTManager->registerRoutes($routes);
        break;
}




