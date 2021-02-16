<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['users_feeds'] = array ( 'table' => 'users_feeds'
                                  , 'fields' => array (
    
       array('name' =>'user_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'feed_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'rank', 'type' =>'int', 'required' => false)
      , array ('name' => 'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'type' =>'bool', 'len'=>'', 'default'=>'0', 'required' => false)
                                                      ) 
                                 , 'indices' => array (
  
       array('name' =>'idx_ud_user_id', 'type' =>'index', 'fields'=>array('user_id', 'feed_id'))                                  
                                                      )
                                  )
?>
