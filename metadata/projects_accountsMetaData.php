<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/



$dictionary['projects_accounts'] = array (
    'table' => 'projects_accounts',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'account_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'project_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false),
    ),
    'indices' => array (
        array('name' => 'projects_accounts_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_proj_acct_proj', 'type' =>'index', 'fields'=>array('project_id')),
        array('name' => 'idx_proj_acct_acct', 'type' =>'index', 'fields'=>array('account_id')),
        array('name' => 'projects_accounts_alt', 'type'=>'alternate_key', 'fields'=>array('project_id','account_id')),
    ),
    'relationships' => array (
        'projects_accounts' => array(
            'lhs_module' => 'Projects',
            'lhs_table' => 'projects',
            'lhs_key' => 'id',
            'rhs_module' => 'Accounts',
            'rhs_table' => 'accounts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_accounts',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'account_id',
        ),
    ),
);
?>
