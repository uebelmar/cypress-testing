<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ProspectList'] = array(
    'table' => 'prospect_lists',
    'unified_search' => true,
    'full_text_search' => true,
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '50',
            'importable' => 'required',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
        ),
        'list_type' => array(
            'name' => 'list_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'prospect_list_type_dom',
            'len' => 100,
            'importable' => 'required',
        ),
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
        ),
        'modified_user_id' => array(
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED_BY',
            'type' => 'assigned_user_name',
            'table' => 'modified_user_id_users',
            'isnull' => 'false',
            'dbType' => 'id',
            'reportable' => true,
        ),
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED_BY',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
        ),
        'created_by' => array(
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'created_by',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'created_by_users',
            'isnull' => 'false',
            'dbType' => 'id'
        ),
        'created_by_name' => array(
            'name' => 'created_by_name',
            'vname' => 'LBL_CREATED',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'created_by',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
        ),
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_CREATED_BY',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
        ),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
        ),
        'domain_name' => array(
            'name' => 'domain_name',
            'vname' => 'LBL_DOMAIN_NAME',
            'type' => 'varchar',
            'len' => '255',
        ),
        'entry_count' => array(
            'name' => 'entry_count',
            'type' => 'int',
            'source' => 'non-db',
            'vname' => 'LBL_LIST_ENTRIES',
        ),
        'ext_id' => array (
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => '50'
        ),
        'attribute_id' => array (
            'name' => 'attribute_id',
            'type' => 'varchar',
            'len' => '50'
        ),
        'prospectlists_accounts_quantity' => array(
            'name' => 'prospectlists_accounts_quantity',
            'vname' => 'LBL_QUANTITY',
            'type' => 'varchar',
            'source' => 'non-db'
        ),
        'prospectlists_contacts_quantity' => array(
            'name' => 'prospectlists_contacts_quantity',
            'vname' => 'LBL_QUANTITY',
            'type' => 'varchar',
            'source' => 'non-db'
        ),
        'prospectlists_consumer_quantity' => array(
            'name' => 'prospectlists_consumer_quantity',
            'vname' => 'LBL_QUANTITY',
            'type' => 'varchar',
            'source' => 'non-db'
        ),
        'prospects' => array(
            'name' => 'prospects',
            'type' => 'link',
            'relationship' => 'prospect_list_prospects',
            'source' => 'non-db',
        ),
        'contacts' => array(
            'name' => 'contacts',
            'type' => 'link',
            'vname' => 'LBL_CONTACTS',
            'relationship' => 'prospect_list_contacts',
            'source' => 'non-db',
            'rel_fields' => [
                'quantity' => [
                    'map' => 'prospectlists_contacts_quantity'
                ]
            ]
        ),
        'consumers' => array(
            'name' => 'consumers',
            'type' => 'link',
            'vname' => 'LBL_CONSUMERS',
            'relationship' => 'prospect_list_consumers',
            'source' => 'non-db',
            'rel_fields' => [
                'quantity' => [
                    'map' => 'prospectlists_consumer_quantity'
                ]
            ]
        ),
        'leads' => array(
            'name' => 'leads',
            'type' => 'link',
            'vname' => 'LBL_LEADS',
            'relationship' => 'prospect_list_leads',
            'source' => 'non-db',
        ),
        'accounts' => array(
            'name' => 'accounts',
            'vname' => 'LBL_ACCOUNTS',
            'type' => 'link',
            'relationship' => 'prospect_list_accounts',
            'source' => 'non-db',
            'rel_fields' => [
                'quantity' => [
                    'map' => 'prospectlists_accounts_quantity'
                ]
            ]
        ),
        'campaigns' => array(
            'name' => 'campaigns',
            'type' => 'link',
            'vname' => 'LBL_CAMPAIGNS',
            'relationship' => 'prospect_list_campaigns',
            'source' => 'non-db',
        ),
        'campaigntasks' => array(
            'name' => 'campaigntasks',
            'type' => 'link',
            'vname' => 'LBL_CAMPAIGNTASKS',
            'relationship' => 'prospect_list_campaigntasks',
            'source' => 'non-db',
        ),
        'users' => array(
            'name' => 'users',
            'type' => 'link',
            'vname' => 'LBL_USERS',
            'relationship' => 'prospect_list_users',
            'source' => 'non-db',
        ),
// CR1000465 cleanup Email
//        'email_marketing' => array(
//            'name' => 'email_marketing',
//            'type' => 'link',
//            'relationship' => 'email_marketing_prospect_lists',
//            'source' => 'non-db',
//        ),
        'marketing_id' => array(
            'name' => 'marketing_id',
            'vname' => 'LBL_MARKETING_ID',
            'type' => 'varchar',
            'len' => '36',
            'source' => 'non-db',
        ),
        'marketing_name' => array(
            'name' => 'marketing_name',
            'vname' => 'LBL_MARKETING_NAME',
            'type' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
        ),
        'prospect_list_filters' => array(
            'name' => 'prospect_list_filters',
            'type' => 'link',
            'relationship' => 'prospectlists_prospect_list_filters',
            'source' => 'non-db',
            'module' => 'ProspectListFilters'
        )
    ),

    'indices' => array(
        array(
            'name' => 'idx_prospect_list_name',
            'type' => 'index',
            'fields' => array('name')
        ),
    ),
    'relationships' => array(
        'prospectlists_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'prospectlists',
            'rhs_table' => 'prospect_lists',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'prospectlists_prospect_list_filters' => array(
            'lhs_module' => 'ProspectLists',
            'lhs_table' => 'prospectlists',
            'lhs_key' => 'id',
            'rhs_module' => 'ProspectListFilters',
            'rhs_table' => 'prospect_list_filters',
            'rhs_key' => 'prospectlist_id',
            'relationship_type' => 'one-to-many'
        ),

    )
);

VardefManager::createVardef('ProspectLists', 'ProspectList', array('assignable', 'default'));
