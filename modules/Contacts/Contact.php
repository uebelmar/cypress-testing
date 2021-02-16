<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\Contacts;

use SpiceCRM\includes\SugarObjects\templates\person\Person;
use SpiceCRM\includes\authentication\AuthenticationController;
class Contact extends Person
{

    var $table_name = "contacts";
    var $object_name = "Contact";
    var $module_dir = 'Contacts';

    var $relationship_fields = Array('account_id'=> 'accounts', 'contacts_users_id' => 'user_sync');

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
        parent::save_relationship_changes($is_update);
    }
}
