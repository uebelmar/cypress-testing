<?php
namespace SpiceCRM\modules\Leads;

use SpiceCRM\includes\SugarObjects\templates\person\Person;
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

// Lead is used to store profile information for people who may become customers.
class Lead extends Person {

	var $table_name = "leads";
	var $object_name = "Lead";
	var $object_names = "Leads";
	var $module_dir = "Leads";

	function __construct() {
		parent::__construct();
	}

	function fill_in_additional_list_fields()
	{
		parent::fill_in_additional_list_fields();
		$this->_create_proper_name_field();

	}

	function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}

	function save($check_notify = false, $fts_index_bean = true) {
		if(empty($this->status))
			$this->status = 'New';
		// call save first so that $this->id will be set
		$value = parent::save($check_notify, $fts_index_bean);
		return $value;
	}

}

