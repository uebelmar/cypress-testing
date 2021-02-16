<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['potentials_opportunities'] = array(
    'table' => 'potentials_opportunities',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'potential_id' => array(
            'name' => 'potential_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'default' => '0'
        ),
        'amount' => array(
            'name' => 'amount',
            'type' => 'currency',
            'dbType' => 'double'
        ),
        'amount_usdollar' => array(
            'name' => 'amount_usdollar',
            'type' => 'currency',
            'dbType' => 'double',
        )
    ),
    'indices' => array(
        array(
            'name' => 'potentials_opportunitiesspk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_potential_opportunity',
            'type' => 'alternate_key',
            'fields' => array('potential_id', 'opportunity_id')
        ),
        array(
            'name' => 'idx_oppid_del_accid',
            'type' => 'index',
            'fields' => array('potential_id', 'deleted', 'opportunity_id')
        )
    ),
    'relationships' => array(
        'potentials_opportunities' => array(
            'lhs_module' => 'Potentials',
            'lhs_table' => 'potentials',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'potentials_opportunities',
            'join_key_lhs' => 'potential_id',
            'join_key_rhs' => 'opportunity_id'
        )
    )
);

