<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['calls_contacts'] = array ( 'table' => 'calls_contacts'
                                  , 'fields' => array (
       array('name' =>'id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'call_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'contact_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'required', 'type' =>'varchar', 'len'=>'1', 'default'=>'1')
      , array('name' =>'accept_status', 'type' =>'varchar', 'len'=>'25', 'default'=>'none')
      , array ('name' => 'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'type' =>'bool', 'len'=>'1', 'default'=>'0', 'required'=>false)
                                                      )     
                                  , 'indices' => array (
       array('name' =>'calls_contactspk', 'type' =>'primary', 'fields'=>array('id'))
      , array('name' =>'idx_con_call_call', 'type' =>'index', 'fields'=>array('call_id'))
      , array('name' =>'idx_con_call_con', 'type' =>'index', 'fields'=>array('contact_id'))
      , array('name' => 'idx_call_contact', 'type'=>'alternate_key', 'fields'=>array('call_id','contact_id'))            
                                                      )

 	  , 'relationships' => array ('calls_contacts' => array('lhs_module'=> 'Calls', 'lhs_table'=> 'calls', 'lhs_key' => 'id',
							  'rhs_module'=> 'Contacts', 'rhs_table'=> 'contacts', 'rhs_key' => 'id',
							  'relationship_type'=>'many-to-many',
							  'join_table'=> 'calls_contacts', 'join_key_lhs'=>'call_id', 'join_key_rhs'=>'contact_id'))

)
?>
