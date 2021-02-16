<?php
$dictionary['salesdocs_serviceequipments'] = array(
    'table' => 'salesdocs_serviceequipments'
, 'fields' => array(
        array('name' => 'id', 'type' => 'varchar', 'len' => '36')
    , array('name' => 'salesdoc_id', 'type' => 'char', 'len' => '36')
    , array('name' => 'serviceequipment_id', 'type' => 'char', 'len' => '36')
    , array('name' => 'date_modified', 'type' => 'datetime')
    , array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => true, 'default' => '0')
    )
, 'indices' => array(
        array('name' => 'salesdocs_serviceequipmentspk', 'type' => 'primary', 'fields' => array('id'))
    , array('name' => 'idx_salesdocs_serviceequipmentsalt', 'type' => 'alternate_key', 'fields' => array('salesdoc_id', 'serviceequipment_id'))
    , array('name' => 'idx_salesdocservequ_del', 'type' => 'index', 'fields' => array('salesdoc_id', 'serviceequipment_id', 'deleted'))
    )
, 'relationships' => array(
        'salesdocs_serviceequipments' => array(
            'lhs_module' => 'SalesDocs',
            'lhs_table' => 'salesdocs',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceEquipments',
            'rhs_table' => 'ServiceEquipment',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'salesdocs_serviceequipments',
            'join_key_lhs' => 'salesdoc_id',
            'join_key_rhs' => 'serviceequipment_id'
        )
    )
);
