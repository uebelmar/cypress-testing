<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/* this table should never get created, it should only be used as a template for the acutal audit tables
 * for each moudule.
 */
$dictionary['audit'] =
    array ( 'table' => 'audit_template',
        'fields' => array (
            'id'=> array('name' =>'id', 'type' =>'id', 'len'=>'36','required'=>true),
            'parent_id'=>array('name' =>'parent_id', 'type' =>'id', 'len'=>'36','required'=>true),
            'transaction_id'=>array('name' =>'transaction_id', 'type' =>'varchar', 'len'=>'36','required'=>false),
            'date_created'=>array('name' =>'date_created','type' => 'datetime'),
            'created_by'=>array('name' =>'created_by','type' => 'varchar','len' => 36),
            'field_name'=>array('name' =>'field_name','type' => 'varchar','len' => 100),
            'data_type'=>array('name' =>'data_type','type' => 'varchar','len' => 100),
            'before_value_string'=>array('name' =>'before_value_string','type' => 'varchar'),
            'after_value_string'=>array('name' =>'after_value_string','type' => 'varchar'),
            'before_value_text'=>array('name' =>'before_value_text','type' => 'text'),
            'after_value_text'=>array('name' =>'after_value_text','type' => 'text'),
        ),
        'indices' => array (
            //name will be re-constructed adding idx_ and table name as the prefix like 'idx_accounts_'
            array ('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
            array ('name' => 'parent_id', 'type' => 'index', 'fields' => array('parent_id')),
            array ('name' => 'field_name', 'type' => 'index', 'fields' => array('field_name')),
        )
    );
