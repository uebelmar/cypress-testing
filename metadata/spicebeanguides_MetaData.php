<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['spicebeanguides'] = array(
    'table' => 'spicebeanguides',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'module' => array(
            'name' => 'module',
            'type' => 'varchar',
            'len' => '50'
        ),
        'status_field' => array(
            'name' => 'status_field',
            'type' => 'varchar',
            'len' => '36'
        ),
        'build_language' => array(
            'name' => 'build_language',
            'type' => 'text'
        )
    ),
    'indices' => array(
        array('name' => 'spicebeanguides_pk', 'type' => 'primary', 'fields' => array('id') ),
        array('name' => 'idx_spicebeanguides_module', 'type' => 'index', 'fields' => array('module') ),
    )
);
$dictionary['spicebeancustomguides'] = array(
    'table' => 'spicebeancustomguides',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'char',
            'len' => '36'
        ),
        'module' => array(
            'name' => 'module',
            'type' => 'varchar',
            'len' => '50'
        ),
        'status_field' => array(
            'name' => 'status_field',
            'type' => 'varchar',
            'len' => '36'
        ),
        'build_language' => array(
            'name' => 'build_language',
            'type' => 'text'
        )
    ),
    'indices' => array(
        array('name' => 'spicebeancustomguides_pk', 'type' => 'primary', 'fields' => array('id') ),
        array('name' => 'idx_spicebeancustomguides_module', 'type' => 'index', 'fields' => array('module') ),
    )
);
$dictionary['spicebeanguidestages'] = array(
    'table' => 'spicebeanguidestages',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'spicebeanguide_id' => array(
            'name' => 'spicebeanguide_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'stage' => array(
            'name' => 'stage',
            'type' => 'varchar',
            'len' => '36'
        ),
        'secondary_stage' => array(
            'name' => 'secondary_stage',
            'type' => 'varchar',
            'len' => '36'
        ),
        'stage_sequence' => array(
            'name' => 'stage_sequence',
            'type' => 'int'
        ),
        'stage_bucket' => array(
            'name' => 'stage_bucket',
            'type' => 'varchar',
            'len' => 50
        ),
        'stage_color' => array(
            'name' => 'stage_color',
            'type' => 'varchar',
            'len' => '6'
        ),
        'stage_add_data' => array(
            'name' => 'stage_add_data',
            'type' => 'text'
        ),
        'stage_label' => array(
            'name' => 'stage_label',
            'type' => 'varchar',
            'len' => 50
        ),
        'stage_componentset' => array(
            'name' => 'stage_componentset',
            'type' => 'varchar',
            'len' => 36
        ),
        'not_in_kanban' => array(
            'name' => 'not_in_kanban',
            'type' => 'bool'
        ),
        'spicebeanguide_status' => array(
            'name' => 'spicebeanguide_status',
            'type' => 'varchar',
            'len' => 4,
            'comment' => 'vlaues are empty, won or lost, this influences the setup of the complete beanguide'
        )
    ),
   'indices' => array(
        array('name' => 'spicebeanguidestages_pk', 'type' => 'primary', 'fields' => array('id') ),
        array('name' => 'idx_spicebeanguidestages_guideid', 'type' => 'index', 'fields' => array('spicebeanguide_id') ),
    )
);

$dictionary['spicebeanguidestages_texts'] = array(
    'table' => 'spicebeanguidestages_texts',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'stage_id' => array(
            'name' => 'stage_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'language' => array(
            'name' => 'language',
            'type' => 'varchar',
            'len' => '5'
        ),
        'stage_name' => array(
            'name' => 'stage_name',
            'type' => 'varchar',
            'len' => '25'
        ),
        'stage_secondaryname' => array(
            'name' => 'stage_secondaryname',
            'type' => 'varchar',
            'len' => '25'
        ),
        'stage_description' => array(
            'name' => 'stage_description',
            'type' => 'text'
        )
    ),
    'indices' => array(
        array('name' => 'spicebeanguidestages_texts_pk', 'type' => 'primary', 'fields' => array('id') ),
        array('name' => 'idx_spicebeanguidestagestexts_stageid', 'type' => 'index', 'fields' => array('stage_id') ),
    )
);

$dictionary['spicebeanguidestages_checks'] = array(
    'table' => 'spicebeanguidestages_checks',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'spicebeanguide_id' => array(
            'name' => 'spicebeanguide_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'stage_id' => array(
            'name' => 'stage_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'check_sequence' => array(
            'name' => 'check_sequence',
            'type' => 'int'
        ),
        'check_include' => array(
            'name' => 'check_include',
            'type' => 'varchar',
            'len' => '150'
        ),
        'check_class' => array(
            'name' => 'check_class',
            'type' => 'varchar',
            'len' => '80'
        ),
        'check_method' => array(
            'name' => 'check_method',
            'type' => 'varchar',
            'len' => 255
        ),
        'check_label' => array(
            'name' => 'check_label',
            'type' => 'varchar',
            'len' => 50
        )
    ),
    'indices' => array(
        array('name' => 'spicebeanguidestages_checks_pk', 'type' => 'primary', 'fields' => array('id') ),
        array('name' => 'idx_spicebeanguidestageschecks_stageid', 'type' => 'index', 'fields' => array('stage_id') ),
        array('name' => 'idx_spicebeanguidestageschecks_guideid', 'type' => 'index', 'fields' => array('spicebeanguide_id') ),
    )
);

$dictionary['spicebeanguidestages_check_texts'] = array(
    'table' => 'spicebeanguidestages_check_texts',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'stage_check_id' => array(
            'name' => 'stage_check_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'language' => array(
            'name' => 'language',
            'type' => 'varchar',
            'len' => '5'
        ),
        'text' => array(
            'name' => 'text',
            'type' => 'varchar',
            'len' => '50'
        )
    ),
    'indices' => array(
        array('name' => 'spicebeanguidestages_check_texts_pk', 'type' => 'primary', 'fields' => array('id') ),
        array('name' => 'idx_spicebeanguidestageschecktexts_stagecheckid', 'type' => 'index', 'fields' => array('stage_check_id') ),
    )
);
