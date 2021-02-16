<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


///////////////////////////////////////////////////////////////////////////////
////	TABLE DEFINITION FOR EMAIL STUFF
$dictionary['UserSignature'] = array(
	'table' => 'users_signatures',
	'fields' => array(
		'id' => array(
			'name'		=> 'id',
			'vname'		=> 'LBL_ID',
			'type'		=> 'id',
			'required'	=> true,
		),
		'date_entered' => array (
			'name' => 'date_entered',
			'vname' => 'LBL_DATE_ENTERED',
			'type' => 'datetime',
			'required'=>true,
		),
		'date_modified' => array (
			'name' => 'date_modified',
			'vname' => 'LBL_DATE_MODIFIED',
			'type' => 'datetime',
			'required'=>true,
		),
		'deleted' => array (
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'required' => false,
			'reportable'=>false,
		),
		'user_id' => array(
			'name' => 'user_id',
			'vname' => 'LBL_USER_ID',
			'type' => 'varchar',
			'len' => 36,
		),  
		'name' => array(
			'name' => 'name',
			'vname' => 'LBL_SUBJECT',
			'type' => 'varchar',
			'required' => false,
			'len' => '255',
		),
		'signature' => array(
			'name' => 'signature',
			'vname' => 'LBL_SIGNATURE',
			'type' => 'text',
			'reportable' => false,
		),
		'signature_html' => array(
			'name' => 'signature_html',
			'vname' => 'LBL_SIGNATURE_HTML',
			'type' => 'text',
			'reportable' => false,
		),
	),
	'indices' => array(
		array(
			'name' => 'users_signaturespk',
			'type' =>'primary',
			'fields' => array('id')
		),
		array(
			'name' => 'idx_usersig_uid',
			'type' => 'index',
			'fields' => array('user_id')
		)
	),
);
?>
