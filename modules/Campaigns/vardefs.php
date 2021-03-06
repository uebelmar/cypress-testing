<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Campaign'] = array('audited' => true,
    'comment' => 'Campaigns are a series of operations undertaken to accomplish a purpose, usually acquiring leads',
    'table' => 'campaigns',
    'unified_search' => true,
    'full_text_search' => true,
    'fields' => array(
//        'tracker_key' => array(
//            'name' => 'tracker_key',
//            'vname' => 'LBL_TRACKER_KEY',
//            'type' => 'int',
//            'required' => true,
//            'studio' => array('editview' => false),
//            'len' => '11',
//            'auto_increment' => true,
//            'comment' => 'The internal ID of the tracker used in a campaign; no longer used as of 4.2 (see campaign_trkrs)'
//        ),
//        'tracker_count' => array(
//            'name' => 'tracker_count',
//            'vname' => 'LBL_TRACKER_COUNT',
//            'type' => 'int',
//            'len' => '11',
//            'default' => '0',
//            'comment' => 'The number of accesses made to the tracker URL; no longer used as of 4.2 (see campaign_trkrs)'
//        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'dbType' => 'varchar',
            'type' => 'name',
            'len' => '50',
            'comment' => 'The name of the campaign',
            'importable' => 'required',
            'required' => true,
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
        ),
        'refer_url' => array(
            'name' => 'refer_url',
            'vname' => 'LBL_REFER_URL',
            'type' => 'varchar',
            'len' => '255',
            'default' => 'http://',
            'comment' => 'The URL referenced in the tracker URL; no longer used as of 4.2 (see campaign_trkrs)'
        ),
        'description' => array('name' => 'description', 'type' => 'none', 'comment' => 'inhertied but not used', 'source' => 'non-db'),
        'tracker_text' => array(
            'name' => 'tracker_text',
            'vname' => 'LBL_TRACKER_TEXT',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'The text that appears in the tracker URL; no longer used as of 4.2 (see campaign_trkrs)'
        ),

        'start_date' => array(
            'name' => 'start_date',
            'vname' => 'LBL_DATE_START',
            'type' => 'date',
            'audited' => true,
            'comment' => 'Starting date of the campaign',
            'validation' => array('type' => 'isbefore', 'compareto' => 'end_date'),
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        'end_date' => array(
            'name' => 'end_date',
            'vname' => 'LBL_DATE_END',
            'type' => 'date',
            'audited' => true,
            'comment' => 'Ending date of the campaign',
            'importable' => 'required',
            'required' => true,
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'campaign_status_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'Status of the campaign',
            'importable' => 'required',
            'required' => true,
        ),
        'impressions' => array(
            'name' => 'impressions',
            'vname' => 'LBL_CAMPAIGN_IMPRESSIONS',
            'type' => 'int',
            'default' => 0,
            'reportable' => true,
            'comment' => 'Expected Click throughs manually entered by Campaign Manager'
        ),
        'currency_id' =>
            array(
                'name' => 'currency_id',
                'vname' => 'LBL_CURRENCY',
                'type' => 'id',
                'group' => 'currency_id',
                'required' => false,
                'do_report' => false,
                'reportable' => false,
                'comment' => 'Currency in use for the campaign'
            ),
        'budget' => array(
            'name' => 'budget',
            'vname' => 'LBL_BUDGET',
            'type' => 'currency',
            'dbType' => 'double',
            'comment' => 'Budgeted amount for the campaign'
        ),
        'expected_cost' => array(
            'name' => 'expected_cost',
            'vname' => 'LBL_EXPECTED_COST',
            'type' => 'currency',
            'dbType' => 'double',
            'comment' => 'Expected cost of the campaign'
        ),
        'actual_cost' => array(
            'name' => 'actual_cost',
            'vname' => 'LBL_ACTUAL_COST',
            'type' => 'currency',
            'dbType' => 'double',
            'comment' => 'Actual cost of the campaign'
        ),
        'expected_revenue' => array(
            'name' => 'expected_revenue',
            'vname' => 'LBL_EXPECTED_REVENUE',
            'type' => 'currency',
            'dbType' => 'double',
            'comment' => 'Expected revenue stemming from the campaign'
        ),
        'campaign_type' => array(
            'name' => 'campaign_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'campaign_type_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'The type of campaign',
            'importable' => 'required',
            'required' => false,
        ),
        'objective' => array(
            'name' => 'objective',
            'vname' => 'LBL_OBJECTIVE',
            'type' => 'text',
            'comment' => 'The objective of the campaign'
        ),
        'content' => array(
            'name' => 'content',
            'vname' => 'LBL_CONTENT',
            'type' => 'text',
            'comment' => 'The campaign description'
        ),
        'prospectlists' => array(
            'name' => 'prospectlists',
            'vname' => 'LBL_PROSPECTLISTS',
            'type' => 'link',
            'relationship' => 'prospect_list_campaigns',
            'source' => 'non-db',
        ),
// CR1000465 cleanup Email
//        'emailmarketing' => array(
//            'name' => 'emailmarketing',
//            'type' => 'link',
//            'relationship' => 'campaign_email_marketing',
//            'source' => 'non-db',
//        ),
//        'queueitems' => array(
//            'name' => 'queueitems',
//            'type' => 'link',
//            'relationship' => 'campaign_emailman',
//            'source' => 'non-db',
//        ),
        'log_entries' => array(
            'name' => 'log_entries',
            'type' => 'link',
            'relationship' => 'campaign_campaignlog',
            'source' => 'non-db',
            'vname' => 'LBL_LOG_ENTRIES',
        ),
        'tracked_urls' => array(
            'name' => 'tracked_urls',
            'type' => 'link',
            'relationship' => 'campaign_campaigntrakers',
            'source' => 'non-db',
            'vname' => 'LBL_TRACKED_URLS',
        ),
        'frequency' => array(
            'name' => 'frequency',
            'vname' => 'LBL_FREQUENCY',
            'type' => 'enum',
            //'options' => 'campaign_status_dom',
            'len' => 100,
            'comment' => 'Frequency of the campaign',
            'options' => 'newsletter_frequency_dom',
            'len' => 100,
        ),
        'leads' => array(
            'name' => 'leads',
            'type' => 'link',
            'relationship' => 'campaign_leads',
            'source' => 'non-db',
            'vname' => 'LBL_LEADS',
            'link_class' => 'ProspectLink',
            'link_file' => 'modules/Campaigns/ProspectLink.php'
        ),
        'opportunities' => array(
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'campaign_opportunities',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITIES',
        ),
        'contacts' => array(
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'campaign_contacts',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS',
            'link_class' => 'ProspectLink',
            'link_file' => 'modules/Campaigns/ProspectLink.php'
        ),
        'consumers' => array(
            'name' => 'consumers',
            'type' => 'link',
            'relationship' => 'campaign_consumers',
            'source' => 'non-db',
            'vname' => 'LBL_CONSUMERS',
            'link_class' => 'ProspectLink',
            'link_file' => 'modules/Campaigns/ProspectLink.php'
        ),
        'accounts' => array(
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'campaign_accounts',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS',
            'link_class' => 'ProspectLink',
            'link_file' => 'modules/Campaigns/ProspectLink.php'
        ),

        // non db fields for campaign log
        'activity_type' => array(
            'name' => 'activity_type',
            'vname' => 'LBL_ACTIVITY',
            'type' => 'enum',
            'options' => 'campainglog_activity_type_dom',
            'len' => 100,
            'comment' => 'The activity that occurred (e.g., Viewed Message, Bounced, Opted out)',
            'source' => 'non-db'
        ),
        'activity_date' => array(
            'name' => 'activity_date',
            'vname' => 'LBL_DATE',
            'type' => 'datetime',
            'comment' => 'The date the activity occurred',
            'source' => 'non-db'
        ),
        'eventregistrations' => array(
            'name' => 'eventregistrations',
            'vname' => 'LBL_EVENTREGISTRATIONS',
            'type' => 'link',
            // 'relationship' => 'eventregistration_campaign_rel',
            'relationship' => 'eventregistration_campaign_rel',
            'module' => 'EventRegistrations',
            'source' => 'non-db',
        ),
        'mailrelais' => array(
            'name' => 'mailrelais',
            'vname' => 'LBL_MAILRELAIS',
            'type' => 'mailrelais',
            'dbType' => 'varchar',
            'len' => 36
        ),
        'email_template_name' =>
            array(
                'name' => 'email_template_name',
                'rname' => 'name',
                'id_name' => 'email_template_id',
                'vname' => 'LBL_EMAILTEMPLATE',
                'type' => 'relate',
                'table' => 'email_templates',
                'isnull' => 'true',
                'module' => 'EmailTemplates',
                'dbType' => 'varchar',
                'link' => 'emailtemplates',
                'len' => '255',
                'source' => 'non-db',
            ),
        'emailtemplates' => array(
            'name' => 'emailtemplates',
            'type' => 'link',
            'relationship' => 'campaign_email_template',
            'source' => 'non-db',
            'module' => 'EmailTemaplates'
        ),
        'email_template_id' => array(
            'name' => 'email_template_id',
            'vname' => 'LBL_MAILRELAIS',
            'dbType' => 'id',
            'type' => 'char',
            'len' => 36
        ),

        'mailbox_id' => array(
            'name' => 'mailbox_id',
            'vname' => 'LBL_MAILBOX_ID',
            'type' => 'id',
        ),

        'mailbox_name' => array(
            'name' => 'mailbox_name',
            'rname' => 'name',
            'id_name' => 'mailbox_id',
            'vname' => 'LBL_MAILBOXES_NAME',
            'join_name' => 'mailboxes_join',
            'type' => 'relate',
            'link' => 'mailbox',
            'table' => 'mailboxes',
            'isnull' => 'true',
            'module' => 'Mailboxes',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'massupdate' => false,
        ),

        'mailbox' => array(
            'name' => 'mailbox',
            'vname' => 'LBL_MAILBOX_LINK',
            'type' => 'link',
            'relationship' => 'campaigns_mailboxes_rel',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        'event_id' => array(
            'name' => 'event_id',
            'vname' => 'LBL_EVENT_ID',
            'type' => 'id',
        ),

        'event_name' => array(
            'name' => 'event_name',
            'rname' => 'name',
            'id_name' => 'event_id',
            'vname' => 'LBL_EVENT',
            'join_name' => 'events_join',
            'type' => 'relate',
            'link' => 'event',
            'table' => 'events',
            'isnull' => 'true',
            'module' => 'Events',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'massupdate' => false,
        ),

        'event' => array(
            'name' => 'event',
            'vname' => 'LBL_EVENT',
            'type' => 'link',
            'relationship' => 'events_campaigns',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        'campaigntasks' => array(
            'name' => 'campaigntasks',
            'type' => 'link',
            'relationship' => 'campaign_campaigntasks',
            'source' => 'non-db',
            'module' => 'CampaignTasks'
        )
    ),
    'indices' => array(
//        array(
//            'name' => 'camp_auto_tracker_key',
//            'type' => 'unique',
//            'fields' => array('tracker_key')
//        ),
        array(
            'name' => 'idx_campaign_name',
            'type' => 'index',
            'fields' => array('name')
        ),
    ),

    'relationships' => array(
        'campaigns_mailboxes_rel' => array(
            'lhs_module' => 'Mailboxes',
            'lhs_table' => 'mailboxes',
            'lhs_key' => 'id',
            'rhs_module' => 'Campaigns',
            'rhs_table' => 'campaigns',
            'rhs_key' => 'mailbox_id',
            'relationship_type' => 'one-to-many',
        ),

        'campaign_accounts' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
            'rhs_module' => 'Accounts', 'rhs_table' => 'accounts', 'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many'),

        'campaign_contacts' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
            'rhs_module' => 'Contacts', 'rhs_table' => 'contacts', 'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many'),

        'campaign_consumers' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
            'rhs_module' => 'Consumers', 'rhs_table' => 'consumers', 'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many'),

        'campaign_leads' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
            'rhs_module' => 'Leads', 'rhs_table' => 'leads', 'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many'),

        'campaign_prospects' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
            'rhs_module' => 'Prospects', 'rhs_table' => 'prospects', 'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many'),

        'campaign_opportunities' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
            'rhs_module' => 'Opportunities', 'rhs_table' => 'opportunities', 'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many'),

