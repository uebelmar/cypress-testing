<?php



$dictionary['SAPIdocFields'] = array(
    'table' => 'sapidocfields',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'comment' => 'Unique identifier'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
        ),
        'active' => array(
            'name' => 'active',
            'vname' => 'LBL_ACTIVE',
            'type' => 'bool',
        ),
        'custom_field_function' => array(
            'name' => 'custom_field_function',
            'vname' => 'LBL_CUSTOM_FIELD_FUNCTION',
            'type' => 'varchar',
            'len' => 255,
        ),
        'mapping_rule' => array(
            'name' => 'mapping_rule',
            'vname' => 'LBL_MAPPING_RULE',
            'type' => 'varchar',
            'len' => 50,
        ),
        'mapping_order' => array(
            'name' => 'mapping_order',
            'vname' => 'LBL_MAPPING_ORDER',
            'type' => 'int',
        ),
        'inbound' => array(
            'name' => 'inbound',
            'vname' => 'LBL_INBOUND',
            'type' => 'bool',
        ),
        'outbound' => array(
            'name' => 'outbound',
            'vname' => 'LBL_OUTBOUND',
            'type' => 'bool',
        ),
        'required' => array(
            'name' => 'required',
            'vname' => 'LBL_REQUIRED',
            'type' => 'bool',
        ),
        'identifier' => array(
            'name' => 'identifier',
            'vname' => 'LBL_IDENTIFIER',
            'type' => 'bool',
        ),
        'mapping_field' => array(
            'name' => 'mapping_field',
            'vname' => 'LBL_MAPPING_FIELD',
            'type' => 'varchar',
            'len' => 255,
        ),
        'sap_field' => array(
            'name' => 'sap_field',
            'vname' => 'LBL_SAP_FIELD',
            'type' => 'varchar',
            'len' => 255,
        ),
        'mapping_field_default' => array(
            'name' => 'mapping_field_default',
            'vname' => 'LBL_MAPPING_FIELD_DEFAULT',
            'type' => 'varchar',
            'len' => 255,
        ),
        'mapping_field_prefix' => array(
            'name' => 'mapping_field_prefix',
            'vname' => 'LBL_MAPPING_FIELD_PREFIX',
            'type' => 'varchar',
            'len' => 255,
        ),
        'value_conector' => array(
            'name' => 'value_conector',
            'vname' => 'LBL_VALUE_CONECTOR',
            'type' => 'varchar',
            'len' => 25,
        ),
        'segment_id' => array(
            'name' => 'segment_id',
            'vname' => 'LBL_SEGMENT_ID',
            'type' => 'id',
            'required' => true,
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocfieldspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocfields' . ( ++$i), 'type' => 'index', 'fields' => array('segment_id')),
    ),
);


