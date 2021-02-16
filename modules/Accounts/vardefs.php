<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Account'] = array('table' => 'accounts', 'audited' => true, 'unified_search' => true, 'full_text_search' => true, 'unified_search_default_enabled' => true, 'duplicate_merge' => true,
    'comment' => 'Accounts are organizations or entities that are the target of selling, support, and marketing activities, or have already purchased products or services',
    'fields' => array(
        'parent_id' =>
            array(
                'name' => 'parent_id',
                'vname' => 'LBL_PARENT_ACCOUNT_ID',
                'type' => 'id',
                'required' => false,
                'reportable' => false,
                'audited' => true,
                'comment' => 'Account ID of the parent of this account',
            ),

        'sic_code' =>
            array(
                'name' => 'sic_code',
                'vname' => 'LBL_SIC_CODE',
                'type' => 'varchar',
                'len' => 10,
                'comment' => 'SIC code of the account',
            ),


        'parent_name' =>
            array(
                'name' => 'parent_name',
                'rname' => 'name',
                'id_name' => 'parent_id',
                'vname' => 'LBL_MEMBER_OF',
                'type' => 'relate',
                'isnull' => 'true',
                'module' => 'Accounts',
                'table' => 'accounts',
                'massupdate' => false,
                'source' => 'non-db',
                'len' => 36,
                'link' => 'member_of',
                'unified_search' => false,
                'importable' => 'true',
            ),


        'members' =>
            array(
                'name' => 'members',
                'type' => 'link',
                'relationship' => 'member_accounts',
                'module' => 'Accounts',
                'bean_name' => 'Account',
                'source' => 'non-db',
                'vname' => 'LBL_MEMBERS',
            ),
        'member_of' =>
            array(
                'name' => 'member_of',
                'type' => 'link',
                'relationship' => 'member_accounts',
                'module' => 'Accounts',
                'bean_name' => 'Account',
                'link_type' => 'one',
                'source' => 'non-db',
                'vname' => 'LBL_MEMBER_OF',
                'side' => 'right',
            ),
        'email_opt_out' =>
            array(
                'name' => 'email_opt_out',
                'vname' => 'LBL_EMAIL_OPT_OUT',
                'source' => 'non-db',
                'type' => 'bool',
                'massupdate' => false,
                'studio' => 'false',
            ),
        'invalid_email' =>
            array(
                'name' => 'invalid_email',
                'vname' => 'LBL_INVALID_EMAIL',
                'source' => 'non-db',
                'type' => 'bool',
                'massupdate' => false,
                'studio' => 'false',
            ),
// CR1000426 cleanup backend, module Cases removed
//        'cases' =>
//            array(
//                'name' => 'cases',
//                'type' => 'link',
//                'relationship' => 'account_cases',
//                'module' => 'Cases',
//                'bean_name' => 'aCase',
//                'source' => 'non-db',
//                'vname' => 'LBL_CASES',
//            ),
        //bug 42902
        'email' => array(
            'name' => 'email',
            'type' => 'email',
            'query_type' => 'default',
            'source' => 'non-db',
            'operator' => 'subquery',
            'subquery' => "SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE",
            'db_field' => array(
                'id',
            ),
            'vname' => 'LBL_ANY_EMAIL',
            'studio' => array('visible' => false, 'searchview' => true),
            'importable' => false,
        ),
        'tasks' =>
            array(
                'name' => 'tasks',
                'type' => 'link',
                'relationship' => 'account_tasks',
                'module' => 'Tasks',
                'bean_name' => 'Task',
                'source' => 'non-db',
                'vname' => 'LBL_TASKS',
            ),
        'notes' =>
            array(
                'name' => 'notes',
                'type' => 'link',
                'relationship' => 'account_notes',
                'module' => 'Notes',
                'bean_name' => 'Note',
                'source' => 'non-db',
                'vname' => 'LBL_NOTES',
            ),
        'meetings' =>
            array(
                'name' => 'meetings',
                'type' => 'link',
                'relationship' => 'account_meetings',
                'module' => 'Meetings',
                'bean_name' => 'Meeting',
                'source' => 'non-db',
                'vname' => 'LBL_MEETINGS',
            ),
        'calls' =>
            array(
                'name' => 'calls',
                'type' => 'link',
                'relationship' => 'account_calls',
                'module' => 'Calls',
                'bean_name' => 'Call',
                'source' => 'non-db',
                'vname' => 'LBL_CALLS',
            ),

        'emails' =>
            array(
                'name' => 'emails',
                'type' => 'link',
                'relationship' => 'emails_accounts_rel', /* reldef in emails */
                'module' => 'Emails',
                'bean_name' => 'Email',
                'source' => 'non-db',
                'vname' => 'LBL_EMAILS',
                'studio' => array("formula" => false),
            ),
        'documents' =>
            array(
                'name' => 'documents',
                'type' => 'link',
                'relationship' => 'documents_accounts',
                'source' => 'non-db',
                'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
            ),
// CR1000426 cleanup backend, module Bugs removed
//        'bugs' => array(
//            'name' => 'bugs',
//            'type' => 'link',
//            'relationship' => 'accounts_bugs',
//            'module' => 'Bugs',
//            'bean_name' => 'Bug',
//            'source' => 'non-db',
//            'vname' => 'LBL_BUGS',
//        ),
        'contacts' => array(
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'accounts_contacts',
            'module' => 'Contacts',
            'bean_name' => 'Contact',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS',
        ),
        'users' => array(
            'name' => 'users',
            'type' => 'link',
            'relationship' => 'accounts_users',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
            'rel_fields' => array(
                'user_role' => array(
                    'map' => 'account_user_role'
                )
            )
        ),
        'email_addresses' => array(
            'name' => 'email_addresses',
            'type' => 'link',
            'relationship' => 'accounts_email_addresses',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_ADDRESSES',
            'reportable' => false,
            'unified_search' => false,
            'rel_fields' => array('primary_address' => array('type' => 'bool')),
            'studio' => array("formula" => false),
        ),
        'email_addresses_primary' =>
            array(
                'name' => 'email_addresses_primary',
                'type' => 'link',
                'relationship' => 'accounts_email_addresses_primary',
                'source' => 'non-db',
                'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
                'duplicate_merge' => 'disabled',
                'studio' => array("formula" => false),
            ),
        'opportunities' =>
            array(
                'name' => 'opportunities',
                'type' => 'link',
                'relationship' => 'accounts_opportunities',
                'module' => 'Opportunities',
                'bean_name' => 'Opportunity',
                'source' => 'non-db',
                'vname' => 'LBL_OPPORTUNITY',
            ),


        'projects' =>
            array(
                'name' => 'projects',
                'type' => 'link',
                'relationship' => 'projects_accounts',
                'module' => 'Projects',
                'bean_name' => 'Project',
                'source' => 'non-db',
                'vname' => 'LBL_PROJECTS',
            ),
        'leads' =>
            array(
                'name' => 'leads',
                'type' => 'link',
                'relationship' => 'account_leads',
                'module' => 'Leads',
                'bean_name' => 'Lead',
                'source' => 'non-db',
                'vname' => 'LBL_LEADS',
            ),
        'campaigns' =>
            array(
                'name' => 'campaigns',
                'type' => 'link',
                'relationship' => 'account_campaign_log',
                'module' => 'CampaignLog',
                'bean_name' => 'CampaignLog',
                'source' => 'non-db',
                'vname' => 'LBL_CAMPAIGNLOG',
                'studio' => array("formula" => false),
            ),
        'campaign_accounts' =>
            array(
                'name' => 'campaign_accounts',
                'type' => 'link',
                'vname' => 'LBL_CAMPAIGNS',
                'relationship' => 'campaign_accounts',
                'source' => 'non-db',
            ),

        'created_by_link' =>
            array(
                'name' => 'created_by_link',
                'type' => 'link',
                'relationship' => 'accounts_created_by',
                'vname' => 'LBL_CREATED_BY_USER',
                'link_type' => 'one',
                'module' => 'Users',
                'bean_name' => 'User',
                'source' => 'non-db',
            ),
        'modified_user_link' =>
            array(
                'name' => 'modified_user_link',
                'type' => 'link',
                'relationship' => 'accounts_modified_user',
                'vname' => 'LBL_MODIFIED_BY_USER',
                'link_type' => 'one',
                'module' => 'Users',
                'bean_name' => 'User',
                'source' => 'non-db',
            ),
        'assigned_user_link' =>
            array(
                'name' => 'assigned_user_link',
                'type' => 'link',
                'relationship' => 'accounts_assigned_user',
                'vname' => 'LBL_ASSIGNED_TO_USER',
                'link_type' => 'one',
                'module' => 'Users',
                'bean_name' => 'User',
                'source' => 'non-db',
                'duplicate_merge' => 'enabled',
                'rname' => 'user_name',
                'id_name' => 'assigned_user_id',
                'table' => 'users',
            ),


        'campaign_id' =>
            array(
                'name' => 'campaign_id',
                'comment' => 'Campaign that generated Account',
                'vname' => 'LBL_CAMPAIGN_ID',
                'rname' => 'id',
                'id_name' => 'campaign_id',
                'type' => 'id',
                'table' => 'campaigns',
                'isnull' => 'true',
                'module' => 'Campaigns',
                'reportable' => false,
                'massupdate' => false,
                'duplicate_merge' => 'disabled',
            ),

        'campaign_name' =>
            array(
                'name' => 'campaign_name',
                'rname' => 'name',
                'vname' => 'LBL_CAMPAIGN',
                'type' => 'relate',
                'reportable' => false,
                'source' => 'non-db',
                'table' => 'campaigns',
                'id_name' => 'campaign_id',
                'link' => 'campaign_accounts',
                'module' => 'Campaigns',
                'duplicate_merge' => 'disabled',
                'comment' => 'The first campaign name for Account (Meta-data only)',
            ),

        'prospect_lists' =>
            array(
                'name' => 'prospect_lists',
                'type' => 'link',
                'relationship' => 'prospect_list_accounts',
                'module' => 'ProspectLists',
                'source' => 'non-db',
                'vname' => 'LBL_PROSPECT_LIST',
                'rel_fields' => [
                    'quantity' => [
                        'map' => 'prospectlists_accounts_quantity'
                    ]
                ]
            ),

        'proposals' => array(
            'name' => 'proposals',
            'vname' => 'LBL_PROPOSALS_LINK',
            'type' => 'link',
            'relationship' => 'accounts_proposals_rel',
            'link_type' => 'one',
            'source' => 'non-db',
        ),
        'ext_id' => array(
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => 50
        ),
        'vat_nr' => array(
            'name' => 'vat_nr',
            'vname' => 'LBL_VAT_NR',
            'type' => 'varchar',
            'len' => 50
        ),
        'vat_details' => array(
            'name' => 'vat_details',
            'vname' => 'LBL_VAT_DETAILS',
            'type' => 'text'
        ),
        'accountkpis' => array(
            'name' => 'accountkpis',
            'type' => 'link',
            'relationship' => 'accounts_accountkpis',
            'source' => 'non-db',
            'module' => 'AccountKPIs',
            'side' => 'right',
            'vname' => 'LBL_ACCOUNTKPIS'
        ),
        'accountbankaccounts' => array(
            'name' => 'accountbankaccounts',
            'type' => 'link',
            'relationship' => 'accounts_bankaccounts',
            'source' => 'non-db',
            'side' => 'right',
            'vname' => 'LBL_ACCOUNTBANKACCOUNTS'
        ),
        'accountccdetails' => array(
            'name' => 'accountccdetails',
            'vname' => 'LBL_ACOUNTCCDETAILS_LINK',
            'type' => 'link',
            'relationship' => 'accounts_accountccdetails',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
            'default' => true, //UI: load related beans on account load. module property required!
            'module' => 'AccountCCDetails'
        ),
        'servicecalls' => array(
            'vname' => 'LBL_SERVICECALLS',
            'name' => 'servicecalls',
            'type' => 'link',
            'module' => 'ServiceCalls',
            'relationship' => 'servicecalls_contacts',
            'source' => 'non-db'
        ),

        'events_account_role' => array(
            'name' => 'events_account_role',
            'vname' => 'LBL_ROLE',
            'type' => 'enum',
            'source' => 'non-db',
            'options' => 'events_account_roles_dom'
        ),
        'location_events' =>
            array(
                'name' => 'location_events',
                'type' => 'link',
                'relationship' => 'account_events',
                'module' => 'Events',
                'bean_name' => 'Event',
                'source' => 'non-db',
                'vname' => 'LBL_EVENT_LOCATION',
            ),
        'events' => array(
            'name' => 'events',
            'type' => 'link',
            'relationship' => 'events_accounts',
            'module' => 'Events',
            'bean_name' => 'Event',
            'source' => 'non-db',
            'vname' => 'LBL_EVENT',
            'rel_fields' => [
                'account_role' => [
                    'map' => 'events_account_role'
                ]
            ]
        ),
        'prospectlists_accounts_quantity' => array(
            'name' => 'prospectlists_accounts_quantity',
            'vname' => 'LBL_QUANTITY',
            'type' => 'varchar',
            'source' => 'non-db'
        ),
        'catalogorders' => [
            'name' => 'catalogorders',
            'type' => 'link',
            'module' => 'CatalogOrders',
            'relationship' => 'accounts_catalogorders',
            'source' => 'non-db'
        ],
        'inquiries' => [
            'name' => 'inquiries',
            'type' => 'link',
            'module' => 'Inquiries',
            'relationship' => 'account_inquiries',
            'source' => 'non-db'
        ],
        'spicetexts' => [
            'name' => 'spicetexts',
            'type' => 'link',
            'relationship' => 'accounts_spicetexts',
            'module' => 'SpiceTexts',
            'source' => 'non-db',
            'vname' => 'LBL_SPICE_TEXTS',
        ],
        'accountvatids' => [
            'name' => 'accountvatids',
            'type' => 'link',
            'relationship' => 'account_accountvatids',
            'rname' => 'vat_id',
            'source' => 'non-db',
            'module' => 'AccountVATIDs',
            'default' => true
        ],
    ),
    'indices' => array(
        array('name' => 'idx_accnt_id_del', 'type' => 'index', 'fields' => array('id', 'deleted')),
        array('name' => 'idx_accnt_name_del', 'type' => 'index', 'fields' => array('name', 'deleted')),//bug #5530
        array('name' => 'idx_accnt_assigned_del', 'type' => 'index', 'fields' => array('deleted', 'assigned_user_id')),
        array('name' => 'idx_accnt_parent_id', 'type' => 'index', 'fields' => array('parent_id')),
        array('name' => 'idx_accnt_ext_id_del', 'type' => 'index', 'fields' => array('ext_id', 'deleted')),
    ),
    'relationships' => array(
        'member_accounts' => array('lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
            'rhs_module' => 'Accounts', 'rhs_table' => 'accounts', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many'
        ),
// CR1000426 cleanup backend, module Cases removed
//        'account_cases' => array('lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
//            'rhs_module' => 'Cases', 'rhs_table' => 'cases', 'rhs_key' => 'account_id',
//            'relationship_type' => 'one-to-many'
//        ),
        'account_tasks' => array('lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
            'rhs_module' => 'Tasks', 'rhs_table' => 'tasks', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),
        'account_notes' => array('lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
            'rhs_module' => 'Notes', 'rhs_table' => 'notes', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),
        'account_meetings' => array('lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
            'rhs_module' => 'Meetings', 'rhs_table' => 'meetings', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),
        'account_calls' => array(
            'lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
            'rhs_module' => 'Calls', 'rhs_table' => 'calls', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),

        /*,'accounts_emails' => array(
            'rhs_module'        => 'Emails',
            'rhs_table'         => 'emails',
            'rhs_key'           => 'id',
            'lhs_module'        => 'Accounts',
            'lhs_table'         => 'accounts',
            'lhs_key'           => 'id',
            'relationship_type' => 'many-to-many',
            'join_table'        => 'emails_accounts',
            'join_key_rhs'      => 'email_id',
            'join_key_lhs'      => 'account_id'
        )
        */
        'account_emails' => array(
            'lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id', 'rhs_module' => 'Emails', 'rhs_table' => 'emails', 'rhs_key' => 'parent_id', 'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),
        'account_leads' => array(
            'lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id', 'rhs_module' => 'Leads', 'rhs_table' => 'leads', 'rhs_key' => 'account_id', 'relationship_type' => 'one-to-many'
        ),
        'accounts_assigned_user' => array(
            'lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Accounts', 'rhs_table' => 'accounts', 'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'accounts_modified_user' => array(
            'lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Accounts', 'rhs_table' => 'accounts', 'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'accounts_created_by' => array(
            'lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Accounts', 'rhs_table' => 'accounts', 'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ),
        'account_campaign_log' => array('lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
            'rhs_module' => 'CampaignLog', 'rhs_table' => 'campaign_log', 'rhs_key' => 'target_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'target_type', 'relationship_role_column_value' => 'Accounts'
        ),
        'accounts_accountkpis' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'AccountKPIs',
            'rhs_table' => 'accountkpis',
            'rhs_key' => 'account_id',
            'relationship_type' => 'one-to-many'
        ),
        'account_events' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Events',
            'rhs_table' => 'events',
            'rhs_key' => 'location_id',
            'relationship_role_column' => 'location_type',
            'relationship_role_column_value' => 'Accounts',
            'relationship_type' => 'one-to-many'
        ),
        'accounts_spicetexts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'SpiceTexts',
            'rhs_table' => 'spicetexts',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Accounts'
        ),
        'account_accountvatids' => array(
            'rhs_module' => 'AccountVATIDs',
            'rhs_table' => 'accountvatids',
            'rhs_key' => 'account_id',
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
            'default' => true
        )
    ),
    //This enables optimistic locking for Saves From EditView
    'optimistic_locking' => true,
);


