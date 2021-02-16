<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['users_last_import'] = array ( 'table' => 'users_last_import'
                                  , 'fields' => array (
       array('name' =>'id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'assigned_user_id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'bean_type', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'bean_id', 'type' =>'varchar', 'len'=>'36',)
      , array ('name' => 'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'required'=>false, 'type' =>'bool', 'len'=>'1')
                                                      )                                  , 'indices' => array(
        array('name' => 'users_last_importpk', 'type' => 'primary', 'fields' => array('id'))
    , array('name' => 'idx_user_imp_id', 'type' => 'index', 'fields' => array('assigned_user_id'))
    )
                                  )
?>
