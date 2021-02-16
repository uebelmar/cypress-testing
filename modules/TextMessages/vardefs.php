<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['TextMessage'] = [

    'table'   => 'textmessages',
    'comment' => 'Text Messages (SMS)',
    'fields'  => [
        'parent_type' => [
            'name'    => 'parent_type',
            'vname'   => 'LBL_PARENT_TYPE',
            'type'    =>'parent_type',
            'dbType'  => 'varchar',
            'group'   => 'parent_name',
            'options' => 'parent_type_display',
            'len'     => '255',
            'comment' => 'Sugar module the Text Message is associated with'
        ],
        'parent_name' => [
            'name'       => 'parent_name',
            'type'       => 'parent',
            'vname'      => 'LBL_RELATED_TO',
            'reportable' => false,
            'source'     => 'non-db',
        ],
        'parent_id' => [
            'name'       => 'parent_id',
            'vname'      => 'LBL_PARENT_ID',
            'type'       => 'id',
            'len'        => '36',
            'required'   => false,
            'reportable' => true,
            'comment'    => 'The ID of the Sugar item specified in parent_type'
        ],
        'description' => [
                'name'    => 'description',
                'vname'   => 'LBL_BODY',
                'type'    => 'text',
                'comment' => 'Full text of the Text Message'
        ],
        'mailbox_id' => [
            'name'       => 'mailbox_id',
            'vname'      => 'LBL_MAILBOX',
            'type'       => 'mailbox',
            'dbtype'     => 'varchar',
            'len'        => '36',
            'reportable' => false,
        ],
        'mailboxes' => [
            'name'            => 'mailboxes',
            'vname'           => 'LBL_MAILBOXES',
            'type'            => 'link',
            'relationship'    => 'mailboxes_textmessages_rel',
            'link_type'       => 'one',
            'source'          => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate'      => false,
            'module'          => 'Mailboxes',
            'bean_name'       => 'Mailbox',
        ],
        'mailbox_name' => [
            'name'           => 'mailbox_name',
            'rname'          => 'name',
            'id_name'        => 'mailbox_id',
            'vname'          => 'LBL_MAILBOXES',
            'type'           => 'relate',
            'table'          => 'mailboxes',
            'join_name'      => 'mailboxes',
            'isnull'         => 'true',
            'module'         => 'Mailboxes',
            'dbType'         => 'varchar',
            'link'           => 'mailboxes',
            'len'            => '255',
            'source'         => 'non-db',
            'unified_search' => true,
            'importable'     => 'required',
        ],
        'status' => [
            'name'    => 'status',
            'vname'   => 'LBL_STATUS',
            'type'    => 'enum',
            'len'     => 100,
            'options' => 'dom_textmessage_status',
        ],
        'delivery_status' => [
            'name'    => 'delivery_status',
            'vname'   => 'LBL_DELIVERY_STATUS',
            'type'    => 'enum',
            'len'     => 16,
            'options' => 'textmessage_delivery_status',
        ],
        'openness' => [
            'name'    => 'openness',
            'vname'   => 'LBL_OPENNESS',
            'type'    => 'enum',
            'len'     => 100,
            'options' => 'dom_textmessage_openness',
        ],
        'msisdn' => [
            'name'     => 'msisdn',
            'vname'    => 'LBL_MSISDN',
            'type'     => 'phone',
            'dbType'   => 'varchar',
            'len'      => 36,
            'required' => true,
            'comment'  => 'The phone number of the sender/recipient of the text message.'
        ],
        'direction' => [
            'name'     => 'direction',
            'vname'    => 'LBL_DIRECTION',
            'type'     => 'enum',
            'len'      => 1,
            'required' => true,
            'default'  => 'o',
            'options'  => 'textmessage_direction',
            'comment'  => 'The direction of the text message (inbound/outbound).',
        ],
        'contact_id' => [
            'name'       => 'contact_id',
            'vname'      => 'LBL_CONTACT_ID',
            'type'       => 'id',
            'required'   => false,
            'reportable' => false,
            'comment'    => 'Contact ID note is associated with'
        ],
        'contact' => [
            'name'         => 'contact',
            'type'         => 'link',
            'relationship' => 'contact_textmessages',
            'vname'        => 'LBL_LIST_CONTACT_NAME',
            'source'       => 'non-db',
        ],
        'contact_name' => [
            'name'             => 'contact_name',
            'rname'            => 'name',
            'id_name'          => 'contact_id',
            'vname'            => 'LBL_CONTACT',
            'table'            => 'contacts',
            'type'             => 'relate',
            'link'             => 'contact',
            'join_name'        => 'contacts',
            'isnull'           => 'true',
            'module'           => 'Contacts',
            'source'           => 'non-db',
            'db_concat_fields' => [
                0 => 'first_name',
                1 => 'last_name'
            ],
        ],
        'date_sent' => [
            'name'                => 'date_sent',
            'vname'               => 'LBL_DATE_SENT',
            'type'                => 'datetimecombo',
            'dbType'              => 'datetime',
            'comment'             => 'Date when the TextMessage was sent.',
            'importable'          => 'required',
            'required'            => true,
            'enable_range_search' => true,
            'options'             => 'date_range_search_dom',
        ],
        'to_be_sent' => [
            'name'    => 'to_be_sent',
            'vname'   => 'LBL_TO_BE_SENT',
            'source'  => 'non-db',
            'type'    => 'bool',
            'default' => false,
        ],
        'message_id' => [
            'name'    => 'message_id',
            'vname'   => 'LBL_MESSAGE_ID',
            'type'    => 'varchar',
            'len'     => 255,
            'comment' => 'ID of the text message item obtained from the SMS gateway.',
        ],
        'error_message' => [
            'name'    => 'error_message',
            'vname'   => 'LBL_ERROR_MESSAGE',
            'type'    => 'varchar',
            'len'     => 255,
            'comment' => 'Error message from the SMS gateway in case the sending failed.'
        ],
    ],
    'relationships' => [
        'mailboxes_textmessages_rel' => [
            'lhs_module'        => 'Mailboxes',
            'lhs_table'         => 'mailboxes',
            'lhs_key'           => 'id',
            'rhs_module'        => 'TextMessages',
            'rhs_table'         => 'textmessages',
            'rhs_key'           => 'mailbox_id',
            'relationship_type' => 'one-to-many',
        ],
        // todo parent relationship
    ],
    'indices' => [
        [
            'name'   => 'idx_textmessages_parent',
            'type'   => 'index',
            'fields' => ['parent_id', 'parent_type'],
        ],
        [
            'name'   => 'idx_textmessages_contact',
            'type'   => 'index',
            'fields' => ['contact_id'],
        ],
        [
            'name'   => 'idx_textmessages_date_start',
            'type'   => 'index',
            'fields' => ['date_sent'],
        ],
    ],
];

