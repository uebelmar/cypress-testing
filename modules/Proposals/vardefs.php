<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Proposal'] = array(
    'table' => 'proposals',
    'comment' => 'Proposals Module',
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '32',
            'required' => true,
            'massupdate' => false,
            'comment' => 'proposal number'
        ),
        'proposalstatus' => array(
            'name' => 'proposalstatus',
            'type' => 'enum',
            'options' => 'proposalstatus_dom',
            'len' => '16',
            'vname' => 'LBL_STATUS',
            'massupdate' => false,
            'comment' => 'Status: draft|submitted|accepted|rejected'
        ),
        'amount' => array(
            'name' => 'amount',
            'vname' => 'LBL_AMOUNT',
            'type' => 'currency',
            'dbType' => 'double',
            'importable' => 'required',
            'duplicate_merge' => '1',
            'required' => true,
            'options' => 'numeric_range_search_dom',
            'enable_range_search' => true,
            'comment' => 'Unconverted amount of the opportunity',
        ),
        'currency_id' => array(
            'name' => 'currency_id',
            'type' => 'id',
            'group' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'reportable' => false,
            'comment' => 'Currency used for display purposes'
        ),
        'currency_name' => array(
            'name' => 'currency_name',
            'rname' => 'name',
            'id_name' => 'currency_id',
            'vname' => 'LBL_CURRENCY_NAME',
            'type' => 'relate',
            'isnull' => 'true',
            'table' => 'currencies',
            'module' => 'Currencies',
            'source' => 'non-db',
            'function' => array('name' => 'getCurrencyNameDropDown', 'returns' => 'html'),
            'studio' => 'false',
            'duplicate_merge' => 'disabled',
        ),
        'currency_symbol' => array(
            'name' => 'currency_symbol',
            'rname' => 'symbol',
            'id_name' => 'currency_id',
            'vname' => 'LBL_CURRENCY_SYMBOL',
            'type' => 'relate',
            'isnull' => 'true',
            'table' => 'currencies',
            'module' => 'Currencies',
            'source' => 'non-db',
            'function' => array('name' => 'getCurrencySymbolDropDown', 'returns' => 'html'),
            'studio' => 'false',
            'duplicate_merge' => 'disabled',
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'group' => 'parent_fields'
        ),
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'group' => 'parent_fields',
            'options' => 'parent_type_display',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'vname' => 'LBL_RELATED_TO',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'type' => 'parent',
            'group' => 'parent_fields',
            'source' => 'non-db',
            'options' => 'parent_type_display',
        ),
        'accounts' => array(
            'name' => 'accounts',
			'vname' => 'LBL_ACCOUNTS_LINK',
            'type' => 'link',
            'relationship' => 'accounts_proposals_rel',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'vname' => 'LBL_OPPORTUNITY_ID',
            'type' => 'id',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
        ),
        'opportunity_name' => array(
            'name' => 'opportunity_name',
            'rname' => 'name',
            'id_name' => 'opportunity_id',
            'vname' => 'LBL_OPPORTUNITY',
            'join_name' => 'opportunities',
            'type' => 'relate',
            'link' => 'opportunities',
            'table' => 'opportunities',
            'isnull' => 'true',
            'module' => 'Opportunities',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'massupdate' => false,
        ),
        'opportunities' => array(
            'name' => 'opportunities',
            'vname' => 'LBL_OPPORTUNITIES_LINK',
            'type' => 'link',
            'relationship' => 'opportunities_proposals_rel',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        // Files
        'file1name' => array(
            'name' => 'file1name',
            'vname' => 'LBL_FILE1NAME',
            'type' => 'varchar',
            'len' => '255',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'File name associated with the note (attachment)'
        ),
        'file1id' => array(
            'name' => 'file1id',
            'type' => 'id'
        ),
        'file1exists' => array(
            'name' => 'file1exists',
            'vname' => 'LBL_FILE1EXISTS',
            'type' => 'bool',
            'source' => 'non-db',
            'massupdate' => false,
        ),
        'file1link' => array(
            'name' => 'file1link',
            'vname' => 'LBL_FILE1EXISTS',
            'type' => 'varchar',
            'source' => 'non-db',
            'massupdate' => false,
        ),
        'file1_mime_type' => array(
            'name' => 'file1_mime_type',
            'vname' => 'LBL_FILE1_MIME_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'massupdate' => false,
            'comment' => 'Attachment MIME type'
        ),
        'file2name' => array(
            'name' => 'file2name',
            'vname' => 'LBL_FILE2NAME',
            'type' => 'varchar',
            'len' => '255',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'File name associated with the note (attachment)'
        ),
        'file2exists' => array(
            'name' => 'file2exists',
            'vname' => 'LBL_FILE2EXISTS',
            'type' => 'bool',
            'source' => 'non-db',
            'massupdate' => false,
        ),
        'file2link' => array(
            'name' => 'file2link',
            'vname' => 'LBL_FILE2EXISTS',
            'type' => 'varchar',
            'source' => 'non-db',
            'massupdate' => false,
        ),
        'file2id' => array(
            'name' => 'file2id',
            'type' => 'id'
        ),
        'file2_mime_type' => array(
            'name' => 'file2_mime_type',
            'vname' => 'LBL_FILE2_MIME_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'massupdate' => false,
            'comment' => 'Attachment MIME type'
        ),
        'file3name' => array(
            'name' => 'file3name',
            'vname' => 'LBL_FILE3NAME',
            'type' => 'varchar',
            'len' => '255',
            'reportable' => true,
            'massupdate' => false,
            'comment' => 'File name associated with the note (attachment)'
        ),
        'file3exists' => array(
            'name' => 'file3exists',
            'vname' => 'LBL_FILE3EXISTS',
            'type' => 'bool',
            'source' => 'non-db',
            'massupdate' => false,
        ),
        'file3link' => array(
            'name' => 'file3link',
            'vname' => 'LBL_FILE3EXISTS',
            'type' => 'varchar',
            'source' => 'non-db',
            'massupdate' => false,
        ),
        'file3id' => array(
            'name' => 'file3id',
            'type' => 'id'
        ),
        'file3_mime_type' => array(
            'name' => 'file3_mime_type',
            'vname' => 'LBL_FILE3_MIME_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'massupdate' => false,
            'comment' => 'Attachment MIME type'
        ),
        'proposal_notes_link' => array (
            'name' => 'proposal_notes_link',
            'type' => 'link',
            'relationship' => 'proposal_notes_rel',
            'source' => 'non-db',
            'vname' => 'LBL_PROPOSAL_NOTES_LINK'
        )
    ),
    'indices' => array(
        array(
            'name' => 'idx_pac',
            'type' => 'index',
            'fields' => array('parent_id'),
        ),
        array(
            'name' => 'idx_opp',
            'type' => 'index',
            'fields' => array('opportunity_id'),
        ),
        array(
            'name' => 'idx_paoppdel',
            'type' => 'index',
            'fields' => array('parent_id', 'opportunity_id', 'deleted'),
        ),
        array(
            'name' => 'idx_stadel',
            'type' => 'index',
            'fields' => array('proposalstatus', 'deleted'),
        ),
    ),
    'relationships' => array(
        'accounts_proposals_rel' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Proposals',
            'rhs_table' => 'proposals',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),
        'opportunities_proposals_rel' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Proposals',
            'rhs_table' => 'proposals',
            'rhs_key' => 'opportunity_id',
            'relationship_type' => 'one-to-many',
        ),
        'proposal_notes_rel' => array (
            'lhs_module' => 'Proposals',
            'lhs_table' => 'proposals',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type', 'relationship_role_column_value' => 'Proposals'
        ),
    )
);

VardefManager::createVardef('Proposals', 'Proposal', array('default', 'assignable'));
