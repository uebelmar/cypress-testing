<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningTerritory'] = array(
	'table' => 'salesplanningterritories',
	'audited' => false,
	'fields' => array(
        'name' => array(
            'name' => 'name',
    	    'vname' => 'LBL_NAME',
	        'type' => 'varchar',
	        'len' => 100,
	        'required' => true,
    	    'reportable' => true,
			'massupdate' => false,
        ),
        'salesplanningnodes' => array(
           'name' => 'salesplanningnodes',
           'vname' => 'LBL_SALESPLANNINGPLANNINGNODES',
           'type' => 'link',
           'relationship' => 'salesplanningterritories_salesplanningnodes',
           'source' => 'non-db',
           'side' => 'right',
        ),
        'salesplanningscopesets' => array(
            'name' => 'salesplanningscopesets',
            'type' => 'link',
            'module' => 'SalesPlanningScopeSets',
            'relationship' => 'salesplanningscopesets_salesplanningterritories',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESPLANNINGSCOPESETS',
            'duplicate_merge' => 'disabled',
        )

    ),

 	'indices' => array(
        array('name' => 'idx_salesplanningterritories_name', 'type' => 'index', 'fields' => array('name', 'deleted')),
    ),

  	'relationships' => array (
    	'salesplanningterritories_salesplanningnodes' => array(
           'lhs_module' => 'SalesPlanningTerritories',
           'lhs_table' => 'salesplanningterritories',
           'lhs_key' => 'id',
           'rhs_module' => 'SalesPlanningNodes',
           'rhs_table' => 'salesplanningnodes',
           'rhs_key' => 'salesplanningterritory_id',
           'relationship_type' => 'one-to-many'
        ),
    ),

  	'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningTerritories','SalesPlanningTerritory', array('default', 'assignable'));
