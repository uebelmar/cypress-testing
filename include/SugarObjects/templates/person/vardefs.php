<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$vardefs = array(
    'fields' => array(
        'salutation' => array(
            'name' => 'salutation',
            'vname' => 'LBL_SALUTATION',
            'type' => 'enum',
            'options' => 'salutation_dom',
            'massupdate' => false,
            'len' => '255',
            'comment' => 'Contact salutation (e.g., Mr, Ms)'
        ),
        'first_name' => array(
            'name' => 'first_name',
            'vname' => 'LBL_FIRST_NAME',
            'type' => 'varchar',
            'len' => '100',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
            'comment' => 'First name of the contact',
            'merge_filter' => 'selected',
        ),
        'last_name' => array(
            'name' => 'last_name',
            'vname' => 'LBL_LAST_NAME',
            'type' => 'varchar',
            'len' => '100',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
            'comment' => 'Last name of the contact',
            'merge_filter' => 'selected',
            'required' => true,
            'importable' => 'required',
        ),
        'degree1' => array(
            'name' => 'degree1',
            'vname' => 'LBL_DEGREE1',
            'type' => 'varchar',
            'len' => 50
        ),
        'degree2' => array(
            'name' => 'degree2',
            'vname' => 'LBL_DEGREE2',
            'type' => 'varchar',
            'len' => 50
        ),
        'name' => array(
            'name' => 'name',
            'rname' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'link' => true, // bug 39288
            'fields' => array('first_name', 'last_name'),
            'sort_on' => 'last_name',
            'source' => 'non-db',
            'group' => 'last_name',
            'len' => '255',
            'db_concat_fields' => array(0 => 'first_name', 1 => 'last_name'),
            'importable' => 'false',
        ),
        'full_name' =>
            array(
                'name' => 'full_name',
                'rname' => 'full_name',
                'vname' => 'LBL_NAME',
                'type' => 'fullname',
                'fields' => array('first_name', 'last_name'),
                'sort_on' => 'last_name',
                'sort_on2' => 'first_name',
                'source' => 'non-db',
                'group' => 'last_name',
                'len' => '510',
                'db_concat_fields' => array(0 => 'first_name', 1 => 'last_name'),
                'studio' => array('listview' => false),
            ),
        'communication_language' => array(
            'name' => 'communication_language',
            'vname' => 'LBL_COMMUNICATION_LANGUAGE',
            'type' => 'language',
            'dbtype' => 'varchar',
            'len' => 10
        ),
        'title_dd' => array(
            'name' => 'title_dd',
            'vname' => 'LBL_TITLE_DD',
            'type' => 'enum',
            'len' => 25,
            'options' => 'contacts_title_dom'
        ),
        'title' => array(
            'name' => 'title',
            'vname' => 'LBL_TITLE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The title of the contact'
        ),
        'department' => array(
            'name' => 'department',
            'vname' => 'LBL_DEPARTMENT',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'The department of the contact',
            'merge_filter' => 'enabled',
        ),
        'do_not_call' => array(
            'name' => 'do_not_call',
            'vname' => 'LBL_DO_NOT_CALL',
            'type' => 'bool',
            'default' => '0',
            'audited' => true,
            'comment' => 'An indicator of whether contact can be called'
        ),
        'is_inactive' => array(
            'name' => 'is_inactive',
            'vname' => 'LBL_IS_INACTIVE',
            'type' => 'bool'
        ),
        'phone_home' => array(
            'name' => 'phone_home',
            'vname' => 'LBL_PHONE_HOME',
            'type' => 'phone',
            'dbType' => 'varchar',
            'len' => 100,
            'unified_search' => true,
            'full_text_search' => array('boost' => 1),
            'comment' => 'Home phone number of the contact',
            'merge_filter' => 'enabled',
        ),
        //bug 42902
        'email' => array(
            'name' => 'email',
            'type' => 'email',
            'query_type' => 'default',
            'source' => 'non-db',
            'operator' => 'subquery',
            'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE',
            'db_field' => array(
                'id',
            ),
            'vname' => 'LBL_ANY_EMAIL',
            'studio' => array('visible' => false, 'searchview' => true),
            'importable' => false,
        ),
        'phone_mobile' => array(
            'name' => 'phone_mobile',
            'vname' => 'LBL_PHONE_MOBILE',
            'type' => 'phone',
            'dbType' => 'varchar',
            'len' => 100,
            'unified_search' => true,
            'full_text_search' => array('boost' => 1),
            'comment' => 'Mobile phone number of the contact',
            'merge_filter' => 'enabled',
        ),
        'phone_work' => array(
            'name' => 'phone_work',
            'vname' => 'LBL_PHONE_OFFICE',
            'type' => 'phone',
            'dbType' => 'varchar',
            'len' => 100,
            'audited' => true,
            'unified_search' => true,
            'full_text_search' => array('boost' => 1),
            'comment' => 'Work phone number of the contact',
            'merge_filter' => 'enabled',
        ),
        'phone_other' => array(
            'name' => 'phone_other',
            'vname' => 'LBL_PHONE_OTHER',
            'type' => 'phone',
            'dbType' => 'varchar',
            'len' => 100,
            'unified_search' => true,
            'full_text_search' => array('boost' => 1),
            'comment' => 'Other phone number for the contact',
            'merge_filter' => 'enabled',
        ),
        'phone_fax' =>
            array(
                'name' => 'phone_fax',
                'vname' => 'LBL_PHONE_FAX',
                'type' => 'phone',
                'dbType' => 'varchar',
                'len' => 100,
                'unified_search' => true,
                'full_text_search' => array('boost' => 1),
                'comment' => 'Contact fax number',
                'merge_filter' => 'enabled',
            ),
        'personal_interests' => array(
            'name' => 'personal_interests',
            'type' => 'multienum',
            'isMultiSelect' => true,
            'dbType' => 'text',
            'options' => 'personalinterests_dom',
            'vname' => 'LBL_PERSONAL_INTERESTS',
            'comment' => 'Personal Interests'
        ),
        'email1' => array(
            'name' => 'email1',
            'vname' => 'LBL_EMAIL_ADDRESS',
            'type' => 'varchar',
            'source' => 'non-db',
            'group' => 'email1',
            'merge_filter' => 'enabled',
            'studio' => array('editview' => true, 'editField' => true, 'searchview' => false, 'popupsearch' => false), // bug 46859
            'full_text_search' => array('boost' => 3, 'analyzer' => 'whitespace'), //bug 54567
        ),
        'email2' => array(
            'name' => 'email2',
            'vname' => 'LBL_OTHER_EMAIL_ADDRESS',
            'type' => 'varchar',
            'source' => 'non-db',
            'group' => 'email2',
            'merge_filter' => 'enabled',
            'studio' => 'false',
        ),
        'invalid_email' => array(
            'name' => 'invalid_email',
            'vname' => 'LBL_INVALID_EMAIL',
            'source' => 'non-db',
            'type' => 'bool',
            'massupdate' => false,
            'studio' => 'false',
        ),
        'email_opt_out' => array(
            'name' => 'email_opt_out',
            'vname' => 'LBL_EMAIL_OPT_OUT',
            'source' => 'non-db',
            'type' => 'bool',
            'massupdate' => false,
            'studio' => 'false',
        ),
        'gdpr_data_agreement' => array(
            'name' => 'gdpr_data_agreement',
            'vname' => 'LBL_GDPR_DATA_AGREEMENT',
            'type' => 'bool',
            'default' => true,
            'audited' => true
        ),
        'gdpr_data_source' => array(
            'name' => 'gdpr_data_source',
            'vname' => 'LBL_GDPR_DATA_SOURCE',
            'type' => 'text',
            'audited' => true,
        ),
        'gdpr_marketing_agreement' => array(
            'name' => 'gdpr_marketing_agreement',
            'vname' => 'LBL_GDPR_MARKETING_AGREEMENT',
            'type' => 'bool',
            'default' => false,
            'audited' => true
        ),
        'gdpr_marketing_source' => array(
            'name' => 'gdpr_marketing_source',
            'vname' => 'LBL_GDPR_MARKETING_SOURCE',
            'type' => 'text',
            'audited' => true,
        ),
        'primary_address_street' => array(
            'name' => 'primary_address_street',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150',
            'group' => 'primary_address',
            'comment' => 'Street address for primary address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_street_number' => array(
            'name' => 'primary_address_street_number',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_NUMBER',
            'type' => 'varchar',
            'len' => '10',
            'comment' => 'Street number for primary address'
        ),
        'primary_address_street_number_suffix' => array(
            'name' => 'primary_address_street_number_suffix',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_NUMBER_SUFFIX',
            'type' => 'varchar',
            'len' => '25',
            'comment' => 'additional info on the number like Appartment or similar'
        ),
        'primary_address_attn' => array(
            'name' => 'primary_address_attn',
            'vname' => 'LBL_PRIMARY_ADDRESS_ATTN',
            'type' => 'varchar',
            'len' => '150',
            'comment' => 'additonal attention field for the address',
            'group' => 'primary_address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_street_2' =>
            array(
                'name' => 'primary_address_street_2',
                'vname' => 'LBL_PRIMARY_ADDRESS_STREET_2',
                'type' => 'varchar',
                'len' => '80',
            ),
        'primary_address_street_3' => array(
            'name' => 'primary_address_street_3',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET_3',
            'type' => 'varchar',
            'len' => '80',
        ),
        'primary_address_city' => array(
            'name' => 'primary_address_city',
            'vname' => 'LBL_PRIMARY_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'primary_address',
            'comment' => 'City for primary address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_district' => array(
            'name' => 'primary_address_district',
            'vname' => 'LBL_PRIMARY_ADDRESS_DISTRICT',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'District for primary address',
        ),
        'primary_address_state' => array(
            'name' => 'primary_address_state',
            'vname' => 'LBL_PRIMARY_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'primary_address',
            'comment' => 'State for primary address',
            'merge_filter' => 'enabled',
        ),
        'primary_address_postalcode' => array(
            'name' => 'primary_address_postalcode',
            'vname' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'primary_address',
            'comment' => 'Postal code for primary address',
            'merge_filter' => 'enabled',

        ),
        'primary_address_pobox' =>
            array(
                'name' => 'primary_address_pobox',
                'vname' => 'LBL_PRIMARY_ADDRESS_POBOX',
                'type' => 'varchar',
                'len' => '20',
                'group' => 'primary_address',
                'comment' => 'pobox for primary address',
                'merge_filter' => 'enabled',

            ),
        'primary_address_country' =>
            array(
                'name' => 'primary_address_country',
                'vname' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
                'type' => 'varchar',
                'group' => 'primary_address',
                'comment' => 'Country for primary address',
                'merge_filter' => 'enabled',
            ),
        'primary_address_latitude' =>
            array(
                'name' => 'primary_address_latitude',
                'vname' => 'LBL_PRIMARY_ADDRESS_LATITUDE',
                'type' => 'double',
                'group' => 'primary_address'
            ),
        'primary_address_longitude' =>
            array(
                'name' => 'primary_address_longitude',
                'vname' => 'LBL_PRIMARY_ADDRESS_LONGITUDE',
                'type' => 'double',
                'group' => 'primary_address'
            ),

        'alt_address_street' => array(
            'name' => 'alt_address_street',
            'vname' => 'LBL_ALT_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150',
            'group' => 'alt_address',
            'comment' => 'Street address for alternate address',
            'merge_filter' => 'enabled',
        ),
        'alt_address_street_number' => array(
            'name' => 'alt_address_street_number',
            'vname' => 'LBL_ALT_ADDRESS_STREET_NUMBER',
            'type' => 'varchar',
            'len' => '10',
            'comment' => 'Street number for alternate address',
        ),
        'alt_address_street_number_suffix' => array(
            'name' => 'alt_address_street_number_suffix',
            'vname' => 'LBL_ALT_ADDRESS_STREET_NUMBER_SUFFIX',
            'type' => 'varchar',
            'len' => '25',
            'comment' => 'Add Street number info like Appartmenr or similar',
        ),
        'alt_address_street_2' =>
            array(
                'name' => 'alt_address_street_2',
                'vname' => 'LBL_ALT_ADDRESS_STREET_2',
                'type' => 'varchar',
                'len' => '80'
            ),
        'alt_address_street_3' => array(
            'name' => 'alt_address_street_3',
            'vname' => 'LBL_ALT_ADDRESS_STREET_3',
            'type' => 'varchar',
            'len' => '80'
        ),
        'alt_address_attn' => array(
            'name' => 'alt_address_attn',
            'vname' => 'LBL_ALT_ADDRESS_ATTN',
            'type' => 'varchar',
            'len' => '150'
        ),
        'alt_address_city' => array(
            'name' => 'alt_address_city',
            'vname' => 'LBL_ALT_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'alt_address',
            'comment' => 'City for alternate address',
            'merge_filter' => 'enabled',
        ),
        'alt_address_district' => array(
            'name' => 'alt_address_district',
            'vname' => 'LBL_ALT_ADDRESS_DISTRICT',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'District for alternate address'
        ),
        'alt_address_state' => array(
            'name' => 'alt_address_state',
            'vname' => 'LBL_ALT_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'alt_address',
            'comment' => 'State for alternate address',
            'merge_filter' => 'enabled',
        ),
        'alt_address_postalcode' => array(
            'name' => 'alt_address_postalcode',
            'vname' => 'LBL_ALT_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'alt_address',
            'comment' => 'Postal code for alternate address',
            'merge_filter' => 'enabled',
        ),
        'alt_address_pobox' => array(
            'name' => 'alt_address_pobox',
            'vname' => 'LBL_ALT_ADDRESS_POBOX',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'alt_address',
            'comment' => 'pobox for alternate address',
            'merge_filter' => 'enabled',
        ),
        'alt_address_country' => array(
            'name' => 'alt_address_country',
            'vname' => 'LBL_ALT_ADDRESS_COUNTRY',
            'type' => 'varchar',
            'group' => 'alt_address',
            'comment' => 'Country for alternate address',
            'merge_filter' => 'enabled',
        ),
        'alt_address_latitude' => array(
            'name' => 'alt_address_latitude',
            'vname' => 'LBL_ALT_ADDRESS_LATITUDE',
            'type' => 'double',
            'group' => 'alt_address'
        ),
        'alt_address_longitude' => array(
            'name' => 'alt_address_longitude',
            'vname' => 'LBL_ALT_ADDRESS_LONGITUDE',
            'type' => 'double',
            'group' => 'alt_address'
        ),
        'assistant' => array(
            'name' => 'assistant',
            'vname' => 'LBL_ASSISTANT',
            'type' => 'varchar',
            'len' => '75',
            'unified_search' => true,
            'full_text_search' => array('boost' => 2),
            'comment' => 'Name of the assistant of the contact',
            'merge_filter' => 'enabled',
        ),
        'assistant_phone' => array(
            'name' => 'assistant_phone',
            'vname' => 'LBL_ASSISTANT_PHONE',
            'type' => 'phone',
            'dbType' => 'varchar',
            'len' => 100,
            'group' => 'assistant',
            'unified_search' => true,
            'full_text_search' => array('boost' => 1),
            'comment' => 'Phone number of the assistant of the contact',
            'merge_filter' => 'enabled',
        ),
        'email_addresses_primary' => array(
            'name' => 'email_addresses_primary',
            'type' => 'link',
            'relationship' => strtolower($object_name) . '_email_addresses_primary',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
            'duplicate_merge' => 'disabled',
        ),
        'email_addresses' => array(
            'name' => 'email_addresses',
            'type' => 'link',
            'relationship' => strtolower($object_name) . '_email_addresses',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_ADDRESSES',
            'reportable' => false,
            'unified_search' => true,
            'rel_fields' => array('primary_address' => array('type' => 'bool')),
        ),
        // Used for non-primary mail import
        'email_addresses_non_primary' => array(
            'name' => 'email_addresses_non_primary',
            'type' => 'email',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_NON_PRIMARY',
            'studio' => false,
            'reportable' => false,
            'massupdate' => false,
        ),
    ),
    'relationships' => array(
        strtolower($module) . '_email_addresses' =>
            array(
                'lhs_module' => $module, 'lhs_table' => strtolower($module), 'lhs_key' => 'id',
                'rhs_module' => 'EmailAddresses', 'rhs_table' => 'email_addresses', 'rhs_key' => 'id',
                'relationship_type' => 'many-to-many',
                'join_table' => 'email_addr_bean_rel', 'join_key_lhs' => 'bean_id', 'join_key_rhs' => 'email_address_id',
                'relationship_role_column' => 'bean_module',
                'relationship_role_column_value' => $module
            ),
        strtolower($module) . '_email_addresses_primary' =>
            array('lhs_module' => $module, 'lhs_table' => strtolower($module), 'lhs_key' => 'id',
                'rhs_module' => 'EmailAddresses', 'rhs_table' => 'email_addresses', 'rhs_key' => 'id',
                'relationship_type' => 'many-to-many',
                'join_table' => 'email_addr_bean_rel', 'join_key_lhs' => 'bean_id', 'join_key_rhs' => 'email_address_id',
                'relationship_role_column' => 'primary_address',
                'relationship_role_column_value' => '1'
            ),
    )
);
?>
