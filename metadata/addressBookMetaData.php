<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 *********************************************************************************/

$dictionary['AddressBook'] = array ('table' => 'address_book',
	'fields' => array (
		'assigned_user_id' => array (
			'name' => 'assigned_user_id',
			'vname' => 'LBL_USER_ID',
			'type' => 'id',
			'required' => true,
			'reportable' => false,
		),
		'bean' => array (
			'name' => 'bean',
			'vname' => 'LBL_BEAN',
			'type' => 'varchar',
			'len' => '50',
			'required' => true,
			'reportable' => false,
		),
		'bean_id' => array (
			'name' => 'bean_id',
			'vname' => 'LBL_BEAN_ID',
			'type' => 'id',
			'required' => true,
			'reportable' => false,
		),
	),
	'indices' => array (
		array(
			'name' => 'ab_user_bean_idx',
			'type' =>'index',
			'fields' => array(
				'assigned_user_id',
				'bean',
			)
		),
	), /* end indices */
);

