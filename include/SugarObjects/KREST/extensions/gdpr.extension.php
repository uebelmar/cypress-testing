<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\includes\SugarObjects\KREST\controllers\gdprController;
use Slim\Routing\RouteCollectorProxy;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('gdpr', '2.0');

/**
 * restrict routes to authenticated users
 */
if(!SpiceCRM\includes\authentication\AuthenticationController::getInstance()->isAuthenticated()) return;


$KRESTModuleHandler = new ModuleHandler($RESTManager->app);

$RESTManager->app->group('/gdpr', function (RouteCollectorProxy $group) use ($RESTManager, $KRESTModuleHandler) {

    $group->get('/{module}/{id}', function ($req, $res, $args) use ($KRESTModuleHandler) {
        $seed = BeanFactory::getBean($args['module'], $args['id']);
        if(!$seed){
            throw new NotFoundException();
        }

        if(!$seed->ACLAccess('detail')){
            throw new ForbiddenException();
        }

        if(method_exists($seed, 'getGDPRRelease')){
            return $res->withJson($seed->getGDPRRelease());
        } else {
            return $res->withJson([]);
        }
    });

    /*
     * Get the GDPR consent text for portal user from the CRM configuration.
     */
    $group->get('/portalGDPRconsentText', [new gdprController(), 'getPortalGDPRconsentText']);

    /*
     * Saves the GDPR consent of a portal user.
     */
    $group->post('/portalGDPRconsent', [new gdprController(), 'setPortalGDPRconsent']);

});
