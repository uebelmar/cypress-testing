<?php

namespace SpiceCRM\modules\Potentials\KREST\controllers;

use SpiceCRM\custom\modules\Potentials\KREST\helpers\PotentialsKRESTHelper;//todo-uebelmar class does not exist
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\modules\SpiceACL\SpiceACL;

class PotentialsKRESTController
{

    public static function getRevenues($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();

        // check that we can view the account
        $account = BeanFactory::getBean('Accounts', $args['accountid']);
        if (!$account) {
            throw (new ForbiddenException('no access to account'))->setErrorCode('noModuleEdit');
        }

        if (!SpiceACL::getInstance()->checkAccess('SalesDocuments', 'list', true)) {
            throw (new ForbiddenException("no access to module SalesDocuments"))->setErrorCode('noModuleList');
        }

        // process if access is granted
        $retArray = [];


        // CR1000381 make getRevenues Query customizable
        // this is a workaround until we have a better solution
        $q = "SELECT products.productgroup_id, MAX(productgroups.name) productgroup_name, COUNT(salesdocitems.id) itemcount, SUM(amount_net) realizedrevenue FROM salesdocitems, salesdocs, productvariants, products, productgroups WHERE salesdocitems.salesdoc_id = salesdocs.id AND salesdocitems.deleted = 0 AND salesdocs.deleted = 0 AND salesdocs.companycode_id = '{$args['companycode']}' AND salesdocs.account_op_id = '{$args['accountid']}' AND salesdocitems.productvariant_id = productvariants.id AND productvariants.product_id = products.id AND products.productgroup_id = productgroups.id GROUP BY products.productgroup_id";

        if(class_exists('SpiceCRM\custom\modules\Potentials\KREST\helpers\PotentialsKRESTHelper')){
            $q = PotentialsKRESTHelper::getQueryForRevenues($args);
        }

        $totals = $db->query($q);

        while ($total = $db->fetchByAssoc($totals)) {
            $retArray[] = [
                'productgroup_id' => $total['productgroup_id'],
                'productgroup_name' => $total['productgroup_name'],
                'productgroup_revenue' => $total['realizedrevenue'],
                'productgroup_itemcount' => $total['itemcount']
            ];
        }

        return $res->withJson($retArray);
    }
}
