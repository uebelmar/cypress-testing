<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Document'] = array('table' => 'documents',
    'audited' => true,
    'fields' => array(
        'file_name' => array(
            'name' => 'file_name',
            'vname' => 'LBL_FILENAME',
            'type' => 'file',
            'dbType' => 'varchar',
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
            'len' => 5
        ),
        'revision_date' => array(
            'name' => 'revision_date',
            'vname' => 'LBL_REVISION_DATE',
            'type' => 'datetime'
        ),
        'active_date' => array(
            'name' => 'active_date',
            'vname' => 'LBL_ACTIVE_DATE',
            'type' => 'date',
        ),
        'exp_date' => array(
            'name' => 'exp_date',
            'vname' => 'LBL_EXP_DATE',
            'type' => 'date',
        ),
        'category_id' => array(
            'name' => 'category_id',
            'vname' => 'LBL_CATEGORY',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_category_dom',
            'reportable' => true,
        ),
        'subcategory_id' => array(
            'name' => 'subcategory_id',
            'vname' => 'LBL_SUBCATEGORY',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_subcategory_dom',
            'reportable' => true,
        ),
        'status_id' => array(
            'name' => 'status_id',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_status_dom',
            'reportable' => false,
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'varchar',
            'source' => 'non-db',
            'comment' => 'Document status for Meta-Data framework',
        ),
        'document_revision_id' => array(
            'name' => 'document_revision_id',
            'vname' => 'LBL_LATEST_REVISION_ID',
            'type' => 'varchar',
            'len' => '36',
            'reportable' => false,
        ),
        'revisions' => array(
            'name' => 'revisions',
            'type' => 'link',
            'relationship' => 'document_revisions',
            'source' => 'non-db',
            'vname' => 'LBL_REVISIONS',
        ),
        'documentrevisions' => array(
            'name' => 'documentrevisions',
            'type' => 'link',
            'relationship' => 'document_revisions',
            'source' => 'non-db',
            'module' => 'DocumentRevisions',
            'side' => 'left',
            'vname' => 'LBL_REVISIONS',
        ),
        'contracts' => array(
            'name' => 'contracts',
            'type' => 'link',
            'relationship' => 'contracts_documents',
            'source' => 'non-db',
            'vname' => 'LBL_CONTRACTS',
        ),
        //todo remove
        'leads' => array(
            'name' => 'leads',
            'type' => 'link',
            'relationship' => 'leads_documents',
            'source' => 'non-db',
            'vname' => 'LBL_LEADS',
        ),
        'accounts' => array(
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'documents_accounts',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS_SUBPANEL_TITLE',
        ),
        'contacts' => array(
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'documents_contacts',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS_SUBPANEL_TITLE',
        ),
        'opportunities' => array(
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'documents_opportunities',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITIES_SUBPANEL_TITLE',
        ),
        'projects' => array(
            'name' => 'projects',
            'type' => 'link',
            'relationship' => 'documents_projects',
            'source' => 'non-db',
            'module' => 'Projects',
            'vname' => 'LBL_PROJECTS',
        ),
        'related_doc_id' => array(
            'name' => 'related_doc_id',
            'vname' => 'LBL_RELATED_DOCUMENT_ID',
            'reportable' => false,
            'dbType' => 'id',
            'type' => 'varchar',
            'len' => '36',
        ),
        'related_doc_name' => array(
            'name' => 'related_doc_name',
            'vname' => 'LBL_DET_RELATED_DOCUMENT',
            'type' => 'relate',
            'table' => 'documents',
            'id_name' => 'related_doc_id',
            'module' => 'Documents',
            'source' => 'non-db',
            'comment' => 'The related document name for Meta-Data framework',
        ),
        'related_doc_rev_id' => array(
            'name' => 'related_doc_rev_id',
            'vname' => 'LBL_RELATED_DOCUMENT_REVISION_ID',
            'reportable' => false,
            'dbType' => 'id',
            'type' => 'varchar',
            'len' => '36',
        ),
        'related_doc_rev_number' => array(
            'name' => 'related_doc_rev_number',
            'vname' => 'LBL_DET_RELATED_DOCUMENT_VERSION',
            'type' => 'varchar',
            'source' => 'non-db',
            'comment' => 'The related document version number for Meta-Data framework',
        ),
        'is_template' => array(
            'name' => 'is_template',
            'vname' => 'LBL_IS_TEMPLATE',
            'type' => 'bool',
            'default' => 0,
            'reportable' => false,
        ),
        'template_type' => array(
            'name' => 'template_type',
            'vname' => 'LBL_TEMPLATE_TYPE',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_template_type_dom',
            'reportable' => false,
        ),
        'latest_revision_name' => array(
            'name' => 'latest_revision_name',
            'vname' => 'LBL_LASTEST_REVISION_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'selected_revision_name' => array(
            'name' => 'selected_revision_name',
            'vname' => 'LBL_SELECTED_REVISION_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'contract_status' => array(
            'name' => 'contract_status',
            'vname' => 'LBL_CONTRACT_STATUS',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'contract_name' => array(
            'name' => 'contract_name',
            'vname' => 'LBL_CONTRACT_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'linked_id' => array(
            'name' => 'linked_id',
            'vname' => 'LBL_LINKED_ID',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'selected_revision_id' => array(
            'name' => 'selected_revision_id',
            'vname' => 'LBL_SELECTED_REVISION_ID',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'latest_revision_id' => array(
            'name' => 'latest_revision_id',
            'vname' => 'LBL_LATEST_REVISION_ID',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'selected_revision_filename' => array(
            'name' => 'selected_revision_filename',
            'vname' => 'LBL_SELECTED_REVISION_FILENAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db'
        ),
        'parent_id' => [
            'name'       => 'parent_id',
            'vname'      => 'LBL_PARENT_ID',
            'type'       => 'id'
        ],
        'parent_type' => [
            'name'     => 'parent_type',
            'vname'    => 'LBL_PARENT_TYPE',
            'type'     => 'parent_type',
            'dbType'   => 'varchar',
            'required' => false,
            'options'  => 'parent_type_display',
            'len'      => 255,
        ],
        'parent_name' => [
            'name'        => 'parent_name',
            'type_name'   => 'parent_type',
            'id_name'     => 'parent_id',
            'vname'       => 'LBL_RELATED_TO',
            'type'        => 'parent',
            'source'      => 'non-db'
        ],
    ),
    'indices' => array(
        array('name' => 'idx_doc_cat', 'type' => 'index', 'fields' => array('category_id', 'subcategory_id')),
    ),
    'relationships' => array(
        'document_revisions' => array(
            'lhs_module' => 'Documents',
            'lhs_table' => 'documents',
            'lhs_key' => 'id',
            'rhs_module' => 'DocumentRevisions',
            'rhs_table' => 'document_revisions',
            'rhs_key' => 'document_id',
            'relationship_type' => 'one-to-many'
        ),
        'documents_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Documents',
            'rhs_table' => 'documents',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'documents_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Documents',
            'rhs_table' => 'documents',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        )
    )
);

VardefManager::createVardef('Documents', 'Document', array('default', 'assignable'));
