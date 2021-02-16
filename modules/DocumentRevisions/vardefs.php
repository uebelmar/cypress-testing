<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['DocumentRevision'] = array(
    'table' => 'document_revisions',
    'audited' => true,
    'fields' => array(
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_name',
            'type' => 'varchar',
            'len' => '100',
            'required' => false,
        ],
        'change_log' => array(
            'name' => 'change_log',
            'vname' => 'LBL_LOGGED_CHANGES',
            'type' => 'varchar',
            'len' => '255',
        ),
        'document_id' => array(
            'name' => 'document_id',
            'vname' => 'LBL_DOCUMENT',
            'type' => 'varchar',
            'len' => '36',
            'required' => false,
            'reportable' => false,
        ),
        'file_name' => array(
            'name' => 'file_name',
            'vname' => 'LBL_FILENAME',
            'type' => 'file',
            'dbType' => 'varchar',
            'required' => true,
            'len' => '255'
        ),
        'file_ext' => array(
            'name' => 'file_ext',
            'vname' => 'LBL_FILE_EXTENSION',
            'type' => 'varchar',
            'len' => 100,
        ),
        'file_mime_type' => array(
            'name' => 'file_mime_type',
            'vname' => 'LBL_MIME',
            'type' => 'varchar',
            'len' => '100',
        ),
        'file_md5' => array(
            'name' => 'file_md5',
            'vname' => 'LBL_FILE_MD5',
            'type' => 'char',
            'len' => '32',
        ),
        'revision' => array(
            'name' => 'revision',
            'vname' => 'LBL_REVISION',
            'type' => 'int',
            'len' => 5,
        ),
        'documentrevisionstatus' => array(
            'name' => 'documentrevisionstatus',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 1,
            'options' => 'document_revisionstatus_dom',
            'default' => 'c'
        ),
        'documents' => array(
            'name' => 'documents',
            'type' => 'link',
            'relationship' => 'document_revisions',
            'source' => 'non-db',
            'vname' => 'LBL_REVISIONS',
        ),
        'latest_revision_id' => array(
            'name' => 'latest_revision_id',
            'vname' => 'LBL_REVISION',
            'type' => 'varchar',
            'len' => '36',
            'source' => 'non-db',
        ),
        'document_name' => array(
            'name' => 'document_name',
            'rname' => 'name',
            'id_name' => 'document_id',
            'vname' => 'LBL_DOCUMENT',
            'join_name' => 'documents',
            'type' => 'relate',
            'link' => 'documents',
            'table' => 'documents',
            'isnull' => 'true',
            'module' => 'Documents',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'massupdate' => false,
        ),
        'documents' => array(
            'name' => 'documents',
            'vname' => 'lbl_documents_link',
            'type' => 'link',
            'relationship' => 'document_revisions',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        'latest_revision' => array(
            'name' => 'latest_revision',
            'vname' => 'LBL_CURRENT_DOC_VERSION',
            'type' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
        )
    ),
    'relationships' => array(
        'revisions_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DocumentRevisions',
            'rhs_table' => 'document_revisions',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        )
    ),
    'indices' => array(
        array('name' => 'documentrevisionspk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'documentrevision_mimetype', 'type' => 'index', 'fields' => array('file_mime_type')),
    )
);

VardefManager::createVardef('DocumentRevisions', 'DocumentRevision', array('default', 'assignable'));
