<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SystemDeploymentCR'] = array(
    'table' => 'systemdeploymentcrs',
    'audited' => true,
    'fields' => array(
        'crid' => array(
            'name' => 'crid',
            'vname' => 'LBL_CRID',
            'type' => 'varchar',
            'readonly' => true,
            'len' => 11,
        ),
        'crstatus' => array(
            'name' => 'crstatus',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => '10',
            'options' => 'crstatus_dom'
        ),
        'crtype' => array(
            'name' => 'crtype',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => '10',
            'options' => 'crtype_dom'
        ),
        'package' => array(
            'name' => 'package',
            'vname' => 'LBL_PACKAGE',
            'type' => 'varchar',
            'length' => 50
        ),
        'tickets' => array(
            'name' => 'tickets',
            'vname' => 'LBL_TICKETS',
            'type' => 'text'
        ),
        'demandid' => array(
            'name' => 'demandid',
            'vname' => 'LBL_DEMANDID',
            'type' => 'varchar',
            'len' => '50'
        ),
        'resolution' => array(
            'name' => 'resolution',
            'vname' => 'LBL_RESOLUTION',
            'type' => 'text'
        ),
        'post_deploy_action' => array(
            'name' => 'post_deploy_action',
            'vname' => 'LBL_POST_DEPLOY_ACTION',
            'type' => 'text'
        ),
        'pre_deploy_action' => array(
            'name' => 'pre_deploy_action',
            'vname' => 'LBL_PRE_DEPLOY_ACTION',
            'type' => 'text'
        ),
        'repairs' => array(
            'name' => 'repairs',
            'vname' => 'LBL_REPAIRS',
            'type' => 'multienum',
            'options' => 'systemdeploymentpackage_repair_dom'
        ),
        'repair_modules' => array(
            'name' => 'repair_modules',
            'vname' => 'LBL_REPAIR_MODULES',
            'type' => 'multienum',
            'options' => 'systemdeploymentpackage_repair_modules_dom'
        ),
        'sdp_scr' => array(
            'name' => 'sdp_scr',
            'type' => 'link',
            'relationship' => 'sdp_scr',
            'module' => 'SystemDeploymentPackages',
            'source' => 'non-db',
            'vname' => 'LBL_SYSTEMDEPLOYMENTPACKAGES',
        ),
        'projectwbss' => array(
            'name' => 'projectwbss',
            'vname' => 'LBL_PROJECTWBS',
            'type' => 'link',
            'relationship' => 'projectwbs_systemdeploymentcrs',
            'source' => 'non-db'
        ),
        'projectwbs_id' => array(
            'name' => 'projectwbs_id',
            'vname' => 'LBL_PROJECTWBS_ID',
            'type' => 'id',
            'comment' => 'SugarId of related Project WBS'
        ),
        'projectwbs_name' => array(
            'name' => 'projectwbs_name',
            'rname' => 'name',
            'id_name' => 'projectwbs_id',
            'vname' => 'LBL_PROJECTWBS',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'ProjectWBSs',
            'table' => 'projectwbss',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'projectwbss',
            'unified_search' => true,
            'importable' => 'true',
        ),
        'users' => array(
            'name' => 'users',
            'type' => 'link',
            'relationship' => 'systemdeploymentcrs_users',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
            'module' => 'Users',
            'default' => false,
            'rel_fields' => [
                'user_role' => [ // column name in join table
                    'map' => 'cr_user_role' // non-db field on Users module side
                ]
            ]
        ),
        'systemdeploymentreleases' => array(
            'name' => 'systemdeploymentreleases',
            'vname' => 'MOD_SYSDEPLOYMENTRELEASE',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'systemdeploymentrelease_systemdeploymentcrs',
            'module' => 'SystemDeploymentReleases',
        ),
        'systemdeploymentrelease_id' => array(
            'name' => 'systemdeploymentrelease_id',
            'vname' => 'LBL_SYSTEMDEPLOYMENTRELEASE_ID',
            'type' => 'id',
            'comment' => 'SugarId of related Project WBS'
        ),
        'systemdeploymentrelease_name' => array(
            'name' => 'systemdeploymentrelease_name',
            'rname' => 'name',
            'id_name' => 'systemdeploymentrelease_id',
            'vname' => 'LBL_SYSTEMDEPLOYMENTRELEASE',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'SystemDeploymentReleases',
            'table' => 'systemdeploymentreleases',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'systemdeploymentreleases',
            'unified_search' => true,
            'importable' => 'true',
        ),
        'scrumuserstories' => array(
            'name' => 'scrumuserstories',
            'type' => 'link',
            'relationship' => 'scrumuserstories_systemdeploymentcrs',
            'vname' => 'LBL_SCRUM_USERSTORIES',
            'module' => 'ScrumUserStories',
            'source' => 'non-db',
            'default' => false
        ),

    ),

    'indices' => array(
        array('name' => 'idx_systemdeploymentcrs_id_del', 'type' => 'index', 'fields' => array('id', 'deleted')),
        array('name' => 'idx_crid', 'type' => 'index', 'fields' => array('crid')),
        array('name' => 'idx_systemdeploymentcrs_wbs', 'type' => 'index', 'fields' => array('projectwbs_id')),
    ),
    'relationships' => array(),
    'optimistic_lock' => true
);




VardefManager::createVardef('SystemDeploymentCRs', 'SystemDeploymentCR', array('default', 'assignable', 'activities'));

