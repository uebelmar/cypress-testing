<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SystemDeploymentSystem'] = array(
    'table' => 'systemdeploymentsystems',
    'audited' => false,
    'fields' => array(
        'url' => array(
            'name' => 'url',
            'vname' => 'LBL_URL',
            'type' => 'varchar',
            'len' => '200'
        ),
        'add_systems' => array(
            'name' => 'add_systems',
            'vname' => 'LBL_ADD_SYSTEMS',
            'type' => 'text',
        ),
        'sys_username' => array(
            'name' => 'sys_username',
            'vname' => 'LBL_SYS_USERNAME',
            'type' => 'varchar',
            'len' => '50'
        ),        
        'sys_password' => array(
            'name' => 'sys_password',
            'vname' => 'LBL_SYS_PASSWORD',
            'type' => 'varchar',
            'len' => '100'
        ),        
        'master_flag' => array(
            'name' => 'master_flag',
            'vname' => 'LBL_MASTER_FLAG',
            'type' => 'bool',
            'default' => false
        ),
        'this_system' => array(
            'name' => 'this_system',
            'vname' => 'LBL_THIS_SYSTEM',
            'type' => 'bool',
            'default' => false
        ),
        'type' => array(
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'systemdeploymentsystems_type_dom',
            'len' => '10'
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'varchar',
            'len' => '1'
        ),
        'systemdeploymentsystems' => array(
            'name' => 'systemdeploymentsystems',
            'type' => 'link',
            'relationship' => 'systemdeploymentsystems_systemdeploymentsystems_rel',
            'module' => 'SystemDeploymentSystems',
            'side' => 'left',
            'source' => 'non-db',
            'vname' => 'LBL_SYSTEMDEPLOYMENTSYSTEMS',
        ),
        'git_user' => array(
            'name' => 'git_user',
            'vname' => 'LBL_GIT_USER',
            'type' => 'varchar',
            'len' => '100'
        ),
        'git_pass' => array(
            'name' => 'git_pass',
            'vname' => 'LBL_GIT_PASS',
            'type' => 'varchar',
            'len' => '100'
        ),
        'git_repo' => array(
            'name' => 'git_repo',
            'vname' => 'LBL_GIT_REPO',
            'type' => 'varchar'
        ),
    ),
    'indices' => array(
        array('name' => 'idx_systemdeploymentsystems_id_del', 'type' => 'index', 'fields' => array('id', 'deleted')),
    ),
    'optimistic_lock' => true
);



    VardefManager::createVardef('SystemDeploymentSystems', 'SystemDeploymentSystem', array('default', 'assignable'));
