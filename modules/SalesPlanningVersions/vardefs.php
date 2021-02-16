<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningVersion'] = array(
    'table' => 'salesplanningversions',
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
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'sales_planning_version_status_dom',
            'len' => 1,
            'required' => false,
            'reportable' => true,
            'massupdate' => false,
        ),
        'adminonly' => array(
            'name' => 'adminonly',
            'vname' => 'LBL_ADMINONLY',
            'type' => 'bool',
            'required' => false,
            'reportable' => true,
            'massupdate' => false,
        ),
        'date_start' => array(
            'name' => 'date_start',
            'vname' => 'LBL_DATE_START',
            'type' => 'date',
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'periode_unit' => array(
            'name' => 'periode_unit',
            'vname' => 'LBL_PERIODE_UNIT',
            'type' => 'enum',
            'options' => 'sales_planning_periode_units_dom',
            'default' => 'months',
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'periode_segments' => array(
            'name' => 'periode_segments',
            'vname' => 'LBL_PERIODE_SEGMENTS',
            'type' => 'int',
            'default' => '5',
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        // create relationship to SalesPlanningScopeSets
        'salesplanningscopeset_id' => array(
            'name' => 'salesplanningscopeset_id',
            'vname' => 'LBL_SALESPLANNINGSCOPESET_ID',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesplanningscopeset_name' => array(
            'name' => 'salesplanningscopeset_name',
            'vname' => 'LBL_SALES_PLANNING_SCOPESET',
            'source' => 'non-db',
            'type' => 'relate',
            'len' => '255',
            'id_name' => 'salesplanningscopeset_id',
            'module' => 'SalesPlanningScopeSets',
            'link' => 'salesplanningscopesets',
            'join_name' => 'salesplanningscopesets',
            'rname' => 'name'
        ),
        'salesplanningscopesets' => array(
            'name' => 'salesplanningscopesets',
            'vname' => 'LBL_SALESPLANNINGSCOPESETS',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_salesplanningversions',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db'
        ),
        'salesplanningcontents' => array(
            'name' => 'salesplanningcontents',
            'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTS',
            'type' => 'link',
            'relationship' => 'salesplanningversions_salesplanningcontents',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db',
            'module' => 'SalesPlanningContents',
            'default' => true
        ),
        'salesplanningcontentdatas' => array(
            'name' => 'salesplanningcontentdatas',
            'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTDATAS',
            'type' => 'link',
            'relationship' => 'salesplanningversions_salesplanningcontentdatas',
            'source' => 'non-db',
            'side' => 'right',
        )
    ),
    'indices' => array(
    //array('name' => 'idx_del', 'type' => 'index', 'fields' => array('deleted')),
        array('name' => 'idx_salesplanningversions_name', 'type' => 'index', 'fields' => array('name', 'deleted')),
        array('name' => 'idx_salesplanningversions_salesplanningscopeset_id', 'type' => 'index', 'fields' => array('salesplanningscopeset_id', 'deleted')),
        array('name' => 'idx_salesplanningversions_status', 'type' => 'index', 'fields' => array('status', 'deleted')),
        array('name' => 'idx_salesplanningversions_adminonly', 'type' => 'index', 'fields' => array('adminonly', 'deleted')),
        array('name' => 'idx_salesplanningversions_periode_unit', 'type' => 'index', 'fields' => array('periode_unit', 'deleted')),
        array('name' => 'idx_salesplanningversions_periode_segments', 'type' => 'index', 'fields' => array('periode_segments', 'deleted')),
    ),
    'relationships' => array(
        'salesplanningversions_salesplanningcontentdatas' => array(
            'lhs_module' => 'SalesPlanningVersions',
            'lhs_table' => 'salesplanningversions',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesPlanningContentData',
            'rhs_table' => 'salesplanningcontentdata',
            'rhs_key' => 'salesplanningversion_id',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningVersions', 'SalesPlanningVersion', array('default', 'assignable'));
