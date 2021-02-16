<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['prospect_list_campaigns'] = array ( 

	'table' => 'prospect_list_campaigns',

	'fields' => array (
		array (
			'name' => 'id',
			'type' => 'varchar',
			'len' => '36',
		),
		array (
			'name' => 'prospect_list_id',
			'type' => 'varchar',
			'len' => '36',
		),
		array (
			'name' => 'campaign_id',
			'type' => 'varchar',
			'len' => '36',
		),
       array ('name' => 'date_modified','type' => 'datetime'),
		array (
			'name' => 'deleted',
			'type' => 'bool',
			'len' => '1',
			'default' => '0'
		),
		
	),
	
	'indices' => array (
		array (
			'name' => 'prospect_list_campaignspk',
			'type' => 'primary',
			'fields' => array ( 'id' )
		),
		array (
			'name' => 'idx_pro_id',
			'type' => 'index',
			'fields' => array ('prospect_list_id')
		),
		array (
			'name' => 'idx_cam_id',
			'type' => 'index',
			'fields' => array ('campaign_id')
		),
		array (
			'name' => 'idx_prospect_list_campaigns', 
			'type'=>'alternate_key', 
			'fields'=>array('prospect_list_id','campaign_id')
		),		
	),

 	'relationships' => array (
		'prospect_list_campaigns' => array('lhs_module'=> 'ProspectLists', 'lhs_table'=> 'prospect_lists', 'lhs_key' => 'id',
		'rhs_module'=> 'Campaigns', 'rhs_table'=> 'campaigns', 'rhs_key' => 'id',
		'relationship_type'=>'many-to-many',
		'join_table'=> 'prospect_list_campaigns', 'join_key_lhs'=>'prospect_list_id', 'join_key_rhs'=>'campaign_id')
	)
)
                                  
?>
