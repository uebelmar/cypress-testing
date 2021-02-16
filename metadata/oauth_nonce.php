<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * table storing reports filter information */
$dictionary['oauth_nonce'] = array(
	'table' => 'oauth_nonce',
	'fields' => array(
		'conskey' => array(
			'name'		=> 'conskey',
			'type'		=> 'varchar',
			'len'		=> 32,
			'required'	=> true,
			'isnull'	=> false,
		),
		'nonce' => array(
			'name'		=> 'nonce',
			'type'		=> 'varchar',
			'len'		=> 32,
			'required'	=> true,
			'isnull'	=> false,
		),
		'nonce_ts' => array(
			'name'		=> 'nonce_ts',
			'type'		=> 'long',
			'required'	=> true,
		),
	),
	'indices' => array(
		array(
			'name'			=> 'oauth_nonce_pk',
			'type'			=> 'primary',
			'fields'		=> array('conskey', 'nonce')
		),
		array(
			'name'			=> 'oauth_nonce_keyts',
			'type'			=> 'index',
			'fields'		=> array('conskey', 'nonce_ts')
		),
	),
);
