<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['meetings_contacts'] = array(
	'table'=> 'meetings_contacts',
	'fields'=> array(
		array(	'name'			=> 'id', 
				'type'			=> 'varchar', 
				'len'			=> '36'
		),
		array(	'name'			=> 'meeting_id', 
				'type'			=> 'varchar', 
				'len'			=> '36',
		),
		array(	'name'			=> 'contact_id', 
				'type'			=> 'varchar', 
				'len'			=> '36',
		),
		array(	'name'			=> 'required', 
				'type'			=> 'varchar', 
				'len'			=> '1', 
				'default'		=> '1',
		),
		array(	'name'			=> 'accept_status', 
				'type'			=> 'varchar', 
				'len'			=> '25', 
				'default'		=> 'none'
		),
		array(	'name'			=> 'date_modified',
				'type'			=> 'datetime'
		),
		array(	'name'			=> 'deleted', 
				'type'			=> 'bool', 
				'len'			=> '1', 
				'default'		=> '0', 
				'required'		=> false
		),
 	), 
	'indices' => array(
 		array(	'name'			=> 'meetings_contactspk', 
				'type'			=> 'primary', 
				'fields'		=> array('id'),
		),
		array(	'name'			=> 'idx_con_mtg_mtg', 
				'type'			=> 'index', 
				'fields'		=> array('meeting_id'),
		),
		array(	'name'			=> 'idx_con_mtg_con', 
				'type'			=> 'index', 
				'fields'		=> array('contact_id'),
		),
		array(	'name'			=> 'idx_meeting_contact', 
				'type'			=> 'alternate_key', 
				'fields'		=> array('meeting_id','contact_id'),
		),
	),
	'relationships' => array(
		'meetings_contacts' => array(
			'lhs_module'		=> 'Meetings', 
			'lhs_table'			=> 'meetings', 
			'lhs_key'			=> 'id',
			'rhs_module'		=> 'Contacts', 
			'rhs_table'			=> 'contacts', 
			'rhs_key'			=> 'id',
			'relationship_type'	=> 'many-to-many',
			'join_table'		=> 'meetings_contacts', 
			'join_key_lhs'		=> 'meeting_id', 
			'join_key_rhs'		=> 'contact_id',
		),
	),
);
?>
