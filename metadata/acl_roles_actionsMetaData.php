<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['acl_roles_actions'] = array (

	'table' => 'acl_roles_actions',

	'fields' => array (
		array (
			'name' => 'id',
			'type' => 'varchar',
			'len' => '36',
		),
		array (
			'name' => 'role_id',
			'type' => 'varchar',
			'len' => '36',
		),
		array (
			'name' => 'action_id',
			'type' => 'varchar',
			'len' => '36',
		),
		array (
			'name' => 'access_override',
			'type' => 'int',
			'len' => '3',
			'required' => false,
		)
      , array ('name' => 'date_modified','type' => 'datetime'),
		array (
			'name' => 'deleted',
			'type' => 'bool',
			'len' => '1',
			'default' => '0'
		),
	),

	'indices' => array (
		array (
			'name' => 'acl_roles_actionspk',
			'type' => 'primary',
			'fields' => array ( 'id' )
		),
		array (
			'name' => 'idx_acl_role_id',
			'type' => 'index',
			'fields' => array ('role_id')
		),
		array (
			'name' => 'idx_acl_action_id',
			'type' => 'index',
			'fields' => array ('action_id')
		),
		 array('name' => 'idx_aclrole_action', 'type'=>'alternate_key', 'fields'=>array('role_id','action_id'))
	),
	'relationships' => array ('acl_roles_actions' => array('lhs_module'=> 'ACLRoles', 'lhs_table'=> 'acl_roles', 'lhs_key' => 'id',
							  'rhs_module'=> 'ACLActions', 'rhs_table'=> 'acl_actions', 'rhs_key' => 'id',
							  'relationship_type'=>'many-to-many',
							  'join_table'=> 'acl_roles_actions', 'join_key_lhs'=>'role_id', 'join_key_rhs'=>'action_id')),

)

?>