// CE version has not all modules...
//set global else error with PHP7.1: Uncaught Error: Cannot use string offset as an array
global $dictionary;
if (is_file("modules/SalesDocs/SalesDoc.php")) {
    $dictionary['Account']['fields']['salesdocs'] = array(
        'name' => 'salesdocs',
        'type' => 'link',
        'vname' => 'LBL_SALESDOCS',
        'relationship' => 'salesdocs_accountsop',
        'module' => 'SalesDocs',
        'source' => 'non-db',
    );
    $dictionary['Account']['fields']['salesdocs_accountrp'] = array(
        'name' => 'salesdocs_accountrp',
        'type' => 'link',
        'vname' => 'LBL_SALESDOCS_AS_RP',
        'relationship' => 'salesdocs_accountsrp',
        'module' => 'SalesDocs',
        'source' => 'non-db',
        'comment' => 'SalesDocs as recipient party'
    );
    $dictionary['Account']['fields']['salesdocs_accountpp'] = array(
        'name' => 'salesdocs_accountpp',
        'type' => 'link',
        'vname' => 'LBL_SALESDOCS_AS_PP',
        'relationship' => 'salesdocs_accountspp',
        'module' => 'SalesDocs',
        'source' => 'non-db',
        'comment' => 'SalesDocs as payer'
    );
    $dictionary['Account']['fields']['salesdocs_accountir'] = array(
        'name' => 'salesdocs_accountir',
        'type' => 'link',
        'vname' => 'LBL_SALESDOCS_AS_IR',
        'relationship' => 'salesdocs_accountsir',
        'module' => 'SalesDocs',
        'source' => 'non-db',
        'comment' => 'SalesDocs as invoice recipient'
    );
}
if (is_file("modules/Addresses/Address.php")) {
    $dictionary['Account']['fields']['addresses'] = array(
        'name' => 'addresses',
        'type' => 'link',
        'relationship' => 'account_addresses',
        'source' => 'non-db',
        'vname' => 'LBL_ADDRESSES',
        'module' => 'Addresses',
        'default' => true
    );
}
if (is_file("modules/ServiceOrders/ServiceOrder.php")) {
    $dictionary['Account']['fields']['serviceorders'] = array(
        'name' => 'serviceorders',
        'type' => 'link',
        'relationship' => 'serviceorders_accounts',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICEORDERS',
        'module' => 'ServiceOrders',
        'default' => false
    );
    $dictionary['Account']['fields']['serviceorders_add'] = array(
        'name' => 'serviceorders_add',
        'type' => 'link',
        'relationship' => 'serviceorders_accounts_add',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICEORDERS_ADD',
        'module' => 'ServiceOrders',
        'default' => false
    );
    $dictionary['Account']['fields']['serviceorder_role'] = array(
        'name' => 'serviceorder_role',
        'type' => 'enum',
        'options' => 'serviceorders_accounts_roles_dom',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICEORDER_ROLE'
    );
}
if (is_file("modules/ServiceTickets/ServiceTicket.php")) {
    $dictionary['Account']['fields']['servicetickets'] = array(
        'name' => 'servicetickets',
        'type' => 'link',
        'relationship' => 'servicetickets_accounts',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICETICKETS',
        'module' => 'ServiceTickets',
        'default' => false
    );
}
if (is_file("modules/ServiceEquipments/ServiceEquipment.php")) {
    $dictionary['Account']['fields']['serviceequipments'] = array(
        'name' => 'serviceequipments',
        'type' => 'link',
        'relationship' => 'serviceequipments_accounts',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICEEQUIPMENTS',
        'module' => 'ServiceEquipments',
        'default' => false
    );
}
if (is_file("modules/ServiceLocations/ServiceLocation.php")) { // CR1000239
    $dictionary['Account']['fields']['servicelocations'] = array(
        'name' => 'servicelocations',
        'module' => 'ServiceLocations',
        'type' => 'link',
        'relationship' => 'servicelocation_accounts',
        'link_type' => 'one',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICELOCATIONS',
    );
}
if (is_file("modules/ProductVariants/ProductVariant.php")) {
    $dictionary['Account']['fields']['manufactures'] = array(
        'name' => 'manufactures',
        'vname' => 'LBL_PRODUCTVARIANT',
        'type' => 'link',
        'module' => 'ProductVariants',
        'relationship' => 'productvariant_manufacturer',
        'source' => 'non-db'
    );
    $dictionary['Account']['fields']['resells'] = array(
        'name' => 'resells',
        'vname' => 'LBL_PRODUCTVARIANT',
        'type' => 'link',
        'module' => 'ProductVariants',
        'relationship' => 'productvariants_resellers',
        'source' => 'non-db'
    );
}
VardefManager::createVardef('Accounts', 'Account', array('default', 'assignable', 'company'));

