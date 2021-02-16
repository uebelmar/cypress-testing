<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Potential'] = array(
    'table' => 'potentials',
    'comment' => 'Potentiale',
    'audited' => true,
    'fields' => array(
        'is_key_account' => array(
            'name' => 'is_key_account',
            'vname' => 'LBL_IS_KEY_ACCOUNT',
            'type' => 'bool',
            'required' => false,
            'audited' => true,
            'comment' => 'Key Account',
        ),
        'is_target_customer' => array(
            'name' => 'is_target_customer',
            'vname' => 'LBL_IS_TARGET_CUSTOMER',
            'type' => 'bool',
            'required' => false,
            'audited' => true,
            'comment' => 'Zielkunde',
        ),
        'currency_id' => array(
            'name' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'type' => 'char',
            'len' => 36,
            'required' => true,
            'audited' => true,
            'comment' => 'Währung in der die monetären Felder eingegeben sind',
        ),
        'potential_value' => array(
            'name' => 'potential_value',
            'vname' => 'LBL_POTENTIAL_VALUE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'disable_num_format' => true,
            'currency_id' => 'currency_id',
            'len' => '26,6',
            'required' => false
        ),
        'strategic' => array(
            'name' => 'strategic',
            'vname' => 'LBL_STRATEGIC',
            'type' => 'bool',
            'default' => 0
        ),
        'self_potential_value' => array(
            'name' => 'self_potential_value',
            'vname' => 'LBL_SELF_POTENTIAL_VALUE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'disable_num_format' => true,
            'len' => '26,6',
            'required' => false,
            'audited' => true,
            'comment' => 'Eigenpotential (monetär)'
        ),
        'potential_captured' => array(
            'name' => 'potential_captured',
            'vname' => 'LBL_POTENTIAL_CAPTURED',
            'type' => 'currency',
            'dbType' => 'decimal',
            'disable_num_format' => true,
            'len' => '26,6',
            'source' => 'non-db'
        ),
        'competitor_potential' => array(
            'name' => 'competitor_potential',
            'vname' => 'LBL_COMPETITOR_POTENTIAL_VALUE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'disable_num_format' => true,
            'len' => '26,6',
            'required' => false,
            'audited' => true,
            'comment' => 'Preis Mitbewerber (monetär)'
        ),
        'comment' => array(
            'name' => 'comment',
            'vname' => 'LBL_COMMENT',
            'type' => 'text',
            'required' => false,
            'audited' => true,
            'comment' => 'Kommentar'
        ),
        'cultivate' => array(
            'name' => 'cultivate',
            'vname' => 'LBL_CULTIVATE_POTENTIAL',
            'type' => 'bool',
            'default' => false,
            'required' => false,
            'audited' => false,
            'comment' => 'Flag zur Potenzialpflege'
        ),
        'companycode_id' => array(
            'name' => 'companycode_id',
            'vname' => 'LBL_COMPANYCODE',
            'type' => 'companies',
            'dbType' => 'varchar',
            'len' => 36,
            'required' => false
        ),
        'companycode_name' => array(
            'name' => 'companycode_name',
            'rname' => 'name',
            'id_name' => 'companycode_id',
            'vname' => 'LBL_COMPANY',
            'type' => 'relate',
            'link' => 'companycode',
            'isnull' => 'true',
            'table' => 'companycodes',
            'module' => 'CompanyCodes',
            'source' => 'non-db',
        ),
        'companycode' => array(
            'name' => 'companycode',
            'type' => 'link',
            'vname' => 'LBL_COMPANYCODE',
            'relationship' => 'companycode_potentials',
            'source' => 'non-db',
        ),
        'account_id' => array(
            'name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_ID',
            'type' => 'id',
            'audited' => true
        ),
        'account' => array(
            'name' => 'account',
            'vname' => 'LBL_ACCOUNT',
            'type' => 'link',
            'relationship' => 'account_potentials',
            'module' => 'Accounts',
            'source' => 'non-db'
        ),
        'account_name' => array(
            'name' => 'account_name',
            'vname' => 'LBL_ACCOUNT',
            'rname' => 'name',
            'type' => 'relate',
            'link' => 'account',
            'id_name' => 'account_id',
            'module' => 'Accounts',
            'source' => 'non-db'
        ),
        'main_competitor_id' => array(
            'name' => 'main_competitor_id',
            'vname' => 'LBL_MAIN_COMPETITOR_ID',
            'type' => 'varchar',
            'len' => 36
        ),
        'main_competitor' => array(
            'name' => 'main_competitor',
            'vname' => 'LBL_MAIN_COMPETITOR',
            'type' => 'link',
            'relationship' => 'potential_maincompetitor',
            'module' => 'Accounts',
            'source' => 'non-db'
        ),
        'main_competitor_name' => array(
            'name' => 'main_competitor_name',
            'vname' => 'LBL_MAIN_COMPETITOR',
            'rname' => 'name',
            'type' => 'relate',
            'link' => 'main_competitor',
            'id_name' => 'main_competitor_id',
            'module' => 'Accounts',
            'source' => 'non-db'
        ),
        'main_contact_id' => array(
            'name' => 'main_contact_id',
            'vname' => 'LBL_MAIN_CONTACT_ID',
            'type' => 'id',
        ),
        'main_contact' => array(
            'name' => 'main_contact',
            'vname' => 'LBL_MAIN_CONTACT',
            'type' => 'link',
            'relationship' => 'contact_potentials',
            'module' => 'Contacts',
            'source' => 'non-db'
        ),
        'main_contact_name' => array(
            'name' => 'main_contact_name',
            'vname' => 'LBL_MAIN_CONTACT',
            'rname' => 'name',
            'type' => 'relate',
            'link' => 'main_contact',
            'id_name' => 'main_contact_id',
            'module' => 'Contacts',
            'source' => 'non-db'
        ),
        'contacts' => array(
            'name' => 'contacts',
            'vname' => 'LBL_CONTACTS',
            'type' => 'link',
            'module' => 'Contacts',
            'bean_name' => 'Contact',
            'relationship' => 'accounts_potentials_competitors',
            'source' => 'non-db',
            'default' => true
        ),
        'resellers' => array(
            'name' => 'resellers',
            'vname' => 'LBL_RESELLERS',
            'type' => 'link',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'relationship' => 'accounts_potentials_resellers',
            'source' => 'non-db',
            'default' => true
        ),
        'competitors' => array(
            'name' => 'competitors',
            'vname' => 'LBL_COMPETITORS',
            'type' => 'link',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'relationship' => 'accounts_potentials_competitors',
            'source' => 'non-db',
            'default' => true
        ),
        'opportunities' => array(
            'name' => 'opportunities',
            'module' => 'Opportunities',
            'type' => 'link',
            'vname' => 'LBL_POTENTIALS',
            'relationship' => 'potentials_opportunities',
            'source' => 'non-db',
        ),
        // added fields for the mapping of the relationship fields
        'opportunity_amount' => array(
            'name' => 'opportunity_amount',
            'vname' => 'LBL_AMOUNT',
            'type' => 'currency',
            'dbType' => 'double',
            'source' => 'non-db'
        ),
        'opportunity_amount_usdollar' => array(
            'name' => 'opportunity_amount_usdollar',
            'type' => 'currency',
            'dbType' => 'double',
            'source' => 'non-db'
        ),
        'leads' => array(
            'name' => 'leads',
            'module' => 'Leads',
            'type' => 'link',
            'vname' => 'LBL_LEADS',
            'relationship' => 'leads_potential',
            'source' => 'non-db',
        ),
        'productgroup_id' => array(
            'name' => 'productgroup_id',
            'vname' => 'LBL_PRODUCTGROUP_ID',
            'type' => 'id',
            'comment' => 'Eindeutige SugarID der Produktgruppe'
        ),
        'productgroup_name' => array(
            'name' => 'productgroup_name',
            'rname' => 'name',
            'id_name' => 'productgroup_id',
            'vname' => 'LBL_PRODUCTGROUP',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'ProductGroups',
            'table' => 'productgroups',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'productgroup',
            'unified_search' => true,
            'importable' => 'true',
        ),
        'productgroup' => array(
            'name' => 'productgroup',
            'vname' => 'LBL_PRODUCTGROUP',
            'type' => 'link',
            'relationship' => 'productgroup_potentials',
            'source' => 'non-db'
        )
    ),
    'relationships' => [
        'productgroup_potentials' => [
            'lhs_module' => 'ProductGroups',
            'lhs_table' => 'productgroups',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'productgroup_id',
            'relationship_type' => 'one-to-many',
        ],
        'contact_potentials' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'main_contact_id',
            'relationship_type' => 'one-to-many'
        ],
        'account_potentials' => [
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'account_id',
            'relationship_type' => 'one-to-many'
        ],
        'potential_maincompetitor' => [
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'main_competitor_id',
            'relationship_type' => 'one-to-many'
        ],
        'companycode_potentials' => [
            'lhs_module' => 'CompanyCodes',
            'lhs_table' => 'companycodes',
            'lhs_key' => 'id',
            'rhs_module' => 'Potentials',
            'rhs_table' => 'potentials',
            'rhs_key' => 'companycode_id',
            'relationship_type' => 'one-to-many'
        ]
    ],
    'indices' => [
        'idx_potentials_accid' => [
            'name' => 'idx_potentials_accid',
            'type' => 'index',
            'fields' => [
                'account_id',
                'deleted'
            ]
        ],
        'idx_potentials_accid_prodgrpid' => [
            'name' => 'idx_potentials_accid_prodgrpid',
            'type' => 'index',
            'fields' => [
                'account_id',
                'productgroup_id',
                'deleted'
            ]
        ],
        'idx_companycode_del' => [
            'name' => 'idx_companycode_del',
            'type' => 'index',
            'fields' => [
                'companycode_id',
                'deleted'
            ]
        ],
        'idx_companycode_prodg_del' => [
            'name' => 'idx_companycode_prodg_del',
            'type' => 'index',
            'fields' => [
                'companycode_id',
                'productgroup_id',
                'deleted'
            ]
        ]
    ]
);

VardefManager::createVardef('Potentials', 'Potential', array('default', 'assignable', 'activities'));
