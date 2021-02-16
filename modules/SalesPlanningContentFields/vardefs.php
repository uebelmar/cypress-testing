<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesPlanningContentField'] = array(
    'table' => 'salesplanningcontentfields',
    'audited' => false,
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 40,
            'required' => true,
            'reportable' => true,
            'massupdate' => false,
        ),
        'field_type' => array(
            'name' => 'field_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => '12',
            'options' => 'sales_planning_content_field_dom',
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
        'currency_id' => array(
            'name' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'type' => 'id',
            'reportable' => true,
            'massupdate' => false,
        ),
        'editable' => array(
            'name' => 'editable',
            'vname' => 'LBL_EDITABLE',
            'type' => 'bool',
            'reportable' => true,
            'massupdate' => false,
        ),
        'redetermine_value' => array(
            'name' => 'redetermine_value',
            'vname' => 'LBL_REDETERMINE_VALUE',
            'type' => 'bool',
            'reportable' => true,
            'massupdate' => false,
        ),
        'storable' => array(
            'name' => 'storable',
            'vname' => 'LBL_STORABLE',
            'type' => 'bool',
            'reportable' => true,
            'massupdate' => false,
        ),
        'group_action' => array(
            'name' => 'group_action',
            'vname' => 'LBL_GROUP_ACTION',
            'type' => 'enum',
            'len' => '3',
            'dbType' => 'varchar',
            'options' => 'sales_planning_group_actions_dom',
            'default' => 'sum',
            'reportable' => true,
            'massupdate' => false,
        ),
        'formula' => array(
            'name' => 'formula',
            'vname' => 'LBL_FORMULA',
            'type' => 'varchar',
            'len' => '200',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'a formula based on the guids for calculate the value'
        ),
        'cbfunction' => array(
            'name' => 'cbfunction',
            'vname' => 'LBL_CALLBACK_FUNCTION',
            'type' => 'varchar',
            'len' => '200',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'a custom function'
        ),
        'cbfunction_sum' => array(
            'name' => 'cbfunction_sum',
            'vname' => 'LBL_CALLBACK_FUNCTION_SUM',
            'type' => 'varchar',
            'len' => '200',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'a custom function for the sum'
        ),
        'static_field' => array(
            'name' => 'static_field',
            'vname' => 'LBL_STATIC',
            'type' => 'bool',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'values will be redetermined if true'
        ),
        'sort_order' => array(
            'name' => 'sort_order',
            'vname' => 'LBL_SORT_SEQUENCE',
            'type' => 'int',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'the order'
        ),
        'display_color' => array(
            'name' => 'display_color',
            'vname' => 'LBL_COLOR',
            'type' => 'varchar',
            'len' => 10
        ),
        'formula_sum' => array(
            'name' => 'formula_sum',
            'vname' => 'LBL_FORMULA_SUM',
            'type' => 'varchar',
            'len' => 200
        ),
        'salesplanningcontentdatas' => array(
            'name' => 'salesplanningcontentdatas',
            'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTDATAS',
            'type' => 'link',
            'relationship' => 'salesplanningcontentfields_salesplanningcontentdatas',
            'source' => 'non-db',
            'side' => 'right',
        ),
        'salesplanningcontents' => array(
            'name' => 'salesplanningcontents',
            'type' => 'link',
            'relationship' => 'salesplanningcontents_salesplanningcontentfields',
            'link_type' => 'one',
            'source' => 'non-db',
            'module' => 'SalesPlanningContents',
            'vname' => 'LBL_SALESPLANNINGPLANNINGCONTENTS',
            'duplicate_merge' => 'disabled',
        )
    ),
    'indices' => array(
        array('name' => 'idx_salesplanningcontentfields_name', 'type' => 'index', 'fields' => array('name', 'deleted')),
    ),
    'relationships' => array(
        'salesplanningcontentfields_salesplanningcontentdatas' => array(
            'lhs_module' => 'SalesPlanningContentFields',
            'lhs_table' => 'salesplanningcontentfields',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesPlanningContentData',
            'rhs_table' => 'salesplanningcontentdata',
            'rhs_key' => 'salesplanningcontentfield_id',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'optimistic_lock' => true,
);




VardefManager::createVardef('SalesPlanningContentFields', 'SalesPlanningContentField', array('default', 'assignable'));
