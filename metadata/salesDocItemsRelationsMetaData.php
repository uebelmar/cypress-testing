<?php


$dictionary ['salesdocitemsquotes_salesdocitemsorders'] = array(
    'table' => 'salesdocitemsquotes_salesdocitemsorders',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesdocitemquote_id', 'type' => 'id'),
        array('name' => 'salesdocitemorder_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),
    'indices' => array(
        array('name' => 'salesdocitemsqtorpk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_salesdocitemsqtoralt', 'type' => 'alternate_key', 'fields' => array('salesdocitemquote_id', 'salesdocitemorder_id')),
        array('name' => 'idx_salesdocitemsqtor_del', 'type' => 'index', 'fields'=> array('salesdocitemquote_id', 'salesdocitemorder_id', 'deleted'))
    ),
    'relationships' => array(
        'salesdocitemsquotes_salesdocitemsorders' => array(
            'rhs_module' => 'SalesDocItems',
            'rhs_table' => 'salesdocitems',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'salesdocitemsquotes_salesdocitemsorders',
            'join_key_lhs' => 'salesdocitemquote_id',
            'join_key_rhs' => 'salesdocitemorder_id',
        ),
    )
);


$dictionary ['salesdocitemsquotes_salesdocitemscontracts'] = array(
    'table' => 'salesdocitemsquotes_salesdocitemscontracts',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesdocitemquote_id', 'type' => 'id'),
        array('name' => 'salesdocitemcontract_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),
    'indices' => array(
        array('name' => 'salesdocitemsqtctpk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_salesdocitemsqtctalt', 'type' => 'alternate_key', 'fields' => array('salesdocitemquote_id', 'salesdocitemcontract_id')),
        array('name' => 'idx_salesdocitemsqtct_del', 'type' => 'index', 'fields'=> array('salesdocitemquote_id', 'salesdocitemcontract_id', 'deleted'))
    ),
    'relationships' => array(
        'salesdocitemsquotes_salesdocitemscontracts' => array(
            'rhs_module' => 'SalesDocItems',
            'rhs_table' => 'salesdocitems',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'salesdocitemsquotes_salesdocitemscontracts',
            'join_key_lhs' => 'salesdocitemquote_id',
            'join_key_rhs' => 'salesdocitemcontract_id',
        ),
    )
);

$dictionary ['salesdocitemsorders_salesdocitemsinvoices'] = array(
    'table' => 'salesdocitemsorders_salesdocitemsinvoices',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesdocitemorder_id', 'type' => 'id'),
        array('name' => 'salesdociteminvoice_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),
    'indices' => array(
        array('name' => 'salesdocitemsorivpk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_salesdocitemsorivalt', 'type' => 'alternate_key', 'fields' => array('salesdocitemorder_id', 'salesdociteminvoice_id')),
        array('name' => 'idx_salesdocitemsoriv_del', 'type' => 'index', 'fields'=> array('salesdocitemorder_id', 'salesdociteminvoice_id', 'deleted'))
    ),
    'relationships' => array(
        'salesdocitemsorders_salesdocitemsinvoices' => array(
            'rhs_module' => 'SalesDocItems',
            'rhs_table' => 'salesdocitems',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'salesdocitemsorders_salesdocitemsinvoices',
            'join_key_lhs' => 'salesdocitemorder_id',
            'join_key_rhs' => 'salesdociteminvoice_id',
        ),
    )
);

$dictionary ['salesdocitemscontracts_salesdocitemsinvoices'] = array(
    'table' => 'salesdocitemscontracts_salesdocitemsinvoices',
    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesdocitemcontract_id', 'type' => 'id'),
        array('name' => 'salesdociteminvoice_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),
    'indices' => array(
        array('name' => 'salesdocitemsctivpk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_salesdocitemsctivalt', 'type' => 'alternate_key', 'fields' => array('salesdocitemcontract_id', 'salesdociteminvoice_id')),
        array('name' => 'idx_salesdocitemsctiv_del', 'type' => 'index', 'fields'=> array('salesdocitemcontract_id', 'salesdociteminvoice_id', 'deleted'))
    ),
    'relationships' => array(
        'salesdocitemscontracts_salesdocitemsinvoices' => array(
            'rhs_module' => 'SalesDocItems',
            'rhs_table' => 'salesdocitems',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'salesdocitemscontracts_salesdocitemsinvoices',
            'join_key_lhs' => 'salesdocitemcontract_id',
            'join_key_rhs' => 'salesdociteminvoice_id',
        ),
    )
);

