<?php

$dictionary['scrumuserstories_systemdeploymentcrs'] = array(
    'table' => 'scrumuserstories_systemdeploymentcrs',
    'fields' => array(
        array(
            'name' =>'id',
            'type' =>'varchar',
            'len'=>'36'),
        array(
            'name' =>'scrumuserstory_id',
            'type' =>'varchar',
            'len'=>'36'),
        array(
            'name' =>'systemdeploymentcr_id',
            'type' =>'varchar', 'len'=>'36'),
        array(
            'name' => 'date_modified',
            'type' => 'datetime'),
        array(
            'name' =>'deleted',
            'type' =>'bool',
            'len'=>'1',
            'required'=>false,
            'default'=>'0')
    ),

    'indices' => array(
        array(
            'name' =>'scrumuserstories_systemdeploymentcrspk',
            'type' =>'primary',
            'fields'=>array('id')),
        array(
            'name' => 'idx_scrumuserstories_systemdeploymentcrs',
            'type'=>'alternate_key',
            'fields'=>array('scrumuserstory_id','systemdeploymentcr_id')),
        array(
            'name' => 'idx_scrumuserstories_crid',
            'type' => 'index',
            'fields' => array('systemdeploymentcr_id')),
        array(
            'name' => 'idx_scrumuserstories_ustrid',
            'type' => 'index',
            'fields' => array('scrumuserstory_id')),
        array(
            'name' => 'idx_ustrid_del_crid',
            'type' => 'index',
            'fields' => array('scrumuserstory_id', 'deleted', 'systemdeploymentcr_id')),
    ),

    'relationships' => array(
    'scrumuserstories_systemdeploymentcrs' => array(
        'lhs_module'=> 'ScrumUserStories',
        'lhs_table'=> 'scrumuserstories',
        'lhs_key' => 'id',
        'rhs_module'=> 'SystemDeploymentCRs',
        'rhs_table'=> 'systemdeploymentcrs',
        'rhs_key' => 'id',
        'relationship_type'=>'many-to-many',
        'join_table'=> 'scrumuserstories_systemdeploymentcrs',
        'join_key_lhs'=>'scrumuserstory_id',
        'join_key_rhs'=>'systemdeploymentcr_id')
)
);
