<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Note'] = array(
    'table' => 'notes',
    'comment' => 'Notes and Attachments',
    'fields' => array(
        'file_mime_type' => array(
            'name' => 'file_mime_type',
            'vname' => 'LBL_FILE_MIME_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Attachment MIME type',
            'importable' => false
        ),
        'file_md5' => array(
            'name' => 'file_md5',
            'vname' => 'LBL_FILE_MD5',
            'type' => 'char',
            'len' => '32',
            'comment' => 'Attachment MD5'
        ),
        'file_name' => array(
            'name' => 'file_name',
            'vname' => 'LBL_FILENAME',
            'type' => 'file',
            'dbType' => 'varchar',
            'len' => '255',
            'comment' => 'File name associated with the note (attachment)'
        ),
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'group' => 'parent_name',
            'options' => 'parent_type_display',
            'len' => '255',
            'comment' => 'Sugar module the Note is associated with'
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => true,
            'comment' => 'The ID of the Sugar item specified in parent_type'
        ),
        'contact_id' => array(
            'name' => 'contact_id',
            'vname' => 'LBL_CONTACT_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
            'comment' => 'Contact ID note is associated with'
        ),
        'portal_flag' => array(
            'name' => 'portal_flag',
            'vname' => 'LBL_PORTAL_FLAG',
            'type' => 'bool',
            'required' => false,
            'comment' => 'Portal flag indicator determines if note created via portal'
        ),
        'embed_flag' => array(
            'name' => 'embed_flag',
            'vname' => 'LBL_EMBED_FLAG',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'Embed flag indicator determines if note embedded in email'
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id', 'vname' => 'LBL_RELATED_TO',
            'type' => 'parent',
            'source' => 'non-db',
            'options' => 'record_type_display_notes',
        ),
        'contact_name' => array(
            'name' => 'contact_name',
            'rname' => 'name',
            'id_name' => 'contact_id',
            'vname' => 'LBL_CONTACT',
            'table' => 'contacts',
            'type' => 'relate',
            'link' => 'contact',
            'join_name' => 'contacts',
            'db_concat_fields' => array(0 => 'first_name', 1 => 'last_name'),
            'isnull' => 'true',
            'module' => 'Contacts',
            'source' => 'non-db',
        ),
        'contact_phone' => array(
            'name' => 'contact_phone',
            'vname' => 'LBL_PHONE',
            'type' => 'phone',
            'vname' => 'LBL_PHONE',
            'source' => 'non-db'
        ),
        'contact_email' => array(
            'name' => 'contact_email',
            'type' => 'varchar',
            'vname' => 'LBL_EMAIL_ADDRESS',
            'source' => 'non-db',
            'studio' => false
        ),
        'account_id' => array(
            'name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_ID',
            'type' => 'id',
            'reportable' => false,
            'source' => 'non-db',
        ),
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'vname' => 'LBL_OPPORTUNITY_ID',
            'type' => 'id',
            'reportable' => false,
            'source' => 'non-db',
        ),
        'lead_id' => array(
            'name' => 'lead_id',
            'vname' => 'LBL_LEAD_ID',
            'type' => 'id',
            'reportable' => false,
            'source' => 'non-db',
        ),

        'contact' => array(
            'name' => 'contact',
            'type' => 'link',
            'relationship' => 'contact_notes',
            'vname' => 'LBL_LIST_CONTACT_NAME',
            'source' => 'non-db',
        ),
        'accounts' => array(
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'account_notes',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS',
        ),
        'opportunities' => array(
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'opportunity_notes',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITIES',
        ),
        'leads' => array(
            'name' => 'leads',
            'type' => 'link',
            'relationship' => 'lead_notes',
            'source' => 'non-db',
            'vname' => 'LBL_LEADS',
        ),
        'emails' => array(
            'name' => 'emails',
            'vname' => 'LBL_EMAILS',
            'type' => 'link',
            'relationship' => 'emails_notes_rel',
            'source' => 'non-db',
        ),
        'projects' => array(
            'name' => 'projects',
            'type' => 'link',
            'relationship' => 'projects_notes',
            'source' => 'non-db',
            'vname' => 'LBL_PROJECTS',
        ),
        'projectwbss' => [
            'name'         => 'projectwbss',
            'type'         => 'link',
            'relationship' => 'projectwbss_notes',
            'source'       => 'non-db',
            'vname'        => 'LBL_PROJECTWBSS',
        ],
        'meetings' => array(
            'name' => 'meetings',
            'type' => 'link',
            'relationship' => 'meetings_notes',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS',
        ),
        'calls' => array(
            'name' => 'calls',
            'type' => 'link',
            'relationship' => 'calls_notes',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS',
        ),
        'tasks' => array(
            'name' => 'tasks',
            'type' => 'link',
            'relationship' => 'tasks_notes',
            'source' => 'non-db',
            'vname' => 'LBL_TASKS',
        ),
    ),
    'relationships' => array(
        'notes_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'notes_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        )
    ),
    'indices' => array(
        array('name' => 'idx_note_name', 'type' => 'index', 'fields' => array('name')),
        array('name' => 'idx_notes_parent', 'type' => 'index', 'fields' => array('parent_id', 'parent_type')),
        array('name' => 'idx_note_contact', 'type' => 'index', 'fields' => array('contact_id')),
        array('name' => 'idx_notes_assigned_del', 'type' => 'index', 'fields' => array('deleted', 'assigned_user_id')),
    )
);

// CE version has not all modules...
//set global else error with PHP7.1: Uncaught Error: Cannot use string offset as an array
global $dictionary;
if (is_file("modules/ServiceTickets/ServiceTicket.php")) {
    $dictionary['Note']['fields']['servicetickets'] = array(
        'name' => 'servicetickets',
        'type' => 'link',
        'relationship' => 'servicetickets_notes',
        'module' => 'ServiceTickets',
        'bean_name' => 'ServiceTicket',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICETICKET',
    );
}
if (is_file("modules/ServiceOrders/ServiceOrder.php")) {
    $dictionary['Note']['fields']['serviceorders'] = array(
        'name' => 'serviceorders',
        'type' => 'link',
        'relationship' => 'serviceorders_notes',
        'module' => 'ServiceOrders',
        'bean_name' => 'ServiceOrder',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICEORDER',
    );
}

VardefManager::createVardef('Notes', 'Note', array('assignable', 'default'));
