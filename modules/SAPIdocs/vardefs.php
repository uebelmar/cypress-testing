<?php

use SpiceCRM\includes\SugarObjects\VarDefManager;

$dictionary['SAPIdoc'] = array(
    'table' => 'sapidocs',
    'fields' => array(
        'refmes' => array(
            'name' => 'refmes',
            'type' => 'varchar',
            'len' => 16
        ),
        'idoctyp' => array(
            'name' => 'idoctyp',
            'type' => 'varchar',
            'len' => 50
        ),
        'mestyp' => array(
            'name' => 'mestyp',
            'type' => 'varchar',
            'len' => 50
        ),
        'sndpor' => array(
            'name' => 'sndpor',
            'type' => 'varchar',
            'len' => 50
        ),
        'sndprt' => array(
            'name' => 'sndprt',
            'type' => 'varchar',
            'len' => 50
        ),
        'sndprn' => array(
            'name' => 'sndprn',
            'type' => 'varchar',
            'len' => 50
        ),
        'rcvpor' => array(
            'name' => 'rcvpor',
            'type' => 'varchar',
            'len' => 50
        ),
        'rcvprt' => array(
            'name' => 'rcvprt',
            'type' => 'varchar',
            'len' => 50
        ),
        'rcvprn' => array(
            'name' => 'rcvprn',
            'type' => 'varchar',
            'len' => 50
        ),
        'idoc' => array(
            'name' => 'idoc',
            'type' => 'text',
            'dbType' => 'longtext'
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'audited' => true,
            'reportable' => true,
            'unified_search' => false,
            'len' => 25,
            'options' => 'sapidoc_status_list'
        ),
        'sap_status_code' => array(
            'name' => 'sap_status_code',
            'vname' => 'LBL_SAP_STATUS_CODE',
            'type' => 'int'
        ),
        'log' => array(
            'name' => 'log',
            'vname' => 'LBL_LOGGING',
            'type' => 'text'
        )
    ),
    'relationships' => array(
    ),
    'indices' => array(
        array('name' => 'sapidocs_pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_sapidocs_name', 'type' => 'index', 'fields' => array('name')),
        array('name' => 'idx_sapidocs_status_type', 'type' => 'index', 'fields' => array('status', 'mestyp')),
    )
);


VarDefManager::createVardef('SAPIdocs', 'SAPIdoc', array('default', 'assignable'));
