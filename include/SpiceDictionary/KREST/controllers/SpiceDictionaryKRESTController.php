<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SpiceDictionary\KREST\controllers;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\UnauthorizedException;
use SpiceCRM\includes\SpiceDictionary\SpiceDictionaryHandler;
use SpiceCRM\includes\authentication\AuthenticationController;

/**
 * handles the dictioonary elements
 *
 * Class SpiceDictionaryKRESTController
 * @package SpiceCRM\includes\SpiceDictionary\KREST\controllers
 */
class SpiceDictionaryKRESTController
{

    /**
     * retrieves the domain defintions
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getDomains($req, $res, $args)
    {
        $handler = new SpiceDictionaryHandler();
        $results = [
            'domaindefinitions' => $handler->getDomainDefinitions(),
            'domainfields' => $handler->getDomainFields(),
            'domainfieldvalidations' => $handler->getDomainFieldValidations(),
            'domainfieldvalidationvalues' => $handler->getDomainFieldValidationValues()
        ];
        return $res->withJson($results);
    }

    /**
     * post the domain changes
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function postDomains($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        // check that we are an admin
        if(!$current_user->is_admin){
            throw new UnauthorizedException('Admin Access Only');
        }

        $handler = new SpiceDictionaryHandler();

        // get the body
        $body = $req->getParsedBody();

        $handler->setDomainDefinitions($body['domaindefinitions']);
        $handler->setDomainFields($body['domainfields']);
        $handler->setDomainFieldValidations($body['domainfieldvalidations']);
        $handler->setDomainFieldValidationValues($body['domainfieldvalidationvalues']);

        if($body['languagelabels']){
            $handler->postLanguageLabels($body['languagelabels'], $body['languagetranslations']);
        }

        if($body['languagecustomlabels']){
            $handler->postLanguageCustomLabels($body['languagecustomlabels'], $body['languagecustomtranslations']);
        }

        $results = [
            'domaindefinitions' => $handler->getDomainDefinitions(),
            'domainfields' => $handler->getDomainFields(),
            'domainfieldvalidations' => $handler->getDomainFieldValidations(),
            'domainfieldvalidationvalues' => $handler->getDomainFieldValidationValues()
        ];

        // remove from the session
        unset($_SESSION['systemvardefs']['domains']);

        // return the response
        return $res->withJson($results);
    }

    public function getDefinitions($req, $res, $args)
    {
        $handler = new SpiceDictionaryHandler();
        $results = [
            'domaindefinitions' => $handler->getDomainDefinitions(),
            'domainfields' => $handler->getDomainFields(),
            'dictionarydefinitions' => $handler->getDictionaryDefinitions(),
            'dictionaryitems' => $handler->getDictionaryItems(),
            'dictionaryrelationships' => $handler->getDictionaryRelationships(),
            'dictionaryrelationshiprelatefields' => $handler->getDictionaryRelateFields(),
            'dictionaryrelationshipfields' => $handler->getDictionaryRelationshipFields(),
            'dictionaryindexes' => $handler->getDictionaryIndexes(),
            'dictionaryindexitems' => $handler->getDictionaryIndexItems()
        ];
        return $res->withJson($results);
    }


    /**
     * post the domain changes
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function postDefinitions($req, $res, $args)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        // check that we are an admin
        if(!$current_user->is_admin){
            throw new UnauthorizedException('Admin Access Only');
        }

        $handler = new SpiceDictionaryHandler();

        // get the body
        $body = $req->getParsedBody();

        $handler->setDictionaryDefinitions($body['dictionarydefinitions']);
        $handler->setDictionaryItems($body['dictionaryitems']);
        $handler->setDictionaryRelationships($body['dictionaryrelationships']);
        $handler->setDictionaryRelationshipFields($body['dictionaryrelationshipfields']);
        $handler->setDictionaryRelateFields($body['dictionaryrelationshiprelatefields']);
        $handler->setDictionaryIndexes($body['dictionaryindexes']);
        $handler->setDictionaryIndexItems($body['dictionaryindexitems']);

        $results = [
            'domaindefinitions' => $handler->getDomainDefinitions(),
            'domainfields' => $handler->getDomainFields(),
            'dictionarydefinitions' => $handler->getDictionaryDefinitions(),
            'dictionaryitems' => $handler->getDictionaryItems(),
            'dictionaryrelationships' => $handler->getDictionaryRelationships(),
            'dictionaryrelationshiprelatefields' => $handler->getDictionaryRelateFields(),
            'dictionaryrelationshipfields' => $handler->getDictionaryRelationshipFields(),
            'dictionaryindexes' => $handler->getDictionaryIndexes(),
            'dictionaryindexitems' => $handler->getDictionaryIndexItems()
        ];
        return $res->withJson($results);
    }

    public function getAppListStrings($req, $res, $args){
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();

        // check that we are an admin
        if(!$current_user->is_admin){
            throw new UnauthorizedException('Admin Access Only');
        }

        $retArray = [];

        $languages = $db->query("SELECT language_code FROM syslangs WHERE system_language = '1'");
        while($language = $db->fetchByAssoc($languages)){
            $retArray[$language['language_code']]['global'] = return_app_list_strings_language('en_us', 'global');
            $retArray[$language['language_code']]['custom'] = return_app_list_strings_language('en_us', 'custom');


            foreach($retArray[$language['language_code']]['custom'] as $dom => $values){
                if(isset($retArray[$language['language_code']]['global'][$dom])){
                    unset($retArray[$language['language_code']]['global'][$dom]);
                }
            }

        }

        return $res->withJson($retArray);
    }



}
