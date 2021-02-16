<?php


$i = 0;
$dictionary['SAPIdocSegmentRelations'] = array(
    'table' => 'sapidocsegmentrelations',
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
        'parent_segment_id' => array(
            'name' => 'parent_segment_id',
            'vname' => 'LBL_PARENT_SEGMENT_ID',
            'type' => 'id',
            'required' => false,
        ),
        'segment_id' => array(
            'name' => 'segment_id',
            'vname' => 'LBL_SEGMENT_ID',
            'type' => 'id',
            'required' => false,
        ),
        'segment_order' => array(
            'name' => 'segment_order',
            'vname' => 'LBL_SEGMENT_ORDER',
            'type' => 'int',
        ),
        'segment_function' => array(
            'name' => 'segment_function',
            'vname' => 'LBL_SEGMENT_FUNCTION',
            'type' => 'varchar',
            'len' => 255,
        ),
        'relationship_name' => array(
            'name' => 'relationship_name',
            'vname' => 'LBL_RELATIONSHIP_NAME',
            'type' => 'varchar',
            'len' => 255,
        ),
        'idoctyp' => array(
            'name' => 'idoctyp',
            'vname' => 'LBL_IDOCTYP',
            'type' => 'varchar',
            'required' => true,
        ),
        'mestyp' => array(
            'name' => 'mestyp',
            'vname' => 'LBL_MESTYP',
            'type' => 'varchar',
            'required' => true,
        ),
        'required_export' => array(
            'name' => 'required_export',
            'vname' => 'LBL_REQUIRED_EXPORT',
            'type' => 'bool',
        ),
    ),
    'indices' => array(
        array('name' => 'sapidocsegmentrelationspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocsegmentrelations' . ( ++$i), 'type' => 'index', 'fields' => array('parent_segment_id')),
        array('name' => 'idx_sapidocsegmentrelations' . ( ++$i), 'type' => 'index', 'fields' => array('segment_id')),
        array('name' => 'idx_sapidocsegmentrelations' . ( ++$i), 'type' => 'index', 'fields' => array('idoctyp')),
    ),
);

