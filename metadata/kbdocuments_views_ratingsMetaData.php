<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


$dictionary['kbdocuments_viwes_ratings'] = array (
	'table' => 'kbdocuments_views_ratings',
	'fields' => array (
       array('name' =>'id', 'type' =>'varchar', 'len'=>'36')
      , array ('name' => 'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'type' =>'bool', 'len'=>'1', 'default'=>'0', 'required' => false,)
      , array('name' =>'kbdocument_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'views_number', 'type' =>'int', 'default'=>'0','required' => false,)
      , array('name' =>'ratings_number', 'type' =>'int', 'default'=>'0','required' => false,)           
	),
	'indices' => array (
       array('name' =>'kbdoc_views_ratingspk', 'type' =>'primary', 'fields'=>array('id'))
       , array('name' =>'idx_kbvr_kbdoc', 'type' =>'index', 'fields'=>array('kbdocument_id'))                  
	),		
);
?>
