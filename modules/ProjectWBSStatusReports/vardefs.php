<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProjectWBSStatusReport'] = [
    'table' => 'projectwbsstatusreports',
    'fields' => [
        'date_end' => [
            'name' => 'date_end',
            'vname' => 'LBL_DATE_END',
            'type' => 'date'
        ],
        'level_of_completion' => [
            'name' => 'level_of_completion',
            'type' => 'int',
            'dbtype' => 'double',
            'validation' => ['type' => 'range', 'min' => 0, 'max' => 100],
            'vname' => 'LBL_LEVEL_OF_COMPLETION',
        ],
        'new_date_end' => [
            'name' => 'new_date_end',
            'vname' => 'LBL_DATE_END',
            'type' => 'date',
            'required' => true
        ],
        'projectwbs_id' => [
            'name' => 'projectwbs_id',
            'vname' => 'LBL_PROJECTWBS_ID',
            'type' => 'id',
        ],
        'projectwbs_name' => [
            'name' => 'projectwbs_name',
            'vname' => 'LBL_PROJECTWBS',
            'type' => 'relate',
            'source' => 'non-db',
            'rname' => 'name',
            'id_name' => 'projectwbs_id',
            'len' => '255',
            'module' => 'ProjectWBSs',
            'link' => 'projectwbss',
            'join_name' => 'projectwbss',
        ],
        'projectwbss' => [
            'name' => 'projectwbss',
            'vname' => 'LBL_PROJECTWBSS',
            'type' => 'link',
            'module' => 'ProjectWBSs',
            'relationship' => 'projectwbs_projectwbsstatusreports',
            'source' => 'non-db',
        ]
    ],
    'indices' => [
        ['name' => 'idx_projectwbsstatusreports_wbsid', 'type' => 'index', 'fields' => ['projectwbs_id']],
        ['name' => 'idx_projectwbsstatusreports_wbsdel', 'type' => 'index', 'fields' => ['projectwbs_id', 'deleted']]
    ]];

VardefManager::createVardef('ProjectWBSStatusReports', 'ProjectWBSStatusReport', ['default', 'assignable']);

$dictionary['ProjectWBSStatusReport']['fields']['name']['required'] = false;
$dictionary['ProjectWBSStatusReport']['fields']['description']['required'] = true;