//jc - adding for refactor for import to not use the required_fields array
//defined in the field_arrays.php file
//BEGIN PHP7.1 compatibility: avoid PHP Fatal error:  Uncaught Error: Cannot use string offset as an array
global $dictionary;
//END
$dictionary['Account']['fields']['name']['importable'] = 'required';

// CR1000336
if(is_file('modules/SystemDeploymentReleases/SystemDeploymentRelease.php')){

    $dictionary['Account']['fields']['systemdeploymentreleases'] = array(
        'name' => 'systemdeploymentreleases',
        'type' => 'link',
        'relationship' => 'account_systemdeploymentreleases',
        'module' => 'SystemDeploymentReleases',
        'bean_name' => 'SystemDeploymentRelease',
        'source' => 'non-db',
        'vname' => 'LBL_SYSTEMDEPLOYMENTRELEASES',
    );
    $dictionary['Account']['relationships']['account_systemdeploymentreleases'] = array(
        'lhs_module' => 'Accounts', 'lhs_table' => 'accounts', 'lhs_key' => 'id',
        'rhs_module' => 'SystemDeploymentReleases', 'rhs_table' => 'systemdeploymentreleases', 'rhs_key' => 'parent_id',
        'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
        'relationship_role_column_value' => 'Accounts'
    );
}

