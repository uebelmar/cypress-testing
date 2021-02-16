<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


use SpiceCRM\includes\SugarObjects\VardefManager;

$GLOBALS['dictionary']['UserQuota'] = array(
    'table' => 'userquotas',
    'fields' => array(
        'period' => array(
            'name' => 'period',
            'type' => 'int'
        ),
        'year' => array(
            'name' => 'year',
            'type' => 'int'
        ),
        'sales_quota' => array(
            'name' => 'sales_quota',
            'type' => 'currency'
        ),
        'period_date' => array(
            'name' => 'period_date',
            'type' => 'date'
        ),
        'user' => array(
            'name' => 'user',
            'type' => 'link',
            'relationship' => 'users_userquotas',
            'source' => 'non-db',
            'vname' => 'LBL_USER',
        )
    ),
    'relationships' => array(
        'users_userquotas' =>
            array(
                'lhs_module' => 'Users',
                'lhs_table' => 'users',
                'lhs_key' => 'id',
                'rhs_module' => 'UserQuotas',
                'rhs_table' => 'userquotas',
                'rhs_key' => 'assigned_user_id',
                'relationship_type' => 'one-to-many'
            )
    ),
    'indices' => array(
        array(
            'name' => 'idx_userquotasuserid',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        )
    )
);

VardefManager::createVardef('UserQuotas', 'UserQuota', array('default', 'assignable'));
