<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['BonusProgram'] = [
    'table' => 'bonusprograms',
    'comment' => 'BonusPrograms Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,

	'fields' => array(
        'external_id' => [
            'name'    => 'external_id',
            'vname'   => 'LBL_EXTERNALID',
            'type'    => 'varchar',
            'len'     => 64
        ],
        'bonuscards' => [
            'name' => 'bonuscards',
            'type' => 'link',
            'relationship' => 'bonuscards_bonusprograms',
            'module' => 'BonusCards',
            'bean_name' => 'BonusCard',
            'source' => 'non-db',
            'vname' => 'LBL_BONUSCARD',
        ],
	),
	'relationships' => [],
	'indices' => [
        [
            'name' => 'idx_bonusprograms_external_id',
            'type'   => 'index',
            'fields' => ['external_id'],
        ]
    ]
];

VardefManager::createVardef('BonusPrograms', 'BonusProgram', ['default', 'assignable']);
