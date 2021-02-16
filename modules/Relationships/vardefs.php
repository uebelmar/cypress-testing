<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['Relationship'] =[
	'table' => 'relationships',
	'fields' => [
		'id' => [
			'name' => 'id',
			'vname' => 'LBL_ID',
			'type' => 'id',
			'required' => true,
		],
		'relationship_name' => [
			'name' => 'relationship_name',
			'vname' => 'LBL_RELATIONSHIP_NAME',
			'type' => 'varchar',
			'required' => true,
			'len' => 150,
			'importable' => 'required',
		],
  		'lhs_module' => [
			'name' => 'lhs_module',
			'vname' => 'LBL_LHS_MODULE',
			'type' => 'varchar',
			'required' => true,
			'len' => 100,
			'comment' => '@deprecated. Will be replaced by lhs_sysmodule_id'
		],
		'lhs_table' => [
			'name' => 'lhs_table',
			'vname' => 'LBL_LHS_TABLE',
			'type' => 'varchar',
			'required' => true,
			'len' => 64,
			'comment' => '@deprecated. Will be removed'
		],
		'lhs_key' => [
			'name' => 'lhs_key',
			'vname' => 'LBL_LHS_KEY',
			'type' => 'varchar',
			'required' => true,
			'len' => 64,
			'comment' => '@deprecated. Will be replaced by lhs_sysdictionaryitem_id'
		],
		'rhs_module' => [
			'name' => 'rhs_module',
			'vname' => 'LBL_RHS_MODULE',
			'type' => 'varchar',
			'required' => true,
			'len' => 100,
			'comment' => '@deprecated. Will be replaced by rhs_sysmodule_id'
		],
		'rhs_table' => [
			'name' => 'rhs_table',
			'vname' => 'LBL_RHS_TABLE',
			'type' => 'varchar',
			'required' => true,
			'len' => 64,
			'comment' => '@deprecated. Will be removed'
		],
		'rhs_key' => [
			'name' => 'rhs_key',
			'vname' => 'LBL_RHS_KEY',
			'type' => 'varchar',
			'required' => true,
			'len' => 64,
			'comment' => '@deprecated. Will be replaced by rhs_sysdictionaryitem_id'
		],
		'join_table' => [
			'name' => 'join_table',
			'vname' => 'LBL_JOIN_TABLE',
			'type' => 'varchar',
			  // Bug #41454 : Custom Relationships with Long Names do not Deploy Properly in MSSQL Environments
			  // Maximum length of identifiers for MSSQL, DB2 is 128 symbols
			  // @see e.g. MssqlManager :: $maxNameLengths property
			  // @see AbstractRelationship::getRelationshipMetaData(]
			  'len' => 128,
			'comment' => '@deprecated. Will be replaced by join_sysdictionary_id'
		],
		'join_key_lhs' => [
			'name' => 'join_key_lhs',
			'vname' => 'LBL_JOIN_KEY_LHS',
			'type' => 'varchar',
			'len' => 128,
			  // Bug #41454 : Custom Relationships with Long Names do not Deploy Properly in MSSQL Environments
			  // Maximum length of identifiers for MSSQL, DB2 is 128 symbols
			  // @see e.g. MssqlManager :: $maxNameLengths property
			  // @see AbstractRelationship::getRelationshipMetaData(]
            'comment' => ''
		],
		'join_key_rhs' => [
			'name' => 'join_key_rhs',
			'vname' => 'LBL_JOIN_KEY_RHS',
			'type' => 'varchar',
			  // Bug #41454 : Custom Relationships with Long Names do not Deploy Properly in MSSQL Environments
			  // Maximum length of identifiers for MSSQL, DB2 is 128 symbols
			  // @see e.g. MssqlManager :: $maxNameLengths property
			  // @see AbstractRelationship::getRelationshipMetaData(]
			  'len' => 128,
			'comment' => ''
		],

		'relationship_type' => [
			'name' => 'relationship_type',
			'vname' => 'LBL_RELATIONSHIP_TYPE',
			'type' => 'varchar',
			'len' => 64
		],
		'relationship_role_column' => [
			'name' => 'relationship_role_column',
			'vname' => 'LBL_RELATIONSHIP_ROLE_COLUMN',
			'type' => 'varchar',
			'len' => 64
		],
		'relationship_role_column_value' => [
			'name' => 'relationship_role_column_value',
			'vname' => 'LBL_RELATIONSHIP_ROLE_COLUMN_VALUE',
			'type' => 'varchar',
			'len' => 50
		],
		'reverse' => [ 
			'name' => 'reverse',
			'vname' => 'LBL_REVERSE',
			'type' => 'bool',
			'default' => '0'
		],
		'deleted' => [
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'reportable'=>false,
			'default' => '0'
		],
	], 
	'indices' => [
	    ['name' =>'relationshippk', 'type' =>'primary', 'fields'=>['id']],
	    ['name' =>'idx_rel_name', 'type' =>'index', 'fields'=>['relationship_name']],
	    ['name' =>'idx_relationship_lhs_module', 'type' =>'index', 'fields'=>['lhs_module']],
	    ['name' =>'idx_relationship_rhs_module', 'type' =>'index', 'fields'=>['rhs_module']], 
	]
];

