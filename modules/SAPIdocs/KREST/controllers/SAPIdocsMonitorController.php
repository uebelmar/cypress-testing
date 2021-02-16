<?php

namespace SpiceCRM\modules\SAPIdocs\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;

class SAPIdocsMonitorController
{

    /**
     * processes the idoc
     *
     * @param $req
     * @param $res
     * @param $args
     * @throws Exception
     */
    public function processIdoc($req, $res, $args)
    {
        $idoc = BeanFactory::getBean('SAPIdocs', $args['id']);

        $idoc->handleIncomingIdoc();

        return $res->withJson(['success' => true]);
    }

}