if(is_file('modules/Potentials/Potential.php')){

    $dictionary['Account']['fields']['potentials'] = array(
        'name' => 'potentials',
        'vname' => 'LBL_POTENTIALS',
        'type' => 'link',
        'relationship' => 'account_potentials',
        'module' => 'Accounts',
        'source' => 'non-db'
    );
    $dictionary['Account']['fields']['resellerpotentials'] = array(
        'name' => 'resellerpotentials',
        'vname' => 'LBL_RESELLERPOTENTIALS',
        'type' => 'link',
        'relationship' => 'accounts_potentials_resellers',
        'module' => 'Accounts',
        'source' => 'non-db'
    );
    $dictionary['Account']['fields']['maincompetitorpotentials'] = array(
        'name' => 'maincompetitorpotentials',
        'vname' => 'LBL_MAINCOMPETITORPOTENTIALS',
        'type' => 'link',
        'relationship' => 'potential_maincompetitor',
        'module' => 'Accounts',
        'source' => 'non-db'
    );
    $dictionary['Account']['fields']['ompetitorpotentials'] = array(
        'name' => 'ompetitorpotentials',
        'vname' => 'LBL_COMPETITORPOTENTIALS',
        'type' => 'link',
        'relationship' => 'accounts_potentials_competitors',
        'module' => 'Accounts',
        'source' => 'non-db'
    );

}

