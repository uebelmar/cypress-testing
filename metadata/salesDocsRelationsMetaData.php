<?php


$dictionary ['salesdocsflow'] = array(
    'table' => 'salesdocsflow',
    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesdoc_from_id', 'type' => 'id'),
        array('name' => 'salesdoc_to_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),
    'indices' => array(
        array('name' => 'salesdocsflowpk', 'type' => 'primary', 'fields' => array('id'))
    ),
    'relationships' => array(
        'salesdocsflow' => array(
            'rhs_module' => 'SalesDocs',
            'rhs_table' => 'salesdocs',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocs',
            'lhs_table' => 'salesdocs',
            'lhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'salesdocsflow',
            'join_key_lhs' => 'salesdoc_from_id',
            'join_key_rhs' => 'salesdoc_to_id',
            'reverse' => 0
        )
    )
);

$dictionary ['salesdocsitemsflow'] = array(
    'table' => 'salesdocsitemsflow',
    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesdocitem_from_id', 'type' => 'id'),
        array('name' => 'salesdocitem_to_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),
    'indices' => array(
        array('name' => 'salesdocsitemsflowpk', 'type' => 'primary', 'fields' => array('id'))
    ),
    'relationships' => array(
        'salesdocsitemsflow' => array(
            'rhs_module' => 'SalesDocItems',
            'rhs_table' => 'salesdocitems',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'salesdocsitemsflow',
            'join_key_lhs' => 'salesdocitem_from_id',
            'join_key_rhs' => 'salesdocitem_to_id',
            'reverse' => 0
        )
    )
);
