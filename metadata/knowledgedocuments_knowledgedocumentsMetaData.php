<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['knowledgedocuments_knowledgedocuments'] = array(
    'table' => 'knowledgedocuments_knowledgedocuments',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'),
        'kbl_id' => array(
            'name' => 'kbl_id',
            'type' => 'varchar',
            'len' => '36'),
        'kbr_id' => array(
            'name' => 'kbr_id',
            'type' => 'varchar',
            'len' => '36'),
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
            'name' => 'accounts_contactspk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_kbl_id',
            'type' => 'index',
            'fields' => array('kbl_id')
        ),
        array(
            'name' => 'idx_kbr_id',
            'type' => 'index',
            'fields' => array('kbr_id')
        )

    ),
    'relationships' => array(
        'knowledgedocuments_knowledgedocuments' => array(
            'lhs_module' => 'KnowledgeDocuments',
            'lhs_table' => 'knowledgedocuments',
            'lhs_key' => 'id',
            'rhs_module' => 'KnowledgeDocuments',
            'rhs_table' => 'knowledgedocuments',
            'rhs_key' => 'id',
            'join_table' => 'knowledgedocuments_knowledgedocuments',
            'join_key_lhs' => 'kbl_id',
            'join_key_rhs' => 'kbr_id',
            'relationship_type' => 'many-to-many',
        )
    )
);