if(is_file('modules/BonusCards/BonusCard.php')){
    $dictionary['Account']['fields']['bonuscards'] = [
        'name' => 'bonuscards',
        'type' => 'link',
        'relationship' => 'bonuscards_accounts',
        'module' => 'BonusCards',
        'bean_name' => 'BonusCard',
        'source' => 'non-db',
        'vname' => 'LBL_BONUSCARDS',
    ];
}

if(is_file('modules/Products/Product.php')){
    $dictionary['Account']['fields']['manufactured_products'] = array(
        'vname' => 'LBL_MANUFACTURED_PRODUCTS',
        'name' => 'manufactured_products',
        'type' => 'link',
        'module' => 'Products',
        'relationship' => 'manufacturer_products',
        'source' => 'non-db'
    );

    $dictionary['Account']['relationships']['manufacturer_products'] = array(
        'lhs_module' => 'Accounts',
        'lhs_table' => 'accounts',
        'lhs_key' => 'id',
        'rhs_module' => 'Products',
        'rhs_table' => 'products',
        'rhs_key' => 'manufacturer_id',
        'relationship_type' => 'one-to-many'
    );
}

if(is_file('modules/ServiceCalls/ServiceCall.php')){
    $dictionary['Account']['fields']['servicecalls'] = array(
        'vname' => 'LBL_SERVICECALLS',
        'name' => 'servicecalls',
        'type' => 'link',
        'module' => 'ServiceCalls',
        'relationship' => 'servicecalls_accounts',
        'source' => 'non-db'
    );
}
