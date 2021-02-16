<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesPlanningVersions;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\authentication\AuthenticationController;

class SalesPlanningVersion extends SugarBean {

	public $module_dir = 'SalesPlanningVersions';
	public $object_name = 'SalesPlanningVersion';
	public $table_name = 'salesplanningversions';
	public $importable = false;

    public function __construct(){
        parent::__construct();

    }


	function create_new_list_query($order_by, $where, $filter = [], $params = [], $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false, $ifListForExport = false){
		$current_user = AuthenticationController::getInstance()->getCurrentUser();

		if(!$current_user->is_admin)
		{
			if(empty($where))
			{
				$where = ' (salesplanningversions.adminonly is null OR salesplanningversions.adminonly = 0) ';
			}
			else
			{
				$where .= ' AND '.  ' (salesplanningversions.adminonly is null OR salesplanningversions.adminonly = 0) ';
			}
		}
		return parent::create_new_list_query($order_by, $where,$filter,$params, $show_deleted,$join_type, $return_array,$parentbean, $singleSelect, $ifListForExport);
	}


}
