<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['opportunities_contacts'] = array(
    'table' => 'opportunities_contacts',
    'fields' => array(
        array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'contact_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'opportunity_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'contact_role',
            'type' => 'varchar',
            'len' => '50'
        ),
        array(
            'name' => 'propensity_to_buy',
            'type' => 'enum',
            'options' => 'opportunity_relationship_buying_center_dom',
            'len' => '2'
        ),
        array(
            'name' => 'level_of_support',
            'type' => 'enum',
            'options' => 'opportunity_relationship_buying_center_dom',
            'len' => '2'
        ),
        array(
            'name' => 'level_of_influence',
            'type' => 'enum',
            'options' => 'opportunity_relationship_buying_center_dom',
            'len' => '2'
        ),
        array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false
        )
    ),
    'indices' => array(
        array(
            'name' => 'opportunities_contactspk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_con_opp_con',
            'type' => 'index',
            'fields' => array('contact_id')
        ),
        array(
            'name' => 'idx_con_opp_opp',
            'type' => 'index',
            'fields' => array('opportunity_id')
        ),
        array(
            'name' => 'idx_opportunities_contacts',
            'type' => 'alternate_key',
            'fields' => array('opportunity_id', 'contact_id')
        )
    ),
    'relationships' => array(
        'opportunities_contacts' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'opportunities_contacts',
            'join_key_lhs' => 'opportunity_id',
            'join_key_rhs' => 'contact_id'
        )
    )
);
