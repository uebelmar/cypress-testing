<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['KnowledgeDocumentAccessLog'] = [
    'table'   => 'knowledgedocumentaccesslogs',
    'comment' => 'Knowledge Document Access Log Module',
    'fields'  => [
        'knowledgedocument_id' => [
            'name'     => 'knowledgedocument_id',
            'vname'    => 'LBL_KNOWLEDGEDOCUMENT',
            'type'     => 'id',
            'len'      => '36',
            'required' => true,
        ],
        'knowledgedocuments' => [
            'name'            => 'knowledgedocuments',
            'vname'           => 'LBL_KNOWLEDGEDOCUMENTS',
            'type'            => 'link',
            'relationship'    => 'knowledgedocumentaccesslogs_knowledgedocuments_rel',
            'link_type'       => 'one',
            'source'          => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate'      => false,
            'module'          => 'KnowledgeDocuments',
            'bean_name'       => 'KnowledgeDocument',
        ],
        'knowledgedocument_name' => [
            'name'           => 'knowledgedocument_name',
            'rname'          => 'name',
            'id_name'        => 'knowledgedocument_id',
            'vname'          => 'LBL_KNOWLEDGEDOCUMENTS',
            'type'           => 'relate',
            'table'          => 'knowledgedocuments',
            'join_name'      => 'knowledgedocuments',
            'isnull'         => 'true',
            'module'         => 'KnowledgeDocuments',
            'dbType'         => 'varchar',
            'link'           => 'knowledgedocuments',
            'len'            => '255',
            'source'         => 'non-db',
            'unified_search' => true,
            'importable'     => 'required',
        ],
        'stat_date' => [
            'name' => 'stat_date',
            'type' => 'date',
        ],
        'counter' => [
            'name' => 'counter',
            'type' => 'int',
            'len'  => '8',
        ],
    ],
    'relationships' => [
        'knowledgedocumentaccesslogs_knowledgedocuments_rel' => [
            'lhs_module'        => 'KnowledgeDocuments',
            'lhs_table'         => 'knowledgedocuments',
            'lhs_key'           => 'id',
            'rhs_module'        => 'KnowledgeDocumentAccessLogs',
            'rhs_table'         => 'knowledgedocumentaccesslogs',
            'rhs_key'           => 'knowledgedocument_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'indices' => []
];

VardefManager::createVardef(
    'KnowledgeDocumentAccessLogs',
    'KnowledgeDocumentAccessLog',
    ['default', 'assignable']
);
