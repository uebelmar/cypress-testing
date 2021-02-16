<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$GLOBALS['dictionary']['UserPreference'] = [
    'table' => 'user_preferences',
    'fields' => [
        'id' =>
            [
                'name' => 'id',
                'vname' => 'LBL_NAME',
                'type' => 'id',
                'required' => true,
                'reportable' => false,
            ],
        'category' =>
            [
                'name' => 'category',
                'type' => 'varchar',
                'len' => 50,
            ],
        'deleted' =>
            [
                'name' => 'deleted',
                'type' => 'bool',
                'default' => '0',
                'required' => false,
            ],
        'date_entered' =>
            [
                'name' => 'date_entered',
                'type' => 'datetime',
                'required' => true,
            ],
        'date_modified' =>
            [
                'name' => 'date_modified',
                'type' => 'datetime',
                'required' => true,
            ],
        'assigned_user_id' =>
            [
                'name' => 'assigned_user_id',
                'rname' => 'user_name',
                'id_name' => 'assigned_user_id',
                'type' => 'assigned_user_name',
                'table' => 'users',
                'required' => true,
                'dbType' => 'id',
            ],
        'assigned_user_name' =>
            [
                'name' => 'assigned_user_name',
                'vname' => 'LBL_ASSIGNED_TO',
                'type' => 'varchar',
                'reportable' => false,
                'massupdate' => false,
                'source' => 'non-db',
                'table' => 'users',
            ],
        'contents' =>
            [
                'name' => 'contents',
                'type' => 'longtext',
                'vname' => 'LBL_DESCRIPTION',
                'isnull' => true,
            ],
    ],
    'indices' => [
        ['name' =>'userpreferencespk', 'type' =>'primary', 'fields'=>['id']],
        ['name' =>'idx_userprefcat', 'type'=>'index', 'fields'=>['category']],
        ['name' =>'idx_userprefnamecat', 'type'=>'index', 'fields'=>['assigned_user_id','category']],
        ['name' =>'idx_userprefnamecatdel', 'type'=>'index', 'fields'=>['assigned_user_id','category', 'deleted']],
    ]
];
 



//// cn: bug 12036 - $dictionary['x'] for SugarBean::createRelationshipMeta() from upgrades
//$dictionary['UserPreference'] = $GLOBALS['dictionary']['UserPreference'];