// CR1000465 cleanup Email
//        'campaign_email_marketing' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
//            'rhs_module' => 'EmailMarketing', 'rhs_table' => 'email_marketing', 'rhs_key' => 'campaign_id',
//            'relationship_type' => 'one-to-many'),
//        'campaign_emailman' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
//            'rhs_module' => 'EmailMan', 'rhs_table' => 'emailman', 'rhs_key' => 'campaign_id',
//            'relationship_type' => 'one-to-many'),

        'campaign_campaignlog' => array('lhs_module' => 'Campaigns', 'lhs_table' => 'campaigns', 'lhs_key' => 'id',
            'rhs_module' => 'CampaignLog', 'rhs_table' => 'campaign_log', 'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many'),

        'campaign_assigned_user' => array('lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Campaigns', 'rhs_table' => 'campaigns', 'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'),

        'campaign_modified_user' => array('lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Campaigns', 'rhs_table' => 'campaigns', 'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'),
        'campaign_email_template' => array('lhs_module' => 'EmailTemplates', 'lhs_table' => 'email_templates', 'lhs_key' => 'id',
            'rhs_module' => 'Campaigns', 'rhs_table' => 'campaigns', 'rhs_key' => 'email_template_id',
            'relationship_type' => 'one-to-many'),
    )
);
VardefManager::createVardef('Campaigns', 'Campaign', array('default', 'assignable',
));

