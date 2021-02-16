<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['calls_leads'] = array ( 'table' => 'calls_leads'
                                  , 'fields' => array (
       array('name' =>'id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'call_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'lead_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'required', 'type' =>'varchar', 'len'=>'1', 'default'=>'1')
      , array('name' =>'accept_status', 'type' =>'varchar', 'len'=>'25', 'default'=>'none')
      , array ('name' => 'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'type' =>'bool', 'len'=>'1', 'default'=>'0', 'required'=>false)
                                                      )     
                                  , 'indices' => array (
       array('name' =>'calls_leadspk', 'type' =>'primary', 'fields'=>array('id'))
      , array('name' =>'idx_lead_call_call', 'type' =>'index', 'fields'=>array('call_id'))
      , array('name' =>'idx_lead_call_lead', 'type' =>'index', 'fields'=>array('lead_id'))
      , array('name' => 'idx_call_lead', 'type'=>'alternate_key', 'fields'=>array('call_id','lead_id'))            
                                                      )

 	  , 'relationships' => array ('calls_leads' => array('lhs_module'=> 'Calls', 'lhs_table'=> 'calls', 'lhs_key' => 'id',
							  'rhs_module'=> 'Leads', 'rhs_table'=> 'leads', 'rhs_key' => 'id',
							  'relationship_type'=>'many-to-many',
							  'join_table'=> 'calls_leads', 'join_key_lhs'=>'call_id', 'join_key_rhs'=>'lead_id'))

)
?>
