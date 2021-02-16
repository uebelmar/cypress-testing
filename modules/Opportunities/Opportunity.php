<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\Opportunities;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;


class Opportunity extends SugarBean
{

    var $table_name = "opportunities";
    var $rel_account_table = "accounts_opportunities";
    var $rel_contact_table = "opportunities_contacts";
    var $module_dir = "Opportunities";
    var $object_name = "Opportunity";

    // This is used to retrieve related fields from form posts.
    var $additional_column_fields = array('assigned_user_name', 'assigned_user_id', 'account_name', 'account_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id'
    );

    var $relationship_fields = array(
        'task_id' => 'tasks',
        'note_id' => 'notes',
        'account_id' => 'accounts',
        'meeting_id' => 'meetings',
        'call_id' => 'calls',
        'email_id' => 'emails',
        'project_id' => 'projects', //@deprecated project. use projects
        // Bug 38529 & 40938
        'currency_id' => 'currencies',
    );

    public function __construct()
    {
        parent::__construct();
        
        if (!SpiceConfig::getInstance()->config['require_accounts']) {
            unset($this->required_fields['account_name']);
        }
    }


    function fill_in_additional_list_fields()
    {
        if ($this->force_load_details == true) {
            $this->fill_in_additional_detail_fields();
        }
    }

    function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();

        if (!empty($this->currency_id)) {
            $currency = BeanFactory::getBean('Currencies');
            $currency->retrieve($this->currency_id);
            if ($currency->id != $this->currency_id || $currency->deleted == 1) {
                $this->amount = $this->amount_usdollar;
                $this->currency_id = $currency->id;
            }
        }
        //get campaign name
        if (!empty($this->campaign_id)) {
            $camp = BeanFactory::getBean('Campaigns');
            $camp->retrieve($this->campaign_id);
            $this->campaign_name = $camp->name;
        }
        $this->account_name = '';
        $this->account_id = '';
        if (!empty($this->id)) {
            $ret_values = Opportunity::get_account_detail($this->id);
            if (!empty($ret_values)) {
                $this->account_name = $ret_values['name'];
                $this->account_id = $ret_values['id'];
                $this->account_id_owner = $ret_values['assigned_user_id'];
            }
        }
    }


    function save($check_notify = FALSE, $fts_index_bean = TRUE)
    {
        // Bug 32581 - Make sure the currency_id is set to something
        global $app_list_strings;
$current_user = AuthenticationController::getInstance()->getCurrentUser();

        if (empty($this->currency_id))
            $this->currency_id = $current_user->getPreference('currency');
        if (empty($this->currency_id))
            $this->currency_id = -99;

        //if probablity isn't set, set it based on the sales stage
        if (!isset($this->probability) && !empty($this->sales_stage)) {
            $prob_arr = $app_list_strings['sales_probability_dom'];
            if (isset($prob_arr[$this->sales_stage]))
                $this->probability = $prob_arr[$this->sales_stage];
        }

        //US DOLLAR
        if (isset($this->amount) && !empty($value)) {
            $currency = BeanFactory::getBean('Currencies');
            $currency->retrieve($this->currency_id);
            $this->amount_usdollar = $currency->convertToDollar($this->amount);
        }

        if (!$this->in_save && ($this->sales_stage != $this->fetched_row['sales_stage'] || $this->date_closed != $this->fetched_row['date_closed'] || $this->probability != $this->fetched_row['probability'] || $this->forecast != $this->fetched_row['forecast'])) {
            $oppStage = BeanFactory::getBean('OpportunityStages');
            if ($oppStage) {
                if (empty($this->id)) {
                    $this->id = create_guid();
                    $this->new_with_id = true;
                }

                $oppStage->sales_stage = $this->sales_stage;
                $oppStage->amount = $this->amount;
                $oppStage->amount_usdollar = $this->amount_usdollar;
                $oppStage->forecast = $this->forecast;
                $oppStage->budget = $this->budget;
                $oppStage->bestcase = $this->bestcase;
                $oppStage->worstcase = $this->worstcase;
                $oppStage->currency_id = $this->currency_id;
                $oppStage->date_closed = $this->date_closed;
                $oppStage->probability = $this->probability;
                $oppStage->opportunity_id = $this->id;
                $oppStage->save();
            }
        }

        return parent::save($check_notify, $fts_index_bean);

    }

    function save_relationship_changes($is_update, $exclude = [])
    {
        //if account_id was replaced unlink the previous account_id.
        //this rel_fields_before_value is populated by sugarbean during the retrieve call.
        if (!empty($this->account_id) and !empty($this->rel_fields_before_value['account_id']) and
            (trim($this->account_id) != trim($this->rel_fields_before_value['account_id']))) {
            //unlink the old record.
            $this->load_relationship('accounts');
            $this->accounts->delete($this->id, $this->rel_fields_before_value['account_id']);
        }
        // Bug 38529 & 40938 - exclude currency_id
        parent::save_relationship_changes($is_update, array('currency_id'));

    }


    /**
     * Static helper function for getting releated account info.
     */
    function get_account_detail($opp_id)
    {
        $ret_array = array();
        $db = DBManagerFactory::getInstance();
        $query = "SELECT acc.id, acc.name, acc.assigned_user_id "
            . "FROM accounts acc, accounts_opportunities a_o "
            . "WHERE acc.id=a_o.account_id"
            . " AND a_o.opportunity_id='$opp_id'"
            . " AND a_o.deleted=0"
            . " AND acc.deleted=0";
        $result = $db->query($query, true, "Error filling in opportunity account details: ");
        $row = $db->fetchByAssoc($result);
        if ($row != null) {
            $ret_array['name'] = $row['name'];
            $ret_array['id'] = $row['id'];
            $ret_array['assigned_user_id'] = $row['assigned_user_id'];
        }
        return $ret_array;
    }
}

