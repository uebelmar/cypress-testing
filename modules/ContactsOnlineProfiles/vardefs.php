<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ContactsOnlineProfile'] = array('table' => 'contactsonlineprofiles', 'audited' => true,

    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'fields' =>
        array(
            'salutation' => array(
                'name' => 'salutation',
                'vname' => 'LBL_SALUTATION',
                'type' => 'enum',
                'options' => 'salutation_dom',
                'massupdate' => false,
                'len' => '255'
            ),
            'first_name' => array(
                'name' => 'first_name',
                'vname' => 'LBL_FIRST_NAME',
                'type' => 'varchar',
                'len' => '100'
            ),
            'last_name' => array(
                'name' => 'last_name',
                'vname' => 'LBL_LAST_NAME',
                'type' => 'varchar',
                'len' => '100'
            ),
            'email' => array(
                'name' => 'email',
                'type' => 'email',
                'dbtype' => 'varchar',
                'len' => 100,
                'vname' => 'LBL_EMAIL'
            ),
            'username' => array(
                'name' => 'username',
                'vname' => 'LBL_USER_NAME',
                'type' => 'varchar',
                'len' => 50,
                'required' => false
            ),
            'gdpr_data_agreement' => array(
                'name' => 'gdpr_data_agreement',
                'vname' => 'LBL_GDPR_DATA_AGREEMENT',
                'type' => 'bool',
                'default' => false
            ),
            'gdpr_marketing_agreement' => array(
                'name' => 'gdpr_marketing_agreement',
                'vname' => 'LBL_GDPR_MARKETING_AGREEMENT',
                'type' => 'bool',
                'default' => false
            ),
            'parent_type' => array(
                'name' => 'parent_type',
                'vname' => 'LBL_PARENT_TYPE',
                'type' => 'parent_type',
                'dbType' => 'varchar',
                'required' => false,
                'group' => 'parent_name',
                'options' => 'parent_type_display',
                'len' => 255,
                'comment' => 'The Sugar object to which the call is related'
            ),
            'parent_name' => array(
                'name' => 'parent_name',
                'parent_type' => 'record_type_display',
                'type_name' => 'parent_type',
                'id_name' => 'parent_id',
                'vname' => 'LBL_RELATED_TO',
                'type' => 'parent',
                'group' => 'parent_name',
                'source' => 'non-db',
                'options' => 'parent_type_display',
            ),
            'parent_id' => array(
                'name' => 'parent_id',
                'vname' => 'LBL_PARENT_ID',
                'type' => 'id',
                'required' => true
            ),
            'contact_id' => array(
                'name' => 'contact_id',
                'vname' => 'LBL_CONTACT_ID',
                'type' => 'varchar',
                'len' => 36,
                'required' => false
            ),
            'contact_name' => array(
                'name' => 'contact_name',
                'rname' => 'last_name',
                'id_name' => 'contact_id',
                'vname' => 'LBL_CONTACT',
                'type' => 'relate',
                'link' => 'contacts',
                'table' => 'contacts',
                'isnull' => 'true',
                'module' => 'Contacts',
                'dbType' => 'varchar',
                'len' => 'id',
                'reportable' => false,
                'source' => 'non-db',
            ),
            'contacts' => array(
                'name' => 'contacts',
                'type' => 'link',
                'relationship' => 'contact_contactonlineprofiles',
                'link_type' => 'one',
                'side' => 'right',
                'source' => 'non-db',
                'vname' => 'LBL_CONTACTS',
            ),
            'profile_address_street' =>
                array(
                    'name' => 'profile_address_street',
                    'vname' => 'LBL_STREET',
                    'type' => 'varchar',
                    'len' => '150',
                    'group' => 'profile_address',
                    'comment' => 'Street address for profile address',
                    'merge_filter' => 'enabled',
                ),
            'profile_address_street_2' =>
                array(
                    'name' => 'profile_address_street_2',
                    'vname' => 'LBL_STREET_2',
                    'type' => 'varchar',
                    'len' => '150',
                    'source' => 'non-db',
                ),
            'profile_address_street_3' =>
                array(
                    'name' => 'profile_address_street_3',
                    'vname' => 'LBL_STREET_3',
                    'type' => 'varchar',
                    'len' => '150',
                    'source' => 'non-db',
                ),
            'profile_address_city' =>
                array(
                    'name' => 'profile_address_city',
                    'vname' => 'LBL_CITY',
                    'type' => 'varchar',
                    'len' => '100',
                    'group' => 'profile_address',
                    'comment' => 'City for profile address',
                    'merge_filter' => 'enabled',
                ),
            'profile_address_state' =>
                array(
                    'name' => 'profile_address_state',
                    'vname' => 'LBL_STATE',
                    'type' => 'varchar',
                    'len' => '100',
                    'group' => 'profile_address',
                    'comment' => 'State for profile address',
                    'merge_filter' => 'enabled',
                ),
            'profile_address_postalcode' =>
                array(
                    'name' => 'profile_address_postalcode',
                    'vname' => 'LBL_POSTALCODE',
                    'type' => 'varchar',
                    'len' => '20',
                    'group' => 'profile_address',
                    'comment' => 'Postal code for profile address',
                    'merge_filter' => 'enabled',

                ),
            'profile_address_country' =>
                array(
                    'name' => 'profile_address_country',
                    'vname' => 'LBL_COUNTRY',
                    'type' => 'varchar',
                    'group' => 'profile_address',
                    'comment' => 'Country for profile address',
                    'merge_filter' => 'enabled',
                ),
            'profile_address_latitude' =>
                array(
                    'name' => 'profile_address_latitude',
                    'vname' => 'LBL_LATITUDE',
                    'type' => 'double',
                    'group' => 'profile_address'
                ),
            'profile_address_longitude' =>
                array(
                    'name' => 'profile_address_longitude',
                    'vname' => 'LBL_LONGITUDE',
                    'type' => 'double',
                    'group' => 'profile_address'
                ),

        ),
    'indices' => array(),
    'relationships' => array(
        'contact_contactonlineprofiles' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'ContactsOnlineProfiles',
            'rhs_table' => 'contactsonlineprofiles',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many')
    ),
    'optimistic_locking' => true,
);

VardefManager::createVardef('ContactsOnlineProfiles', 'ContactsOnlineProfile', array('default', 'assignable'));

//set global else error with PHP7.1: Uncaught Error: Cannot use string offset as an array
global $dictionary;
$dictionary['ContactsOnlineProfile']['fields']['name']['vname'] = 'LBL_SOURCE';
