<?php

namespace SpiceCRM\modules\OrgUnits\KREST\controllers;

use SpiceCRM\includes\RESTManager;
use Psr\Http\Message\RequestInterface;
use SpiceCRM\includes\SpiceSlim\SpiceResponse;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\KREST\handlers\ModuleHandler;

class OrgUnitsController{

    /**
     *
     * @param $req RequestInterface
     * @param $res SpiceResponse
     * @param $args
     * @return mixed
     */

    public function GetBeanFullList($req, $res, $args){
        $RESTManager = RESTManager::getInstance();
        $handler = new ModuleHandler($RESTManager->app);
        $bean = BeanFactory::getBean('OrgUnits');
        $list = $bean->get_full_list('name');
        $result = [];
        foreach($list as $row)
            $result[] = $handler->mapBeanToArray('OrgUnits', $row);

        return $res->withJson($result);
    }
}