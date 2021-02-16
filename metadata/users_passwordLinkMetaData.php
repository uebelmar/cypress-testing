<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['users_password_link'] = array(
    'table' => 'users_password_link',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
        ) ,
        'username' => array(
            'name' => 'username',
            'vname' => 'LBL_USERNAME',
            'type' => 'varchar',
            'len' => 36,
        ) ,
        'date_generated' => array(
            'name' => 'date_generated',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
        ) ,
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
        ) ,
    ) ,
    'indices' => array(
        array(
            'name' => 'users_password_link_pk',
            'type' => 'primary',
            'fields' => array(
                'id'
            )
        ) ,
        array(
            'name' => 'idx_username',
            'type' => 'index',
            'fields' => array(
                'username'
            )
        )
    ) ,
);

$dictionary['users_password_tokens'] = array(
    'table' => 'users_password_tokens',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
        ) ,
        'user_id' => array(
            'name' => 'user_id',
            'vname' => 'LBL_USERNAME',
            'type' => 'varchar',
            'len' => 36,
        ) ,
        'date_generated' => array(
            'name' => 'date_generated',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
        ) ,
    ) ,
    'indices' => array(
        array(
            'name' => 'users_password_tokens_pk',
            'type' => 'primary',
            'fields' => array(
                'id'
            )
        ) ,
        array(
            'name' => 'idx_user_id',
            'type' => 'index',
            'fields' => array(
                'user_id'
            )
        )
    ) ,
);
?>
