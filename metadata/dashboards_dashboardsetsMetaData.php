<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['dashboards_dashboardsets'] = array(
    'table' => 'dashboards_dashboardsets',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'dashboard_id' => array(
            'name' => 'dashboard_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'dashboardset_id' => array(
            'name' => 'dashboardset_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'dashboard_sequence' => array (
            'name' => 'dashboard_sequence',
            'vname' => 'LBL_SORT_SEQUENCE',
            'type' => 'int',
            'default' => 99
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'required' => false,
            'default' => '0'
        )
    ),
    'indices' => array(
        array(
            'name' => 'idx_dashboards_dashboardsets_primary',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_dashboards_dashboardsets',
            'type' => 'alternate_key',
            'fields' => array('dashboard_id', 'dashboardset_id')
        )
    ),
    'relationships' => array(
        'dashboards_dashboardsets' => array(
            'lhs_module' => 'DashboardSets',
            'lhs_table' => 'dashboardsets',
            'lhs_key' => 'id',
            'rhs_module' => 'Dashboards',
            'rhs_table' => 'dashboards',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'events_contacts',
            'join_key_lhs' => 'dashboardset_id',
            'join_key_rhs' => 'dashboard_id'
        )
    )
);
