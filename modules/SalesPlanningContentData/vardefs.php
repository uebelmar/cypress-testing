<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningContentData'] = array(
	'table' => 'salesplanningcontentdata',
	'audited' => true,
	'fields' => array(

        'name' => array(
            'name' => 'name',
    	    'vname' => 'LBL_NAME',
	        'type' => 'varchar',
	        'len' => 10,
	        'required' => true,
    	    'reportable' => true,
			'massupdate' => false,
            'comment' => 'the nodes full path'
        ),

        'value' => array(
            'name' => 'value',
    	    'vname' => 'LBL_VALUE',
	        'dbType' => 'decimal',
            'disable_num_format' => true,
            'len' => '26,6',
	        'required' => true,
    	    'reportable' => true,
			'massupdate' => false,
        ),


        #create relationship to SalesPlanningNode
        'salesplanningnode_id' => array(
           'name' => 'salesplanningnode_id',
           'vname' => 'LBL_SALESPLANNINGPLANNINGNODE_ID',
           'type' => 'varchar',
           'len' => 36
        ),

        'salesplanningnode_name' => array(
           'name' => 'salesplanningnode_name',
           'vname' => 'LBL_SALES_PLANNING_NODE',
           'source' => 'non-db',
           'type' => 'relate',
           'len' => '255',
           'id_name' => 'salesplanningnode_id',
           'module' => 'SalesPlanningNodes',
           'link' => 'salesplanningnodes_link',
           'join_name' => 'salesplanningnodes',
           'rname' => 'name'
        ),

        'salesplanningnodes_link' => array(
           'name' => 'salesplanningnodes_link',
           'vname' => 'LBL_SALESPLANNINGPLANNINGNODES',
           'type' => 'link',
           'relationship' => 'salesplanningnodes_salesplanningcontentdata',
           'link_type' => 'one',
           'side' => 'right',
           'source' => 'non-db'
        ),



        #create relationship to SalesPlanningVersion
        'salesplanningversion_id' => array(
           'name' => 'salesplanningversion_id',
           'vname' => 'LBL_SALESPLANNINGPLANNINGVERSION_ID',
           'type' => 'varchar',
           'len' => 36
        ),

        'salesplanningversion_name' => array(
           'name' => 'salesplanningversion_name',
           'vname' => 'LBL_SALES_PLANNING_VERSION',
           'source' => 'non-db',
           'type' => 'relate',
           'len' => '255',
           'id_name' => 'salesplanningversion_id',
           'module' => 'SalesPlanningVersions',
           'link' => 'salesplanningversions_link',
           'join_name' => 'salesplanningversions',
           'rname' => 'name'
        ),

        'salesplanningversions_link' => array(
           'name' => 'salesplanningversions_link',
           'vname' => 'LBL_SALESPLANNINGPLANNINGVERSIONS',
           'type' => 'link',
           'relationship' => 'salesplanningversions_salesplanningcontentdatas',
           'link_type' => 'one',
           'side' => 'right',
           'source' => 'non-db'
        ),


        #create relationship to SalesPlanningContentField
        'salesplanningcontentfield_id' => array(
           'name' => 'salesplanningcontentfield_id',
           'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTFIELD_ID',
           'type' => 'varchar',
           'len' => 36
        ),

        'salesplanningcontentfield_name' => array(
           'name' => 'salesplanningcontentfield_name',
           'vname' => 'LBL_SALES_PLANNING_CONTENT_FIELD',
           'source' => 'non-db',
           'type' => 'relate',
           'len' => '255',
           'id_name' => 'salesplanningcontentfield_id',
           'module' => 'SalesPlanningContentFields',
           'link' => 'salesplanningcontentfields_link',
           'join_name' => 'salesplanningcontentfields',
           'rname' => 'name'
        ),

        'salesplanningcontentfields_link' => array(
           'name' => 'salesplanningcontentfields_link',
           'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTFIELDS',
           'type' => 'link',
           'relationship' => 'salesplanningcontentfields_salesplanningcontentdatas',
           'link_type' => 'one',
           'side' => 'right',
           'source' => 'non-db'
        )


    ),

 	'indices' => array(
        array('name' => 'salesplanningversion_id', 'type' => 'index', 'fields' => array('salesplanningversion_id')),
        array('name' => 'salesplanningcontentfield_id', 'type' => 'index', 'fields' => array('salesplanningcontentfield_id')),
        array('name' => 'salesplanningnode_id', 'type' => 'index', 'fields' => array('salesplanningnode_id')),
        array('name' => 'idx_name_version_field_node_del', 'type' => 'index', 'fields' => array('name', 'salesplanningversion_id', 'salesplanningcontentfield_id', 'salesplanningnode_id', 'deleted')),
    ),

  	'relationships' => array (
    ),

  	'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningContentData','SalesPlanningContentData', array('default', 'assignable'));
