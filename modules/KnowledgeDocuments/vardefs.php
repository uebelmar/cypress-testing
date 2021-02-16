<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['KnowledgeDocument'] = array(
    'table' => 'knowledgedocuments',
    'comment' => 'KnowledgeDocuments Module',
    'audited' => false,
    'duplicate_merge' => false,
    'unified_search' => false,

    'fields' => array(
        'description' =>
            array(
                'name' => 'description',
                'vname' => 'LBL_HTML',
                'type' => 'html',
                'comment' => 'HTML formatted book content',
            ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'knowledge_status_dom',
            'importable' => 'required',
            'required' => true,
        ),
        'parent_sequence' => array(
            'name' => 'parent_sequence',
            'vname' => 'LBL_PARENT_SEQUENCE',
            'type' => 'int',
            'len' => '11',
            'reportable' => false,
            'default' => 0,
            'comment' => 'Sequence of the document in a book',
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
            'audited' => true,
            'comment' => 'ID of the parent of this Unit',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'rname' => 'name',
            'id_name' => 'parent_id',
            'vname' => 'LBL_MEMBER_OF',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'KnowledgeDocuments',
            'table' => 'knowledgedocuments',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'parent_link'
        ),
        'parent_link' => array(
            'name' => 'parent_link',
            'type' => 'link',
            'relationship' => 'parent_knowledgedocument',
            'module' => 'KnowledgeDocuments',
            'bean_name' => 'KnowledgeDocument',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_PARENT_LINK',
            'side' => 'right',
        ),
        'knowledgebook_id' => array(
            'name' => 'knowledgebook_id',
            'vname' => 'LBL_KNOWLEDGE_BOOK_ID',
            'type' => 'id',
        ),
        'knowledgebook_name' => array(
            'name' => 'knowledgebook_name',
            'rname' => 'name',
            'id_name' => 'knowledgebook_id',
            'vname' => 'LBL_KNOWLEDGE_BOOK',
            'type' => 'relate',
            'table' => 'knowledgebooks',
            'isnull' => 'true',
            'module' => 'KnowledgeBooks',
            'dbType' => 'varchar',
            'link' => 'knowledgebooks',
            'len' => 255,
            'source' => 'non-db'
        ),
        'knowledgebooks' => array(
            'name' => 'knowledgebooks',
            'type' => 'link',
            'relationship' => 'knowledgebooks_knowledgedocuments',
            'source' => 'non-db',
            'module' => 'KnowledgeBooks'
        ),
        'knowledgedocuments' => array(
            'name' => 'knowledgedocuments',
            'type' => 'link',
            'relationship' => 'knowledgedocuments_knowledgedocuments',
            'module' => 'KnowledgeDocuments',
            'bean_name' => 'KnowledgeDocument',
            'source' => 'non-db',
            'vname' => 'LBL_KNOWLEDGE_DOCUMENTS',
        ),
        'children' => array(
            'name' => 'children',
            'type' => 'link',
            'relationship' => 'knowledgedocuments_children',
            'module' => 'KnowledgeDocuments',
            'bean_name' => 'KnowledgeDocument',
            'source' => 'non-db',
            'vname' => 'LBL_CHILDREN',
            'side' => 'left',
        ),
        'breadcrumbs' => array(
            'name' => 'breadcrumbs',
            'type' => 'json',
            'source' => 'non-db'
        )
    ),
    'relationships' => array(
        'parent_knowledgedocument' => array(
            'lhs_module' => 'KnowledgeDocuments',
            'lhs_table' => 'knowledgedocuments',
            'lhs_key' => 'id',
            'rhs_module' => 'KnowledgeDocuments',
            'rhs_table' => 'knowledgedocuments',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
        ),
        'knowledgedocuments_children' => array(
            'lhs_module' => 'KnowledgeDocuments',
            'lhs_table' => 'knowledgedocuments',
            'lhs_key' => 'id',
            'rhs_module' => 'KnowledgeDocuments',
            'rhs_table' => 'knowledgedocuments',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
        ),

    ),
    'indices' => array(
        array(
            'name' => 'idx_knowledgedocument_id_del',
            'type' => 'index',
            'fields' => array('id', 'deleted')
        ),
        array(
            'name' => 'idx_knowledgedocument_parent_id',
            'type' => 'index',
            'fields' => array('parent_id')
        ),
        array(
            'name' => 'idx_knowledgedoc_uid',
            'type' => 'index',
            'fields' => array('knowledgebook_id')
        )
    )
);

VardefManager::createVardef('KnowledgeDocuments', 'KnowledgeDocument', array('default', 'assignable'));
