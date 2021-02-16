<?php
// Not sure we need this at all.... commented for now

if (is_file("modules/ServiceEquipments/ServiceEquipment.php")) {

    $dictionary['serviceorders_serviceequipments'] = array(
        'table' => 'serviceorders_serviceequipments'
    , 'fields' => array(
            array('name' => 'id', 'type' => 'varchar', 'len' => '36')
        , array('name' => 'serviceorder_id', 'type' => 'char', 'len' => '36')
        , array('name' => 'serviceequipment_id', 'type' => 'char', 'len' => '36')
        , array('name' => 'date_modified', 'type' => 'datetime')
        , array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => true, 'default' => '0')
        )
    , 'indices' => array(
            array('name' => 'serviceorders_serviceequipmentspk', 'type' => 'primary', 'fields' => array('id'))
        , array('name' => 'idx_serviceorders_serviceequipmentsalt', 'type' => 'alternate_key', 'fields' => array('serviceorder_id', 'serviceequipment_id'))
        , array('name' => 'idx_servordservequ_del', 'type' => 'index', 'fields' => array('serviceorder_id', 'serviceequipment_id', 'deleted'))
        )
    , 'relationships' => array(
            'serviceorders_serviceequipments' => array(
                'lhs_module' => 'ServiceOrders',
                'lhs_table' => 'serviceorders',
                'lhs_key' => 'id',
                'rhs_module' => 'ServiceEquipments',
                'rhs_table' => 'ServiceEquipment',
                'rhs_key' => 'id',
                'relationship_type' => 'many-to-many',
                'join_table' => 'serviceorders_serviceequipments',
                'join_key_lhs' => 'serviceorder_id',
                'join_key_rhs' => 'serviceequipment_id'
            )
        )
    );
//
//    $dictionary['serviceequipments_contacts'] = array(
//        'table' => 'serviceequipments_contacts'
//    , 'fields' => array(
//            array('name' => 'id', 'type' => 'varchar', 'len' => '36')
//        , array('name' => 'serviceequipment_id', 'type' => 'char', 'len' => '36')
//        , array('name' => 'contact_id', 'type' => 'char', 'len' => '36')
//        , array('name' => 'date_modified', 'type' => 'datetime')
//        , array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => true, 'default' => '0')
//        )
//    , 'indices' => array(
//            array('name' => 'serviceequipments_contactspk', 'type' => 'primary', 'fields' => array('id'))
//        , array('name' => 'idx_serviceequipments_contacts_alt', 'type' => 'alternate_key', 'fields' => array('serviceequipment_id', 'contact_id'))
//        , array('name' => 'idx_serviceequipments_contacts_del', 'type' => 'index', 'fields' => array('serviceequipment_id', 'contact_id', 'deleted'))
//        )
//    , 'relationships' => array(
//            'serviceequipments_contacts' => array(
//                'lhs_module' => 'ServiceEquipments',
//                'lhs_table' => 'ServiceEquipment',
//                'lhs_key' => 'id',
//                'rhs_module' => 'Contacts',
//                'rhs_table' => 'contacts',
//                'rhs_key' => 'id',
//                'relationship_type' => 'many-to-many',
//                'join_table' => 'serviceequipments_contacts',
//                'join_key_lhs' => 'serviceequipment_id',
//                'join_key_rhs' => 'contact_id'
//            )
//        )
//    );
//
//    $dictionary['serviceequipments_users'] = array(
//        'table' => 'serviceequipments_users'
//    , 'fields' => array(
//            array('name' => 'id', 'type' => 'varchar', 'len' => '36')
//        , array('name' => 'serviceequipment_id', 'type' => 'char', 'len' => '36')
//        , array('name' => 'user_id', 'type' => 'char', 'len' => '36')
//        , array('name' => 'date_modified', 'type' => 'datetime')
//        , array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => true, 'default' => '0')
//        )
//    , 'indices' => array(
//            array('name' => 'serviceequipments_userspk', 'type' => 'primary', 'fields' => array('id'))
//        , array('name' => 'idx_serviceequipments_users_alt', 'type' => 'alternate_key', 'fields' => array('serviceequipment_id', 'user_id'))
//        , array('name' => 'idx_serviceequipments_users_del', 'type' => 'index', 'fields' => array('serviceequipment_id', 'user_id', 'deleted'))
//        )
//    , 'relationships' => array(
//            'serviceequipments_users' => array(
//                'lhs_module' => 'ServiceEquipments',
//                'lhs_table' => 'ServiceEquipment',
//                'lhs_key' => 'id',
//                'rhs_module' => 'Users',
//                'rhs_table' => 'users',
//                'rhs_key' => 'id',
//                'relationship_type' => 'many-to-many',
//                'join_table' => 'serviceequipments_users',
//                'join_key_lhs' => 'serviceequipment_id',
//                'join_key_rhs' => 'user_id'
//            )
//        )
//    );
}
