<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesPlanningContentData;

use SpiceCRM\data\SugarBean;

class SalesPlanningContentData extends SugarBean {

	public $module_dir = 'SalesPlanningContentData';
	public $object_name = 'SalesPlanningContentData';
	public $table_name = 'salesplanningcontentdata';
	public $importable = false;

	public $id;
	public $name;
	public $date_entered;
	public $date_modified;
	public $modified_user_id;
	public $modified_by_name;
	public $modified_user_link;
	public $created_by;
	public $created_by_name;
	public $created_by_link;
	public $description;
	public $deleted;


	// This is used to retrieve related fields from form posts.
	public $relationship_fields = array();


    public function __construct(){
        parent::__construct();

    }

	public function get_summary_text(){
    	return $this->name;
	}

    /*
     * enable acl check for this module
     * @param $interface: string
     * @return boolean
     */
	public function bean_implements($interface){
		switch($interface) {
			case 'ACL':return true;
		}
		return false;
	}

}