//BEGIN PHP7.1 compatibility: avoid PHP Fatal error:  Uncaught Error: Cannot use string offset as an array
global $dictionary;
//END
#create relationship to parent
$dictionary['TextMessage']['fields']['textmessagetemplate_id'] = [
    'name'  => 'textmessagetemplate_id',
    'type'  => 'id',
    'vname' => 'LBL_TEXTMESSAGETEMPLATE',
];

$dictionary['TextMessage']['fields']['textmessagetemplate_name'] = [
    'source'    => 'non-db',
    'name'      => 'textmessagetemplate_name',
    'vname'     => 'LBL_TEXTMESSAGETEMPLATE',
    'type'      => 'relate',
    'len'       => '255',
    'id_name'   => 'textmessagetemplate_id',
    'module'    => 'TextMessageTemplates',
    'link'      => 'textmessagetemplates_link',
    'join_name' => 'textmessagetemplates',
    'rname'     => 'name',
];

$dictionary['TextMessage']['fields']['textmessagetemplates_link'] = [
    'name'         => 'textmessagetemplates_link',
    'type'         => 'link',
    'relationship' => 'textmessagetemplates_textmessages',
    'link_type'    => 'one',
    'side'         => 'right',
    'source'       => 'non-db',
    'vname'        => 'LBL_TEXTMESSAGETEMPLATES_TEXTMESSAGES_LINK',
];

#create index
$dictionary['TextMessage']['indices']['textmessagetemplates_textmessages_textmessagetemplate_id'] = [
    'name'   => 'textmessagetemplates_textmessages_textmessagetemplate_id',
    'type'   => 'index',
    'fields' => ['textmessagetemplate_id'],
];

VardefManager::createVardef('TextMessages','TextMessage', ['assignable', 'default']);

$dictionary['TextMessage']['fields']['name']['required'] = false;
