<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningCharacteristic'] = array(
    'table' => 'salesplanningcharacteristics',
    'audited' => false,
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 36,
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'field_type' => array(
            'name' => 'field_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => '12',
            'options' => 'sales_planning_characteristics_fieldtype_dom',
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'field_size' => array(
            'name' => 'field_size',
            'vname' => 'LBL_SIZE',
            'type' => 'int',
            'required' => false,
            'reportable' => true,
            'massupdate' => false,
        ),
        'field_module' => array(
            'name' => 'field_module',
            'vname' => 'LBL_MODULE',
            'type' => 'varchar',
            'len' => 30,
            'required' => false
        ),
        'field_link' => array(
            'name' => 'field_link',
            'vname' => 'LBL_LINK',
            'type' => 'varchar',
            'len' => 30,
            'required' => false
        ),
        'field_reference' => array(
            'name' => 'field_reference',
            'vname' => 'LBL_REFERENCE',
            'type' => 'varchar',
            'len' => '20',
            'reportable' => true,
            'massupdate' => false,
        ),
        'salesplanningcharacteristicvalues' => array(
            'name' => 'salesplanningcharacteristicvalues',
            'vname' => 'LBL_SALESPLANNINGCHARACTERISTICVALUES',
            'type' => 'link',
            'relationship' => 'salesplanningcharacteristics_salesplanningcharacteristicvalues',
            'source' => 'non-db',
            'module' => 'SalesPlanningCharacteristicValues',
            'id_name' => 'salesplanningcharacteristic_id'
        ),
        'salesplanningscopesets_characteristic_sequence' => array(
            'name' => 'salesplanningscopesets_characteristic_sequence',
            'vname' => 'LBL_SEQUENCE',
            'type' => 'varchar',
            'source' => 'non-db'
        ),
        'salesplanningscopesets' => array(
            'name' => 'salesplanningscopesets',
            'type' => 'link',
            'relationship' => 'salesplanningscopesets_salesplanningcharacteristics',
            'module' => 'SalesPlanningScopeSets',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SALESPLANNINGSCOPESETS',
            'duplicate_merge' => 'disabled',
            'rel_fields' => array(
                'characteristic_sequence' => array(
                    'map' => 'salesplanningscopesets_characteristic_sequence'
                )
            )
        )
    ),
    'indices' => array(
    ),
    'relationships' => array(
        'salesplanningcharacteristics_salesplanningcharacteristicvalues' => array(
            'lhs_module' => 'SalesPlanningCharacteristics',
            'lhs_table' => 'salesplanningcharacteristics',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesPlanningCharacteristicValues',
            'rhs_table' => 'salesplanningcharacteristicvalues',
            'rhs_key' => 'salesplanningcharacteristic_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningCharacteristics', 'SalesPlanningCharacteristic', array('default', 'assignable'));
