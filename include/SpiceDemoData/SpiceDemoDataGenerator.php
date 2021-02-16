<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\includes\SpiceDemoData;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

/**
 * Class SpiceDemoDataGenerator
 *
 * this class supports generation of demo data using mockaroo.com as a service
 *
 * @package SpiceCRM\includes\SpiceDemoData
 */
class SpiceDemoDataGenerator
{
    var $key = '08e28c80';

    public function generateForModule($req, $res, $args){
        switch ($args['module']){
            case 'Accounts':
                $this->GenerateAccounts();
                break;
            case 'Contacts':
                $this->GenerateContacts();
                break;
            case 'Consumers':
                $this->GenerateConsumers();
                break;
            case 'Leads':
                $this->GenerateLeads();
                break;
        }
        return $res->withJson(['status' => 'success']);
    }

    /**
     * generates Accounts
     */
    public function GenerateAccounts(){
        $accounts = $this->makeCall('accounts');
        foreach($accounts as $account){
            $seed = BeanFactory::getBean('Accounts');
            foreach($seed->field_defs as $fieldName => $fieldData){
                if(isset($account[$fieldName])){
                    $seed->{$fieldName} = $account[$fieldName];
                }
            }
            if(!empty($seed->id)){
                $seed->new_with_id = true;
            }

            // populate some default values
            $this->popuplateDefaults($seed);

            // save the bean
            $seed->save();
        }
    }

    /**
     * generates Accounts
     */
    public function GenerateContacts(){
        $db = DBManagerFactory::getInstance();

        $contacts = $this->makeCall('contacts');
        foreach($contacts as $contact){
            $seed = BeanFactory::getBean('Contacts');
            foreach($seed->field_defs as $fieldName => $fieldData){
                if(isset($contact[$fieldName])){
                    $seed->{$fieldName} = $contact[$fieldName];
                }
            }
            if(!empty($seed->id)){
                $seed->new_with_id = true;
            }

            // populate some default values
            $this->popuplateDefaults($seed);

            // save the bean
            $seed->save();

            $seed->load_relationship('accounts');
            $account = $db->fetchByAssoc($db->query("SELECT id FROM accounts WHERE billing_address_country = '$seed->primary_address_country' ORDER BY RAND() LIMIT 1"));
            $seed->accounts->add($account['id']);
        }
    }

    /**
     * generates Accounts
     */
    public function GenerateConsumers(){
        $db = DBManagerFactory::getInstance();

        $contacts = $this->makeCall('consumers');
        foreach($contacts as $contact){
            $seed = BeanFactory::getBean('Consumers');
            foreach($seed->field_defs as $fieldName => $fieldData){
                if(isset($contact[$fieldName])){
                    $seed->{$fieldName} = $contact[$fieldName];
                }
            }
            if(!empty($seed->id)){
                $seed->new_with_id = true;
            }

            // populate some default values
            $this->popuplateDefaults($seed);

            // save the bean
            $seed->save();
        }
    }

    /**
     * generates Leads
     */
    public function GenerateLeads(){
        $db = DBManagerFactory::getInstance();

        $leads = $this->makeCall('leads');
        foreach($leads as $lead){
            $seed = BeanFactory::getBean('Leads');
            foreach($seed->field_defs as $fieldName => $fieldData){
                if(isset($lead[$fieldName])){
                    $seed->{$fieldName} = $lead[$fieldName];
                }
            }
            if(!empty($seed->id)){
                $seed->new_with_id = true;
            }

            // populate some default values
            $this->popuplateDefaults($seed);

            // save the bean
            $seed->save();
        }
    }

    private function  popuplateDefaults(&$seed){
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $seed->assigned_user_id = $current_user->id;
    }

    private function makeCall($api){
        $cURL = "https://my.api.mockaroo.com/$api.json?key=08e28c80";
        $ch = curl_init($cURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        return json_decode($result, true);
    }
}
