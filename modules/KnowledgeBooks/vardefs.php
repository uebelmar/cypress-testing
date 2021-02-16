<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['KnowledgeBook'] = [
    'table'           => 'knowledgebooks',
    'comment'         => 'KnowledgeBooks Module',
    'audited'         =>  false,
    'duplicate_merge' =>  false,
    'unified_search'  =>  false,

	'fields' => [
        'html' => [
                'name'    => 'html',
                'vname'   => 'LBL_HTML',
                'type'    => 'html',
                'comment' => 'HTML formatted book content',
         ],
        'status' => [
            'name' => 'status',
            'vname'      => 'LBL_STATUS',
            'type'       => 'enum',
            'len'        => 100,
            'options'    => 'knowledge_status_dom',
            'importable' => 'required',
            'required'   => true,
        ],
        'knowledgedocuments' => [
            'name' => 'knowledgedocuments',
            'type' => 'link',
            'relationship' => 'knowledgebooks_knowledgedocuments',
            'source' => 'non-db',
            'module' => 'KnowledgeDocuments'
        ],
        'public' => [
            'name'    => 'public',
            'vname'      => 'LBL_PUBLIC',
            'type'    => 'bool',
            'default' => '0',
        ],
	],
	'relationships' => [
        'knowledgebooks_knowledgedocuments' => [
            'lhs_module' => 'KnowledgeBooks',
            'lhs_table' => 'knowledgebooks',
            'lhs_key' => 'id',
            'rhs_module' => 'KnowledgeDocuments',
            'rhs_table' => 'knowledgedocuments',
            'rhs_key' => 'knowledgebook_id',
            'relationship_type' => 'one-to-many',
        ],
	],
	'indices' => []
];

VardefManager::createVardef('KnowledgeBooks', 'KnowledgeBook', ['default', 'assignable']);
