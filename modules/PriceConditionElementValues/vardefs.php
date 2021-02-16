<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['PriceConditionElementValue'] = array(
    'table' => 'priceconditionelementvalues',
    'fields' => array(
        'id' =>
            array(
                'name' => 'id',
                'vname' => 'LBL_ID',
                'type' => 'id',
                'required' => true,
                'reportable' => true,
                'comment' => 'Unique identifier',
                'duplicate_merge' => 'disabled',
                'audited' => false
            ),
        'date_entered' =>
            array(
                'name' => 'date_entered',
                'vname' => 'LBL_DATE_ENTERED',
                'type' => 'datetime',
                'group' => 'created_by_name',
                'comment' => 'Date record created',
                'enable_range_search' => true,
                'options' => 'date_range_search_dom',
                'duplicate_merge' => 'disabled',
                'audited' => false
            ),
        'date_modified' =>
            array(
                'name' => 'date_modified',
                'vname' => 'LBL_DATE_MODIFIED',
                'type' => 'datetime',
                'group' => 'modified_by_name',
                'comment' => 'Date record last modified',
                'enable_range_search' => true,
                'options' => 'date_range_search_dom',
                'duplicate_merge' => 'disabled',
                'audited' => false
            ),
        'date_indexed' =>
            array(
                'name' => 'date_indexed',
                'vname' => 'LBL_DATE_INDEXED',
                'type' => 'datetime',
                'comment' => 'Date record last indexed',
                'enable_range_search' => true,
                'options' => 'date_range_search_dom',
                'duplicate_merge' => 'disabled',
                'audited' => false
            ),
        'deleted' =>
            array(
                'name' => 'deleted',
                'vname' => 'LBL_DELETED',
                'type' => 'bool',
                'default' => '0',
                'reportable' => false,
                'comment' => 'Record deletion indicator',
                'duplicate_merge' => 'disabled',
                'audited' => false
            ),
        'pricecondition_id' => array (
            'name' => 'pricecondition_id',
            'vname' => 'LBL_PRICECONDITION',
            'type' => 'id',
            'required' => true
        ),
        'pricecondition' => array(
            'name' => 'pricecondition',
            'vname' => 'LBL_PRICECONDITION',
            'module' => 'PriceConditions',
            'type' => 'link',
            'relationship' => 'pricecondition_priceconditionev',
            'source' => 'non-db'
        ),
        'element_id' => array(
            'name' => 'element_id',
            'vname' => 'LBL_ELEMENT_ID',
            'type' => 'id',
            'comment' => 'the id of the element'
        ),
        'element_value' => array(
            'name' => 'element_value',
            'vname' => 'LBL_ELEMENT_VALUE',
            'type' => 'varchar',
            'comment' => 'the value of the element'
        ),
    ),
    'relationships' => array(
        'pricecondition_priceconditionev' => array(
            'lhs_module' => 'PriceConditions',
            'lhs_table' => 'priceconditions',
            'lhs_key' => 'id',
            'rhs_module' => 'PriceConditionElementValues',
            'rhs_table' => 'priceconditionelementvalues',
            'rhs_key' => 'pricecondition_id',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'indices' => array(
        'id' => array('name' => 'priceconditionelementvalues_pk', 'type' => 'primary', 'fields' => array('id')),
        'idx_priceconditionev_pcid' => array('name' => 'idx_priceconditionev_pcid', 'type' => 'index', 'fields' => array('pricecondition_id')),
        'idx_priceconditionev_pcid' => array('name' => 'idx_priceconditionev_ev', 'type' => 'index', 'fields' => array('pricecondition_id', 'element_value'))
    )
);

VardefManager::createVardef('PriceConditionElementValues', 'PriceConditionElementValue', array());
