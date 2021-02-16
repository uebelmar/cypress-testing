<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['prospect_list_campaigntasks'] = array(
    'table' => 'prospect_list_campaigntasks',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36',
        ),
        'prospect_list_id' => array(
            'name' => 'prospect_list_id',
            'type' => 'varchar',
            'len' => '36',
        ),
        'campaigntask_id' => array(
            'name' => 'campaigntask_id',
            'type' => 'varchar',
            'len' => '36',
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0'
        )
    ),
    'indices' => array(
        array(
            'name' => 'prospect_list_campaigntaksspk',
            'type' => 'primary',
            'fields' => array('id')
        )
    ),
    'relationships' => array(
        'prospect_list_campaigntasks' => array(
            'lhs_module' => 'ProspectLists',
            'lhs_table' => 'prospect_lists',
            'lhs_key' => 'id',
            'rhs_module' => 'CampaignTasks',
            'rhs_table' => 'campaigntasks',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'prospect_list_campaigntasks',
            'join_key_lhs' => 'prospect_list_id',
            'join_key_rhs' => 'campaigntask_id'
        )
    )
)

?>
