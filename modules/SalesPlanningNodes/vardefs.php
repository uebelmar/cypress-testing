<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningNode'] = array(
    'table' => 'salesplanningnodes',
    'audited' => false,
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'salesplanningcharacteristicvalue_name' => array(
            'name' => 'salesplanningcharacteristicvalue_name',
            'rname' => 'name',
            'id_name' => 'salesplanningcharacteristicvalue_id',
            'vname' => 'LBL_SALES_PLANNING_CHARACTERISTIC_VALUE',
            'type' => 'relate',
            'link' => 'salesplanningcharacteristicvalues',
            'module' => 'SalesPlanningCharacteristicValues',
            'source' => 'non-db',
        ),
        'salesplanningcharacteristicvalue_id' => array(
            'name' => 'salesplanningcharacteristicvalue_id',
            'rname' => 'id',
            'type' => 'relate',
            'module' => 'SalesPlanningCharacteristicValues',
            'dbType' => 'id',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'hideacl' => true,
        ),
        'salesplanningcharacteristicvalues' => array(
            'name' => 'salesplanningcharacteristicvalues',
            'type' => 'link',
            'relationship' => 'salesplanningnodes_salesplanningcharacteristicvalues',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
        ),
        'salesplanningterritory_id' => array(
            'name' => 'salesplanningterritory_id',
            'vname' => 'LBL_SALES_PLANNING_TERRITORY_ID',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesplanningscopeset_id' => array(
            'name' => 'salesplanningscopeset_id',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesplanningscopeset_name' => array(
            'name' => 'salesplanningscopeset_name',
            'vname' => 'LBL_SALES_PLANNING_SCOPESET',
            'source' => 'non-db',
            'type' => 'relate',
            'module' => 'SalesPlanningScopeSets',
            'id_name' => 'salesplanningscopeset_id',
            'link' => 'salesplanningscopesets',
            'rname' => 'name'
        ),
        'salesplanningterritory_name' => array(
            'name' => 'salesplanningterritory_name',
            'vname' => 'LBL_SALES_PLANNING_TERRITORY',
            'source' => 'non-db',
            'type' => 'relate',
            'id_name' => 'salesplanningterritory_id',
            'module' => 'SalesPlanningTerritories',
            'link' => 'salesplanningterritories',
            'rname' => 'name'
        ),
        'salesplanningterritories' => array(
            'name' => 'salesplanningterritories',
            'type' => 'link',
            'module' => 'SalesPlanningTerritories',
            'relationship' => 'salesplanningterritories_salesplanningnodes',
            'source' => 'non-db',
            'link_type' => 'one',
            'side' => 'right',
        ),
        'salesplanningcontentdata' => array(
            'name' => 'salesplanningcontentdata',
            'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTDATA',
            'type' => 'link',
            'relationship' => 'salesplanningnodes_salesplanningcontentdata',
            'source' => 'non-db',
            'side' => 'right',
        ),
        'salesplanningscopesets' => array(
            'name' => 'salesplanningscopesets',
            'module' => 'SalesPlanningScopeSets',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_salesplanningnodes',
            'source' => 'non-db'
        )
    ),
    'indices' => array(
        array('name' => 'idx_salesplanningterritory_deleted', 'type' => 'index', 'fields' => array('salesplanningterritory_id', 'deleted')),
        array('name' => 'idx_salesplanningnodes_salesplanningscopeset_id', 'type' => 'index', 'fields' => array('salesplanningscopeset_id', 'deleted')),

    ),
    'relationships' => array(
        'salesplanningnodes_salesplanningcontentdata' => array(
            'lhs_module' => 'SalesPlanningNodes',
            'lhs_table' => 'salesplanningnodes',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesPlanningContentData',
            'rhs_table' => 'salesplanningcontentdata',
            'rhs_key' => 'salesplanningnode_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningNodes', 'SalesPlanningNode', array('default', 'assignable'));
