<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['BonusCard'] = array(
    'table' => 'bonuscards',
    'comment' => 'BonusCards Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,

	'fields' => array(
        'first_name' => [
            'name' => 'first_name',
            'vname' => 'LBL_FIRST_NAME',
            'type' => 'varchar',
            'len' => '100',
        ],
        'last_name' => [
            'name' => 'last_name',
            'vname' => 'LBL_LAST_NAME',
            'type' => 'varchar',
            'len' => '100',
        ],
        'gdpr_data_agreement' => [
            'name' => 'gdpr_data_agreement',
            'vname' => 'LBL_GDPR_DATA_AGREEMENT',
            'type' => 'bool',
            'default' => false,
            'comment' => 'GDPR(General Data Protection Rule) - DSGVO Datenverarbeitungs Zustimmung'
        ],
        'gdpr_marketing_agreement' => [
            'name' => 'gdpr_marketing_agreement',
            'vname' => 'LBL_GDPR_MARKETING_AGREEMENT',
            'type' => 'bool',
            'default' => false,
            'comment' => 'GDPR(General Data Protection Rule) - DSGVO Marketing Zustimmung'
        ],
        'gdpr_data_source' => array(
            'name' => 'gdpr_data_source',
            'vname' => 'LBL_GDPR_DATA_SOURCE',
            'type' => 'text',
            'audited' => true,
        ),
        'gdpr_marketing_source' => array(
            'name' => 'gdpr_marketing_source',
            'vname' => 'LBL_GDPR_MARKETING_SOURCE',
            'type' => 'text',
            'audited' => true,
        ),
        'link' => [
            'name' => 'link',
            'vname' => 'LBL_LINK',
            'type' => 'varchar',
        ],
        'purchase_date' => [
            'name' => 'purchase_date',
            'vname' => 'LBL_PURCHASE_DATE',
            'type' => 'date',
        ],
        'valid_until' => [
            'name' => 'valid_until',
            'vname' => 'LBL_VALID_UNTIL',
            'type' => 'date',
        ],
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'group' => 'parent_name'
        ),
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'group' => 'parent_name',
            'options' => 'parent_type_display',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'vname' => 'LBL_RELATED_TO',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'type' => 'parent',
            'group' => 'parent_name',
            'source' => 'non-db',
            'options' => 'parent_type_display',
        ),
        'bonusprogram_id' => array(
            'name' => 'bonusprogram_id',
            'vname' => 'LBL_BONUSPROGRAM_ID',
            'type' => 'varchar',
            'len' => 36,
            'required' => true
        ),
        'bonusprogram_name' => array(
            'name' => 'bonusprogram_name',
            'vname' => 'LBL_BONUSPROGRAM',
            'rname' => 'name',
            'id_name' => 'bonusprogram_id',
            'type' => 'relate',
            'table' => 'bonusprograms',
            'module' => 'BonusPrograms',
            'dbType' => 'varchar',
            'link' => 'bonusprogram',
            'len' => '255',
            'source' => 'non-db',
            'required' => true
        ),
        'bonusprograms' => [
            'name' => 'bonusprograms',
            'vname' => 'LBL_BONUSPROGRAM',
            'type' => 'link',
            'relationship' => 'bonuscards_bonusprograms',
            'source' => 'non-db',
            'default' => true,
        ],
        'old_card_id' => array(
            'name' => 'old_card_id',
            'type' => 'varchar',
            'len' => 36,
        ),
        'old_card_name' => array(
            'name' => 'old_card_name',
            'vname' => 'LBL_OLD_CARD',
            'rname' => 'name',
            'id_name' => 'old_card_id',
            'type' => 'relate',
            'table' => 'bonuscards',
            'module' => 'BonusCards',
            'dbType' => 'varchar',
            'link' => 'old_card_link',
            'len' => '255',
            'source' => 'non-db',
        ),
        'old_card_link' => array(
            'name' => 'old_card_link',
            'type' => 'link',
            'relationship' => 'oldcard_bonuscards',
            'module' => 'BonusCards',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_OLD_CARDS',
            'side' => 'right',
        ),
        'children' => array(
            'name' => 'children',
            'type' => 'link',
            'relationship' => 'bonuscard_children',
            'module' => 'BonusCards',
            'source' => 'non-db',
            'vname' => 'LBL_CHILDREN',
            'side' => 'left',
        ),
	),
	'relationships' => array(
        'oldcard_bonuscards' => array(
            'rhs_module' => 'BonusCards',
            'rhs_table' => 'bonuscards',
            'rhs_key' => 'old_card_id',
            'lhs_module' => 'BonusCards',
            'lhs_table' => 'bonuscards',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ),
        'bonuscard_children' => array(
            'rhs_module' => 'BonusCards',
            'rhs_table' => 'bonuscards',
            'rhs_key' => 'old_card_id',
            'lhs_module' => 'BonusCards',
            'lhs_table' => 'bonuscards',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ),
        'bonuscards_bonusprograms' => array(
            'rhs_module' => 'BonusCards',
            'rhs_table' => 'bonuscards',
            'rhs_key' => 'bonusprogram_id',
            'lhs_module' => 'BonusPrograms',
            'lhs_table' => 'bonusprograms',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ),
        'bonuscards_accounts' => array(
            'rhs_module' => 'BonusCards',
            'rhs_table' => 'bonuscards',
            'rhs_key' => 'parent_id',
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ),
        'bonuscards_contacts' => array(
            'rhs_module' => 'BonusCards',
            'rhs_table' => 'bonuscards',
            'rhs_key' => 'parent_id',
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ),
        'bonuscards_employees' => array(
            'rhs_module' => 'BonusCards',
            'rhs_table' => 'bonuscards',
            'rhs_key' => 'parent_id',
            'lhs_module' => 'Employees',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ),
	),
	'indices' => array(
	)
);

VardefManager::createVardef('BonusCards', 'BonusCard', array('default', 'assignable'));
