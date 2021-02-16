<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ContactCCDetail'] = array(
    'table' => 'contactccdetails',
    'audited' => false,
    'fields' => array(
        'contact_id' => array(
            'name' => 'contact_id',
            'vname' => 'LBL_CONTACT_ID',
            'type' => 'id',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
        ),
        'contact_name' => array(
            'name' => 'contact_name',
            'rname' => 'name',
            'id_name' => 'contact_id',
            'vname' => 'LBL_CONTACT',
            'join_name' => 'contacts',
            'type' => 'relate',
            'link' => 'contacts',
            'table' => 'contacts',
            'isnull' => 'true',
            'module' => 'Contacts',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'massupdate' => false,
        ),
        'contacts' => array(
            'name' => 'contacts',
            'vname' => 'LBL_CONTACTS',
            'type' => 'link',
            'relationship' => 'contacts_contactccdetails',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        'contact_classification' => array(
            'name' => 'contact_classification',
            'vname' => 'LBL_CLASSIFICATION',
            'type' => 'enum',
            'options' => 'contact_classification_dom',
        ),
        'companycode_id' => array(
            'name' => 'companycode_id',
            'type' => 'id',
            'required' => true,
        ),
        'companycode_name' => array(
            'name' => 'companycode_name',
            'rname' => 'name',
            'id_name' => 'companycode_id',
            'vname' => 'LBL_COMPANYCODE',
            'join_name' => 'companycodes',
            'type' => 'relate',
            'link' => 'companycodes',
            'table' => 'companycodes',
            'isnull' => 'true',
            'module' => 'CompanyCodes',
            'dbType' => 'varchar',
            'len' => '12',
            'source' => 'non-db',
            'unified_search' => true,
            'massupdate' => false,
        ),
        'companycodes' => array(
            'name' => 'companycodes',
            'vname' => 'LBL_COMPANYCODES',
            'type' => 'link',
            'relationship' => 'companycodes_contactccdetails',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        'abccategory' => array(
            'name' => 'abccategory',
            'type' => 'enum',
            'len' => 1,
            'options' => 'abccategory_dom',
            'vname' => 'LBL_CATEGORY',
        ),
        'paymentterms' => array(
            'name' => 'paymentterms',
            'type' => 'varchar',
            'len' => 50,
            'vname' => 'LBL_PAYMENTTERMS',
        ),
        'incoterm1' => array(
            'name' => 'incoterm1',
            'type' => 'varchar',
            'len' => 20,
            'vname' => 'LBL_INCOTERM1',
        ),
        'incoterm2' => array(
            'name' => 'incoterm2',
            'type' => 'varchar',
            'len' => 20,
            'vname' => 'LBL_INCOTERM2',
        ),
    ),
    'indices' => array(
        array('name' => 'idx_contactccdetails_id_del', 'type' => 'index', 'fields' => array('id', 'deleted'),),
        array('name' => 'idx_contactccdetails_companycode_id', 'type' => 'index', 'fields' => array('companycode_id'),),
    ),
    'relationships' => array(
        'contacts_contactccdetails' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'ContactCCDetails',
            'rhs_table' => 'contactccdetails',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many',
        ),
        'companycodes_contactccdetails' => array(
            'lhs_module' => 'CompanyCodes',
            'lhs_table' => 'companycodes',
            'lhs_key' => 'id',
            'rhs_module' => 'ContactCCDetails',
            'rhs_table' => 'contactccdetails',
            'rhs_key' => 'companycode_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
);


VardefManager::createVardef('ContactCCDetails', 'ContactCCDetail', array('default', 'assignable'));