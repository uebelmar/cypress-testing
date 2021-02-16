<?php



$dictionary['sysexchangemappingmodules'] = array(
    'table' => 'sysexchangemappingmodules',
    'comment' => 'List of modules that may be synced to exchange',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'comment' => 'Unique identifier'
        ),
        'sysmodule_id' => array(
            'name' => 'sysmodule_id',
            'vname' => 'LBL_SYSMODULE_ID',
            'type' => 'id',
            'required' => true,
            'comment' => 'id of the module that shall be syncable with exchange'
        ),
        'exchange_object' => array(
            'name' => 'exchange_object',
            'vname' => 'LBL_EXCHANGE_OBJECT',
            'type' => 'varchar',
            'len' => 25,
            'required' => true,
            'comment' => 'name of corresponding exchange object'
        ),
        'exchangesubscription' => array(
            'name' => 'exchangesubscription',
            'vname' => 'LBL_EXCHANGESUBSCRIPTION',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'if a subscription on the exchange server shoudl be established if the object is active'
        ),
        'outlookaddenabled' => array(
            'name' => 'outlookaddenabled',
            'vname' => 'LBL_OUTLOOKADDENABLED',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'set to ture if the object can be added in the outlook addin'
        ),
        'module_handler' => [
            'name'    => 'module_handler',
            'vname'   => 'LBL_MODULE_HANDLER',
            'type'    => 'varchar',
            'len'     => 255,
            'comment' => 'The name of the class handling the conversion between a Bean and the corresponding EWS object',
        ],
        'version' => [
            'name' => 'version',
            'type' => 'varchar',
            'len'  => 16,
        ],
        'package' => [
            'name' => 'package',
            'type' => 'varchar',
            'len'  => 32,
        ],
    )
);

$dictionary['sysexchangemappingsegmentitems'] = array(
    'table' => 'sysexchangemappingsegmentitems',
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
            'default' => 0,
        ),
        'active' => array(
            'name' => 'active',
            'vname' => 'LBL_ACTIVE',
            'type' => 'bool',
            'default' => 1,
        ),
        'inbound' => array(
            'name' => 'inbound',
            'vname' => 'LBL_INBOUND',
            'type' => 'bool',
            'default' => 1
        ),
        'outbound' => array(
            'name' => 'outbound',
            'vname' => 'LBL_OUTBOUND',
            'type' => 'bool',
            'default' => 1
        ),
        'segment_id' => array(
            'name' => 'segment_id',
            'vname' => 'LBL_SEGMENT_ID',
            'type' => 'id',
            'comment' => 'related top segment'
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_MAPPING_PARENT_ID',
            'type' => 'id',
            'comment' => 'id of parent id. Often named fields'
        ),
        'attribute_field' => array(
            'name' => 'attribute_field',
            'vname' => 'LBL_ATTRIBUTE',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'the key in metadata structure array '
        ),
        'value_field' => array(
            'name' => 'value_field',
            'vname' => 'LBL_VALUE_FIELD',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'the value for the key in metadata structure array '
        ),
        'version' => [
            'name' => 'version',
            'type' => 'varchar',
            'len'  => 16,
        ],
        'package' => [
            'name' => 'package',
            'type' => 'varchar',
            'len'  => 32,
        ],
    ),
    'indices' => array(
        array('name' => 'sysexchangemappingsegmentitemspk', 'type' => 'primary', 'fields' => array('id')),
    )
);

$dictionary['sysexchangemappingcustomsegmentitems'] = array(
    'table' => 'sysexchangemappingcustomsegmentitems',
    'fields' => $dictionary['sysexchangemappingsegmentitems']['fields'],
    'indices' => array(
        array('name' => 'sysexchangemappingcustomsegmentitemspk', 'type' => 'primary', 'fields' => array('id')),
    )
);


$i = 0;
$dictionary['sysexchangemappingsegments'] = array(
    'table' => 'sysexchangemappingsegments',
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
            'default' => 0,
        ),
        'active' => array(
            'name' => 'active',
            'vname' => 'LBL_ACTIVE',
            'type' => 'bool',
            'default' => 1,
        ),
        'exchange_segment' => array(
            'name' => 'exchange_segment',
            'vname' => 'LBL_SEGMENT',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'top level of the segment'
        ),
        'sysmodule_id' => array(
            'name' => 'sysmodule_id',
            'vname' => 'LBL_SYSMODULE_ID',
            'type' => 'id',
            'required' => true
        ),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text'
        ),
        'version' => [
            'name' => 'version',
            'type' => 'varchar',
            'len'  => 16,
        ],
        'package' => [
            'name' => 'package',
            'type' => 'varchar',
            'len'  => 32,
        ],
    ),
    'indices' => array(
        array('name' => 'sysexchangemappingsegmentspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sysexchangemappingsegments' . (++$i), 'type' => 'index', 'fields' => array('sysmodule_id')),
    )
);

$i = 0;
$dictionary['sysexchangemappingcustomsegments'] = array(
    'table' => 'sysexchangemappingcustomsegments',
    'fields' => $dictionary['sysexchangemappingsegments']['fields'],
    'indices' => array(
        array('name' => 'sysexchangemappingcustomsegmentspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sysexchangemappingcustomsegments' . (++$i), 'type' => 'index', 'fields' => array('sysmodule_id')),
    )
);

