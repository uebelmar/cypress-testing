<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['serviceorders_accounts'] = array(
    'table' => 'serviceorders_accounts',
    'fields' => array(
        array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'serviceorder_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'account_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'account_role',
            'type' => 'varchar',
            'len' => '50'
        ),
        array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false
        )
    ),
    'indices' => array(
        array(
            'name' => 'serviceorders_accountspk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_srvo_acc_srvo',
            'type' => 'index',
            'fields' => array('serviceorder_id')
        ),
        array(
            'name' => 'idx_srvo_acc_acc',
            'type' => 'index',
            'fields' => array('account_id')
        )
    ),
    'relationships' => array(
        'serviceorders_accounts_add' => array(
            'lhs_module' => 'ServiceOrders',
            'lhs_table' => 'serviceorders',
            'lhs_key' => 'id',
            'rhs_module' => 'Accounts',
            'rhs_table' => 'accounts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'serviceorders_accounts',
            'join_key_lhs' => 'serviceorder_id',
            'join_key_rhs' => 'account_id'
        )
    )
);
