<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\SystemTenants;


use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceInstaller\SpiceInstaller;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Administration\KREST\controllers\adminController;
use SpiceCRM\includes\authentication\AuthenticationController;

class SystemTenant extends SugarBean
{

    public $table_name = "systemtenants";
    public $object_name = "SystemTenant";
    public $module_dir = 'SystemTenants';

    /**
     * loads the tenant data from teh config for the loader to return to the frontend
     */
    public function getTenantData(){
        global $sugar_config;

        return $sugar_config['tenant'] ?: [];
    }

    /**
     * switches the tenant
     * called fromt he authentication
     */
    public function switchToTenant(){
        DBManagerFactory::switchInstance($this->id);

        // reloads the config
        SpiceConfig::getInstance()->reloadConfig();

        // unset the fts settings
        unset($_SESSION['SpiceFTS']);
    }

    /**
     * initializes a new tenant, sets up the database and builds all required tables
     */
    public function intializeTenant(){
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        if(!$current_user->is_admin) return false;

        $db = DBManagerFactory::getInstance();
        $db->createDatabase($this->id);

        // memorize the current db name so we can switch back after the new tenant has been initialized
        $preserved_db_name = SpiceConfig::getInstance()->config['dbconfig']['db_name'];

        // switch to ne database
        $db = DBManagerFactory::switchInstance($this->id);

        // run installer on new database
        $installer = new  SpiceInstaller($db);
        $installer->createTables($db);
        $installer->insertDefaults($db);
        // create local and in tenant

        // $installer->createCurrentUser($db, );

        $installer->retrieveCoreandLanguages($db, ['language'=>['language_code' => 'en_us']]);

        $admin = new adminController();
        $admin->repairAndRebuild(null, null, null);

        // set the fts setting
        $this->copyConfig($db, $sugar_config, 'fts');
        $this->copyConfig($db, $sugar_config, 'default_preferences');
        $this->copyConfig($db, $sugar_config, 'system');
        $this->copyConfig($db, $sugar_config, 'core');

        // switch back to current dabatase
        DBManagerFactory::switchInstance($preserved_db_name);

        return true;
    }

    /**
     * copy config values fromt eh current sugar config to the new config table
     *
     * @param $db
     * @param $config
     * @param $category
     */
    private function copyConfig($db, $config, $category){
        foreach($config[$category] as $name => $value){
            $db->query("INSERT INTO config (category, name, value) VALUES ('$category', '$name', '$value')");
        }
    }

    /**
     * handle tha after save event on teh user if the user has a tenant id
     *
     * @param $bean
     * @param $event
     * @param $arguments
     */
    public function handleUserAfterSaveHook(&$bean, $event, $arguments)
    {
        global $sugar_config;

        // if we have a user ina tenant and are not in the tenant
        // central user maintenance int eh master
        if(!empty($bean->systemtenant_id) && empty(AuthenticationController::getInstance()->systemtenantid)){
            $tenant = $this->retrieve($bean->systemtenant_id);
            if($tenant) {
                DBManagerFactory::switchInstance($tenant->id);

                // get a new user in the tenant and see if it exists
                $tenantuser = BeanFactory::getBean('Users');
                if(!$tenantuser->retrieve($bean->id)){
                    $tenantuser->new_with_id = true;
                };
                // map all fields
                foreach ($bean->field_defs as $fieldname => $fieldDefs){
                    if($fieldname == 'systemtenant_id' ||  $fieldDefs->type == 'link' || $fieldDefs->source == 'non_db') continue;
                    $tenantuser->{$fieldname} = $bean->{$fieldname};
                }
                // save user
                $tenantuser->save();

                // switch back
                DBManagerFactory::switchInstance($sugar_config['dbconfig']['db_name']);
            }
        }

        // if we are in a tenant update the central user record as well
        if(empty($bean->systemtenant_id) && !empty(AuthenticationController::getInstance()->systemtenantid)){
            // switchto the master database
            DBManagerFactory::switchInstance($sugar_config['dbconfig']['db_name']);
            $masterUser = BeanFactory::getBean('Users', $bean->id);
            // map all fields
            foreach ($bean->field_defs as $fieldname => $fieldDefs){
                if($fieldname == 'systemtenant_id' ||  $fieldDefs->type == 'link' || $fieldDefs->source == 'non_db') continue;
                $masterUser->{$fieldname} = $bean->{$fieldname};
            }
            // save user
            $masterUser->save();
            DBManagerFactory::switchInstance(AuthenticationController::getInstance()->systemtenantid);
        }
    }
}
