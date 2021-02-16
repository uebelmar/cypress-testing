<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningScopeSet'] = array(
    'table' => 'salesplanningscopesets',
    'audited' => false,
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 90,
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        // reviewer (many-many relationship between SalesPlanningScopeSets and Users)
        'reviewer_name' => array(
            'name' => 'reviewer_name',
            'rname' => 'name',
            'id_name' => 'reviewer_id',
            'vname' => 'LBL_REVIEWER_NAME',
            'join_name' => 'reviewers',
            'type' => 'relate',
            'link' => 'reviewers',
            'table' => 'users',
            'isnull' => 'true',
            'module' => 'Users',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'reviewer_id' => array(
            'name' => 'reviewer_id',
            'rname' => 'id',
            'id_name' => 'user_id',
            'vname' => 'LBL_REVIEWER_ID',
            'type' => 'relate',
            'table' => 'users',
            'isnull' => 'true',
            'module' => 'Users',
            'dbType' => 'id',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'hideacl' => true,
        ),
        'reviewers' => array(
            'name' => 'reviewers',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_reviewers',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_REVIEWER',
            'duplicate_merge' => 'disabled',
        ),
        // territories (many-many relationship between SalesPlanningTerritories and KOrgObjects)
        'salesplanningterritory_name' => array(
            'name' => 'salesplanningterritory_name',
            'rname' => 'name',
            'id_name' => 'salesplanningterritory_id',
            'vname' => 'LBL_SALES_PLANNING_TERRITORY',
            'join_name' => 'salesplanningterritories',
            'type' => 'relate',
            'link' => 'salesplanningterritories',
            'table' => 'salesplanningterritories',
            'isnull' => 'true',
            'module' => 'SalesPlanningTerritories',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'salesplanningterritory_id' => array(
            'name' => 'salesplanningterritory_id',
            'rname' => 'id',
            'id_name' => 'salesplanningterritory_id',
            'vname' => 'LBL_SALESPLANNINGTERRITORY_ID',
            'type' => 'relate',
            'table' => 'salesplanningterritories',
            'isnull' => 'true',
            'module' => 'SalesPlanningTerritories',
            'dbType' => 'id',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'hideacl' => true,
        ),
        'salesplanningterritories' => array(
            'name' => 'salesplanningterritories',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_salesplanningterritories',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESPLANNINGTERRITORIES',
            'duplicate_merge' => 'disabled',
        ),
        // characteristics (many-many relationship between SalesPlanningScopeSets and SalesPlanningCharacteristics)
        'salesplanningcharacteristic_name' => array(
            'name' => 'salesplanningcharacteristic_name',
            'rname' => 'name',
            'id_name' => 'salesplanningcharacteristic_id',
            'vname' => 'LBL_SALES_PLANNING_CHARACTERISTIC',
            'join_name' => 'salesplanningcharacteristics',
            'type' => 'relate',
            'link' => 'salesplanningcharacteristics',
            'table' => 'salesplanningcharacteristics',
            'isnull' => 'true',
            'module' => 'SalesPlanningCharacteristics',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'salesplanningcharacteristic_id' => array(
            'name' => 'salesplanningcharacteristic_id',
            'rname' => 'id',
            'id_name' => 'user_id',
            'vname' => 'LBL_SALESPLANNINGCHARACTERISTIC_ID',
            'type' => 'relate',
            'table' => 'salesplanningcharacteristics',
            'isnull' => 'true',
            'module' => 'SalesPlanningCharacteristics',
            'dbType' => 'id',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'hideacl' => true,
        ),
        'salesplanningscopesets_characteristic_sequence' => array(
            'name' => 'salesplanningscopesets_characteristic_sequence',
            'vname' => 'LBL_SEQUENCE',
            'type' => 'varchar',
            'source' => 'non-db'
        ),
        'salesplanningcharacteristics' => array(
            'name' => 'salesplanningcharacteristics',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_salesplanningcharacteristics',
            'module' => 'SalesPlanningCharacteristics',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESPLANNINGCHARACTERISTICS',
            'duplicate_merge' => 'disabled',
            'rel_fields' => array(
                'characteristic_sequence' => array(
                    'map' => 'salesplanningscopesets_characteristic_sequence'
                )
            )
        ),
        'salesplanningnodes' => array(
            'name' => 'salesplanningnodes',
            'vname' => 'LBL_SALESPLANNINGPLANNINGNODES',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_salesplanningnodes',
            'source' => 'non-db',
            'side' => 'right',
        ),
        'salesplanningversions' => array(
            'name' => 'salesplanningversions',
            'vname' => 'LBL_SALESPLANNINGPLANNINGVERSIONS',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_salesplanningversions',
            'source' => 'non-db',
            'side' => 'right',
        )
    ),
    'indices' => array(
    ),
    'relationships' => array(
        'salesplanningscopesets_salesplanningnodes' => array(
            'lhs_module' => 'SalesPlanningScopeSets',
            'lhs_table' => 'salesplanningscopesets',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesPlanningNodes',
            'rhs_table' => 'salesplanningnodes',
            'rhs_key' => 'salesplanningscopeset_id',
            'relationship_type' => 'one-to-many'
        ),
        'salesplanningscopesets_salesplanningversions' => array(
            'lhs_module' => 'SalesPlanningScopeSets',
            'lhs_table' => 'salesplanningscopesets',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesPlanningVersions',
            'rhs_table' => 'salesplanningversions',
            'rhs_key' => 'salesplanningscopeset_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningScopeSets', 'SalesPlanningScopeSet', array('default', 'assignable'));
