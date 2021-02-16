<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['TextMessageTemplate'] = [
    'table'   => 'textmessage_templates',
    'comment' => 'Templates used in text message processing',
    'fields' => [
        'published' => [
            'name'    => 'published',
            'vname'   => 'LBL_PUBLISHED',
            'type'    => 'varchar',
            'len'     => '3',
            'comment' => ''
        ],
        'body' => [
            'name'                => 'body',
            'vname'               => 'LBL_TEXTMESSAGE_BODY_PLAIN',
            'type'                => 'text',
            'comment'             => 'Plain text body to be used in resulting text message',
            'stylesheet_id_field' => 'style',
        ],
        'assigned_user_id' => [
            'name'            => 'assigned_user_id',
            'rname'           => 'user_name',
            'id_name'         => 'assigned_user_id',
            'vname'           => 'LBL_ASSIGNED_TO_ID',
            'group'           => 'assigned_user_name',
            'type'            => 'relate',
            'table'           => 'users',
            'module'          => 'Users',
            'reportable'      => true,
            'isnull'          => 'false',
            'dbType'          => 'id',
            'audited'         => true,
            'comment'         => 'User ID assigned to record',
            'duplicate_merge' => 'disabled',
        ],
        'assigned_user_name' => [
            'name'            => 'assigned_user_name',
            'link'            => 'assigned_user_link',
            'vname'           => 'LBL_ASSIGNED_TO',
            'rname'           => 'user_name',
            'type'            => 'relate',
            'reportable'      => false,
            'source'          => 'non-db',
            'table'           => 'users',
            'id_name'         => 'assigned_user_id',
            'module'          => 'Users',
            'duplicate_merge' => 'disabled',
        ],
        'assigned_user_link' => [
            'name'            => 'assigned_user_link',
            'type'            => 'link',
            'relationship'    => 'textmessagetemplates_assigned_user',
            'vname'           => 'LBL_ASSIGNED_TO',
            'link_type'       => 'one',
            'module'          => 'Users',
            'bean_name'       => 'User',
            'source'          => 'non-db',
            'duplicate_merge' => 'enabled',
            'rname'           => 'user_name',
            'id_name'         => 'assigned_user_id',
            'table'           => 'users',
        ],
        'language' => [
            'name'     => 'language',
            'vname'    => 'LBL_LANGUAGE',
            'type'     => 'language',
            'dbtype'   => 'varchar',
            'len'      => 10,
            'required' => true,
            'comment'  => 'Language used by the template',
        ],



//        'for_bean' => array(
//            'name' => 'for_bean',
//            'vname' => 'LBL_FOR_MODULE',
//            'type' => 'enum',
//            'required' => false,
//            'reportable' => false,
//            'options' => 'systemdeploymentpackage_repair_modules_dom'
//        ),
//        'type' => array(
//            'name' => 'type',
//            'vname' => 'LBL_TYPE',
//            'type' => 'enum',
//            'required' => false,
//            'reportable' => false,
//            'options' => 'emailTemplates_type_list',
//            'comment' => 'Type of the email template'
//        ),
    ],
    'indices' => [
        [
            'name'   => 'idx_textmessage_template_name',
            'type'   => 'index',
            'fields' => ['name'],
        ],



//        array(
//            'name' => 'idx_email_template_forbean',
//            'type' => 'index',
//            'fields' => array('for_bean')
//        ),
//        array(
//            'name' => 'idx_email_template_type',
//            'type' => 'index',
//            'fields' => array('type')
//        )
    ],
    'relationships' => [
        'textmessagetemplates_assigned_user' => [
            'lhs_module'        => 'Users',
            'lhs_table'         => 'users',
            'lhs_key'           => 'id',
            'rhs_module'        => 'TextMessageTemplates',
            'rhs_table'         => 'textmessage_templates',
            'rhs_key'           => 'assigned_user_id',
            'relationship_type' => 'one-to-many',
        ],
        //begin workaround maretval 2017-09-21  to terminate error when using default template in Vardefs
        'textmessagetemplates_modified_user' => [
            'lhs_module'        => 'Users',
            'lhs_table'         => 'users',
            'lhs_key'           => 'id',
            'rhs_module'        => 'TextMessageTemplates',
            'rhs_table'         => 'textmessage_templates',
            'rhs_key'           => 'modified_user_id',
            'relationship_type' => 'one-to-many',
        ],
        'textmessagetemplates_created_by' => [
            'lhs_module'        => 'Users',
            'lhs_table'         => 'users',
            'lhs_key'           => 'id',
            'rhs_module'        => 'TextMessageTemplates',
            'rhs_table'         => 'textmessage_templates',
            'rhs_key'           => 'created_by',
            'relationship_type' => 'one-to-many',
        ],
        //end
    ],
];
//BEGIN PHP7.1 compatibility: avoid PHP Fatal error:  Uncaught Error: Cannot use string offset as an array
global $dictionary;
//END
$dictionary['TextMessageTemplate']['relationships']['textmessagetemplates_textmessages'] = [
    'lhs_module'        => 'TextMessageTemplates',
    'lhs_table'         => 'textmessage_templates',
    'lhs_key'           => 'id',
    'rhs_module'        => 'TextMessages',
    'rhs_table'         => 'textmessages',
    'rhs_key'           => 'textmessagetemplate_id',
    'relationship_type' => 'one-to-many',
];

$dictionary['TextMessageTemplate']['fields']['textmessages'] = [
    'name'         => 'textmessages',
    'type'         => 'link',
    'relationship' => 'textmessagetemplates_textmessages',
    'source'       => 'non-db',
    'side'         => 'right',
    'vname'        => 'LBL_TEXTMESSAGETEMPLATES_TEXTMESSAGES_LINK',
];

VardefManager::createVardef('TextMessageTemplates', 'TextMessageTemplate', ['default', 'assignable']);
