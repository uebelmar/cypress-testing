<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SpiceDictionary\KREST\controllers;

use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\RESTManager;


class MetadataRESTController {

    /** @var ModuleHandler|null */
    public $moduleHandler = null;

    /**
     * MetadataRESTController constructor.
     * initialize moduleHandler
     */
    public function __construct()
    {
        $RESTManager = RESTManager::getInstance();
        $this->moduleHandler = new ModuleHandler($RESTManager);
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getModules($req, $res, $args)
    {
        return $res->withJson($this->moduleHandler->get_modules());
    }

    /**
     * get variable definitions for a specific module
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getVarDefsForModule($req, $res, $args)
    {
        $bean = BeanFactory::getBean($args['module']);
        return $res->withJson($bean->field_name_map);
    }

}