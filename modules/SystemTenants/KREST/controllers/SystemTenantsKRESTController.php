<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\SystemTenants\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\UnauthorizedException;
use SpiceCRM\includes\SpiceDemoData\SpiceDemoDataGenerator;

class SystemTenantsKRESTController
{
    /**
     * initializes the tenant
     *
     * @param $req
     * @param $res
     * @param $args
     */
    public function initialize($req, $res, $args){
        $tenant = BeanFactory::getBean('SystemTenants', $args['id']);
        $tenant->intializeTenant();
    }

    /**
     * loads demo data in a client
     *
     * @param $req
     * @param $res
     * @param $args
     */
    public function loadDemoData($req, $res, $args){
        global $sugar_config, $current_user;

        if(!$current_user->is_admin){
            throw new UnauthorizedException('only admin access');
        }

        $tenant = BeanFactory::getBean('SystemTenants', $args['id']);
        if($tenant) {
            $tenant->switchToTenant();
            $demoGenerator = new SpiceDemoDataGenerator();
            $demoGenerator->GenerateAccounts();
            $demoGenerator->GenerateContacts();
            $demoGenerator->GenerateConsumers();
            $demoGenerator->GenerateLeads();
            DBManagerFactory::switchInstance($sugar_config['db_config']['db_name']);
        }
    }
}
