<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['linked_documents'] = array ( 'table' => 'linked_documents'
   , 'fields' => array (
        array('name' =>'id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'parent_id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'parent_type', 'type' =>'varchar', 'len'=>'25')      
      , array('name' =>'document_id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'document_revision_id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'type' =>'bool', 'len'=>'1', 'default'=>'0', 'required'=>false)
   )   
   , 'indices' => array (
        array('name' =>'linked_documentspk', 'type' =>'primary', 'fields'=>array('id')),
        array(	'name'			=> 'idx_parent_document', 
				'type'			=> 'alternate_key', 
				'fields'		=> array('parent_type','parent_id','document_id'),
		),
   )
   , 'relationships' => array (
			'contracts_documents' => array('lhs_module'=> 'Contracts', 'lhs_table'=> 'contracts', 'lhs_key' => 'id',
				   'rhs_module'=> 'Documents', 'rhs_table'=> 'documents', 'rhs_key' => 'id',
				   'relationship_type'=>'many-to-many',
				   'join_table'=> 'linked_documents', 'join_key_lhs'=>'parent_id', 'join_key_rhs'=>'document_id', 'relationship_role_column'=>'parent_type',
				   'relationship_role_column_value'=>'Contracts'),
			'leads_documents' => array('lhs_module'=> 'Leads', 'lhs_table'=> 'leads', 'lhs_key' => 'id',
				   'rhs_module'=> 'Documents', 'rhs_table'=> 'documents', 'rhs_key' => 'id',
				   'relationship_type'=>'many-to-many',
				   'join_table'=> 'linked_documents', 'join_key_lhs'=>'parent_id', 'join_key_rhs'=>'document_id', 'relationship_role_column'=>'parent_type',
				   'relationship_role_column_value'=>'Leads'),
			'contracttype_documents' => array('lhs_module'=> 'ContractTypes', 'lhs_table'=> 'contract_types', 'lhs_key' => 'id',
				   'rhs_module'=> 'Documents', 'rhs_table'=> 'documents', 'rhs_key' => 'id',
				   'relationship_type'=>'many-to-many',
				   'join_table'=> 'linked_documents', 'join_key_lhs'=>'parent_id', 'join_key_rhs'=>'document_id', 'relationship_role_column'=>'parent_type',
				   'relationship_role_column_value'=>'ContracTemplates'),
			),
   );
?>
