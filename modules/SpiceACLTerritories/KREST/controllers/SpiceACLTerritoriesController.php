<?php
namespace SpiceCRM\modules\SpiceACLTerritories\KREST\controllers;

use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;
use SpiceCRM\modules\SpiceACLTerritories\SpiceACLTerritoriesRESTHandler;


class SpiceACLTerritoriesController
{

    /**
     * Territories
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function territories($req, $res, $args)
    {

        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $activeTerritories = json_decode(html_entity_decode($req->getParams()['activeterritories']), true);
        return $res->withJson($spiceACLTerritoriesRESTHandler->getUserTerritories($activeTerritories));

    }

    /**
     * Territory
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function territory($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getTerritory($args['id']));
    }

    /**
     * Territory Hash
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function territoryHash($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getTerritoriesByHash($args['hash_id']));
    }

    /**
     * Spice ACL Territories
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritories($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getTerritorries());
    }

    /**
     * Spice ACL Territories Org Elements
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesOrgElements($req, $res, $args){

        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();
        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgElements());
    }

    /**
     * Spice ACL Territories Set Org Elements
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesSetOrgElements($req, $res, $args)
    {

        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson($spiceACLTerritoriesRESTHandler->setOrgElement($args['id'], $postParams));
    }

    /**
     * Spice ACL Territories Delete Org Elements
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesDeleteOrgElements($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->deleteOrgElement($args['id']));
    }

    /**
     * Spice ACL Territories Get Org Element Values
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesGetOrgElementValues($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgElementValues($args['orgelementid']));
    }

    /**
     * Spice ACL Territories Set Org Element Values
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesSetOrgElementValues($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson($spiceACLTerritoriesRESTHandler->setOrgElementValues($postParams));
    }

    /**
     * Spice ACL Territories Delete Org Element Values
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesDeleteOrgElementValues($req, $res, $args)
    {

        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->deleteOrgElementValues($args['spiceaclterritoryelement_id'], $args['elementvalue']));

    }

    /**
     * Spice ACL Territories Get Org Object Types
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesGetOrgObjectsTypes($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgObjectTypes());
    }

    /**
     * Spice ACL Territories Get Org Object Type
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesGetOrgObjectsType($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgObjectType($args['id']));
    }

    /**
     * Spice ACL Territories Set Org Object Type
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesSetOrgObjectsType($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson($spiceACLTerritoriesRESTHandler->setOrgObjectTypes($args['id'], $postParams));
    }

    /**
     * Spice ACL Territories Delete Org Object Type
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesDeleteOrgObjectsType($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->deleteOrgObjectType($args['id']));
    }

    /**
     * Spice ACL Territories Get Org Object Type by Module
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesGetOrgObjectTypeByModule($req, $res, $args)
    {

        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgObjectTypeByModule($args['module']));
    }

    /**
     * Spice ACL Territories Get Org Object Type Elements
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesGetOrgObjectTypeElements($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgObjectTypeElements($args['spiceaclterritorytype_id']));

    }

    /**
     * Spice ACL Territories Set Org Object Type Elements
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesSetOrgObjectTypeElements($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson($spiceACLTerritoriesRESTHandler->setOrgObjectTypeElement($postParams));
    }

    /**
     * Spice ACL Territories Delete Org Object Type Elements
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesDeleteOrgObjectTypeElements($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->deleteOrgObjectTypeElements($args['spiceaclterritoryelement_id'], $args['spiceaclterritorytype_id']));
    }

    /**
     * Spice ACL Territories Get Org Object Type Modules
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesGetOrgObjectTypeModules($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgObjectTypeModules());
    }

    /**
     * Spice ACL Territories Set Org Object Type Modules
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesSetOrgObjectTypeModules($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson($spiceACLTerritoriesRESTHandler->setOrgObjectTypeModule($postParams));
    }

    /**
     * Spice ACL Territories Set Org Object Types
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesSetOrgObjectTypes($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson($spiceACLTerritoriesRESTHandler->setOrgObjectTypes($postParams));
    }

    /**
     * Spice ACL Territories Delete Org Object Type Modules
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTerritoriesDeleteOrgObjectTypeModules($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->deleteOrgObjectTypeModules($args['module']));
    }


    /**
     * Spice ACL Get Territories
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclGetTeritories($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $getParams = $req->getQueryParams();
        return $res->withJson($spiceACLTerritoriesRESTHandler->getTerritories($getParams));
    }

    /**
     * Spice ACL Get Territories for Module
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclGetTeritoriesForModule($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $getParams = $req->getQueryParams();
        $getParams = array_merge($getParams, $args);
        return $res->withJson($spiceACLTerritoriesRESTHandler->getTerritoriesForModule($getParams));
    }

    /**
     * Spice ACL Territories Check
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTeritoriesCheck($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson(['status' => $spiceACLTerritoriesRESTHandler->checkTerritory($postParams) ? 'success': 'error']);

    }

    /**
     * Spice ACL Add Territories
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclAddTeritories($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        $postParams = $req->getParsedBody();
        return $res->withJson($spiceACLTerritoriesRESTHandler->addTerritory($args['id'], $postParams));
    }

    /**
     * Spice ACL Delete Territorry
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclDeleteTeritorry($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->deleteTerritorry($args['id']));
    }

    /**
     * Spice ACL Territorry Get Object Values
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function spiceAclTeritorryGetOrgObjectValues($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();

        return $res->withJson($spiceACLTerritoriesRESTHandler->getOrgObjectValues($args['objectid']));
    }

    /**
     * Get Spice ACL Territorry
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     */
    public function getSpiceAclTeritorry($req, $res, $args)
    {
        /**
         * get a Spice ACL Territories Handler
         */
        $spiceACLTerritoriesRESTHandler = new SpiceACLTerritoriesRESTHandler();
        return $res->withJson($spiceACLTerritoriesRESTHandler->getTerritory($args['id']));
    }

}