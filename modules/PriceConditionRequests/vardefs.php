<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['PriceConditionRequest'] = array(
    'table' => 'priceconditionrequests',
    'fields' => array(
        'parent_type' => [
            'name'     => 'parent_type',
            'vname'    => 'LBL_PARENT_TYPE',
            'type'     => 'parent_type',
            'dbType'   => 'varchar',
            'required' => false,
            'group'    => 'parent_name',
            'options'  => 'parent_type_display',
            'len'      => 255,
            'comment'  => 'The Sugar object to which the call is related',
        ],
        'parent_name' => [
            'name'        => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name'   => 'parent_type',
            'id_name'     => 'parent_id',
            'vname'       => 'LBL_RELATED_TO',
            'type'        => 'parent',
            'group'       => 'parent_name',
            'source'      => 'non-db',
            'options'     => 'parent_type_display',
        ],
        'parent_id' => [
            'name'       => 'parent_id',
            'vname'      => 'LBL_LIST_RELATED_TO_ID',
            'type'       => 'id',
            'group'      => 'parent_name',
            'reportable' => false,
            'comment'    => 'The ID of the parent Sugar object identified by parent_type'
        ],
        'priceconditions' => array(
            'name' => 'priceconditions',
            'vname' => 'LBL_PRICECONDITIONS',
            'module' => 'PriceConditions',
            'type' => 'link',
            'relationship' => 'priceconditionrequest_priceconditions',
            'source' => 'non-db',
            'comment' => 'the link to the priceconditions for this record'
        )
    ),
    'relationships' => array(
        'priceconditionrequest_priceconditions' => array(
            'lhs_module' => 'PriceConditionRequest',
            'lhs_table' => 'priceconditionrequest',
            'lhs_key' => 'id',
            'rhs_module' => 'PriceConditions',
            'rhs_table' => 'priceconditions',
            'rhs_key' => 'priceconditionrequest_id',
            'relationship_type' => 'one-to-many'
        ),
        'accounts_priceconditionrequests' => [
            'lhs_module'                     => 'Accounts',
            'lhs_table'                      => 'accounts',
            'lhs_key'                        => 'id',
            'rhs_module'                     => 'PriceConditionRequest',
            'rhs_table'                      => 'priceconditionrequest',
            'rhs_key'                        => 'parent_id',
            'relationship_type'              => 'one-to-many',
            'relationship_role_column'       => 'parent_type',
            'relationship_role_column_value' => 'Accounts',
        ],
        'products_priceconditionrequests' => [
            'lhs_module'                     => 'Products',
            'lhs_table'                      => 'products',
            'lhs_key'                        => 'id',
            'rhs_module'                     => 'PriceConditionRequest',
            'rhs_table'                      => 'priceconditionrequest',
            'rhs_key'                        => 'parent_id',
            'relationship_type'              => 'one-to-many',
            'relationship_role_column'       => 'parent_type',
            'relationship_role_column_value' => 'Products',
        ],
        'productvariants_priceconditionrequests' => [
            'lhs_module'                     => 'ProductVariants',
            'lhs_table'                      => 'productvariants',
            'lhs_key'                        => 'id',
            'rhs_module'                     => 'PriceConditionRequest',
            'rhs_table'                      => 'priceconditionrequest',
            'rhs_key'                        => 'parent_id',
            'relationship_type'              => 'one-to-many',
            'relationship_role_column'       => 'parent_type',
            'relationship_role_column_value' => 'ProductVariants',
        ],
    ),
    'indices' => array(
    )
);

VardefManager::createVardef('PriceConditionRequests', 'PriceConditionRequest', array('default', 'assignable'));

// ToDo: change with Vardef Manager
// do not set name to required
$dictionary['PriceCondition']['fields']['name']['required'] = false;
