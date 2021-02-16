<?php
namespace SpiceCRM\modules\Prospects;

use SpiceCRM\includes\SugarObjects\templates\person\Person;
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

class Prospect extends Person {

	var $module_dir = 'Prospects';
	var $table_name = "prospects";
	var $object_name = "Prospect";

    // This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('assigned_user_name');


	function __construct() {
		parent::__construct();
	}

	function fill_in_additional_list_fields()
	{
		parent::fill_in_additional_list_fields();
		$this->_create_proper_name_field();
		$this->email_and_name1 = $this->full_name." &lt;".$this->email1."&gt;";
	}

	function fill_in_additional_detail_fields()
	{
		parent::fill_in_additional_list_fields();
		$this->_create_proper_name_field();
   	}


}
