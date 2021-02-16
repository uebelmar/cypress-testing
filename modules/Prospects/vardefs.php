<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Prospect'] = array(

	'table' => 'prospects',
	'unified_search' => true,
	'fields' => array (
//removed since release 2019.07.001
//	 'tracker_key' => array (
//		'name' => 'tracker_key',
//		'vname' => 'LBL_TRACKER_KEY',
//		'type' => 'int',
//		'len' => '11',
//		'auto_increment' => true,
//		'importable' => 'false',
//		'studio' => array('editview' => false),
//		),
	  'birthdate' =>
	  array (
	    'name' => 'birthdate',
	    'vname' => 'LBL_BIRTHDATE',
	    'massupdate' => false,
	    'type' => 'date',
	  ),
	  'do_not_call' =>
	  array (
	    'name' => 'do_not_call',
	    'vname' => 'LBL_DO_NOT_CALL',
	    'type'=>'bool',
	    'default' =>'0',
	  ),
	  'lead_id' =>
	  array (
		'name' => 'lead_id',
		'type' => 'id',
		'reportable'=>false,
		'vname'=>'LBL_LEAD_ID',
	  ),
	  'account_name' =>
	  array (
    	'name' => 'account_name',
    	'vname' => 'LBL_ACCOUNT_NAME',
    	'type' => 'varchar',
    	'len' => '150',
  	),
     'campaign_id' =>
      array (
            'name' => 'campaign_id',
        'comment' => 'Campaign that generated lead',
        'vname'=>'LBL_CAMPAIGN_ID',
        'rname' => 'id',
        'id_name' => 'campaign_id',
        'type' => 'id',
        'table' => 'campaigns',
        'isnull' => 'true',
        'module' => 'Campaigns',
        //'dbType' => 'char',
        'reportable'=>false,
        'massupdate' => false,
            'duplicate_merge'=> 'disabled',
      ),
	'email_addresses' =>
	array (
		'name' => 'email_addresses',
        'type' => 'link',
		'relationship' => 'prospects_email_addresses',
        'source' => 'non-db',
		'vname' => 'LBL_EMAIL_ADDRESSES',
		'reportable'=>false,
        'rel_fields' => array('primary_address' => array('type'=>'bool')),
	),
	'email_addresses_primary' =>
	array (
		'name' => 'email_addresses_primary',
        'type' => 'link',
		'relationship' => 'prospects_email_addresses_primary',
        'source' => 'non-db',
		'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
		'duplicate_merge'=> 'disabled',
	),
	  'campaigns' =>
	  array (
  		'name' => 'campaigns',
    	'type' => 'link',
    	'relationship' => 'prospect_campaign_log',
    	'module'=>'CampaignLog',
    	'bean_name'=>'CampaignLog',
    	'source'=>'non-db',
		'vname'=>'LBL_CAMPAIGNLOG',
	  ),
      'prospect_lists' =>
      array (
        'name' => 'prospect_lists',
        'type' => 'link',
        'relationship' => 'prospect_list_prospects',
        'module'=>'ProspectLists',
        'source'=>'non-db',
        'vname'=>'LBL_PROSPECT_LIST',
      ),
      'calls' =>
		array (
			'name' => 'calls',
			'type' => 'link',
			'relationship' => 'prospect_calls',
			'source' => 'non-db',
			'vname' => 'LBL_CALLS',
		),
      'meetings'=>
		array (
			'name' => 'meetings',
			'type' => 'link',
			'relationship' => 'prospect_meetings',
			'source' => 'non-db',
			'vname' => 'LBL_MEETINGS',
		),
      'notes'=>
		array (
			'name' => 'notes',
			'type' => 'link',
			'relationship' => 'prospect_notes',
			'source' => 'non-db',
			'vname' => 'LBL_NOTES',
		),
      'tasks'=>
		array (
			'name' => 'tasks',
			'type' => 'link',
			'relationship' => 'prospect_tasks',
			'source' => 'non-db',
			'vname' => 'LBL_TASKS',
		),
      'emails'=>
		array (
			'name' => 'emails',
			'type' => 'link',
			'relationship' => 'emails_prospects_rel',
			'source' => 'non-db',
			'vname' => 'LBL_EMAILS',
		),
        // Used for non-primary mail import
        'email_addresses_non_primary' =>
            array(
                'name' => 'email_addresses_non_primary',
                'type' => 'email',
                'source' => 'non-db',
                'vname' => 'LBL_EMAIL_NON_PRIMARY',
                'studio' => false,
                'reportable' => false,
                'massupdate' => false,
            ),
	),

	'indices' =>
			array (
//				array(
//						'name' => 'prospect_auto_tracker_key' ,
//						'type'=>'index' ,
//						'fields'=>array('tracker_key')
//				),
       			array(	'name' 	=>	'idx_prospects_last_first',
						'type' 	=>	'index',
						'fields'=>	array(
										'last_name',
										'first_name',
										'deleted'
									)
				),
       			array(
						'name' =>	'idx_prospecs_del_last',
						'type' =>	'index',
						'fields'=>	array(
										'last_name',
										'deleted'
										)
				),
               array('name' =>'idx_prospects_id_del', 'type'=>'index', 'fields'=>array('id','deleted')),
               array('name' =>'idx_prospects_assigned', 'type'=>'index', 'fields'=>array('assigned_user_id')),

    		),

	'relationships' => array (
    'prospect_tasks' => array('lhs_module'=> 'Prospects', 'lhs_table'=> 'prospects', 'lhs_key' => 'id',
                              'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'parent_id',
                              'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
                              'relationship_role_column_value'=>'Prospects'),
    'prospect_notes' => array('lhs_module'=> 'Prospects', 'lhs_table'=> 'prospects', 'lhs_key' => 'id',
                              'rhs_module'=> 'Notes', 'rhs_table'=> 'notes', 'rhs_key' => 'parent_id',
                              'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
                              'relationship_role_column_value'=>'Prospects'),
    'prospect_meetings' => array('lhs_module'=> 'Prospects', 'lhs_table'=> 'prospects', 'lhs_key' => 'id',
                              'rhs_module'=> 'Meetings', 'rhs_table'=> 'meetings', 'rhs_key' => 'parent_id',
                              'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
                              'relationship_role_column_value'=>'Prospects'),
    'prospect_calls' => array('lhs_module'=> 'Prospects', 'lhs_table'=> 'prospects', 'lhs_key' => 'id',
                              'rhs_module'=> 'Calls', 'rhs_table'=> 'calls', 'rhs_key' => 'parent_id',
                              'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
                              'relationship_role_column_value'=>'Prospects'),
    'prospect_emails' => array('lhs_module'=> 'Prospects', 'lhs_table'=> 'prospects', 'lhs_key' => 'id',
                              'rhs_module'=> 'Emails', 'rhs_table'=> 'emails', 'rhs_key' => 'parent_id',
                              'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
                              'relationship_role_column_value'=>'Prospects'),
    'prospect_campaign_log' => array(
									'lhs_module'		=>	'Prospects',
									'lhs_table'			=>	'prospects',
									'lhs_key' 			=> 	'id',
						  			'rhs_module'		=>	'CampaignLog',
									'rhs_table'			=>	'campaign_log',
									'rhs_key' 			=> 	'target_id',
						  			'relationship_type'	=>'one-to-many',
						   			'relationship_role_column' => 'target_type',
						   			'relationship_role_column_value' => 'Prospects'
						  		),

	)
);
VardefManager::createVardef('Prospects','Prospect', array('default', 'assignable',
'person'));

