<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['roles_users'] = array ( 

	'table' => 'roles_users',

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
			'name' => 'user_id',
			'type' => 'varchar',
			'len' => '36',
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
			'name' => 'roles_userspk',
			'type' => 'primary',
			'fields' => array ( 'id' )
		),
		array (
			'name' => 'idx_ru_role_id',
			'type' => 'index',
			'fields' => array ('role_id')
		),
		array (
			'name' => 'idx_ru_user_id',
			'type' => 'index',
			'fields' => array ('user_id')
		),
	),
	'relationships' => array ('roles_users' => array('lhs_module'=> 'Roles', 'lhs_table'=> 'roles', 'lhs_key' => 'id',
							  'rhs_module'=> 'Users', 'rhs_table'=> 'users', 'rhs_key' => 'id',
							  'relationship_type'=>'many-to-many',
							  'join_table'=> 'roles_users', 'join_key_lhs'=>'role_id', 'join_key_rhs'=>'user_id')),
	
)
                                  
?>
