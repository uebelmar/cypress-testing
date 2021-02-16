<?php


$dictionary ['SpiceACLTerritoryElements'] = array(
    'table' => 'spiceaclterritoryelements',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'required' => true,
            'reportable' => false),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 35),
        'length' => array(
            'name' => 'length',
            'vname' => 'LBL_LENGTH',
            'type' => 'int',
            'len' => 2),
    ),
    'indices' => array(
        array(
            'name' => 'id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['SpiceACLTerritoryElementValues'] = array(
    'table' => 'spiceaclterritoryelementvalues',
    'fields' => array(
        'spiceaclterritoryelement_id' => array(
            'name' => 'spiceaclterritoryelement_id',
            'type' => 'id',
            'required' => true,
            'reportable' => false),
        'elementvalue' => array(
            'name' => 'elementvalue',
            'type' => 'varchar',
            'len' => 50),
        'elementdescription' => array(
            'name' => 'elementdescription',
            'type' => 'varchar',
            'len' => 250)
    ),
    'indices' => array(
        array(
            'name' => 'elementvalue',
            'type' => 'unique',
            'fields' => array('spiceaclterritoryelement_id', 'elementvalue')
        )
    )
);

$dictionary ['SpiceACLTerritoryTypes'] = array(
    'table' => 'spiceaclterritorytypes',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'required' => true,
            'reportable' => false),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 35)
    ),
    'indices' => array(
        array(
            'name' => 'id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);
$dictionary ['SpiceACLTerritoryTypes_SpiceACLTerritoryElements'] = array(
    'table' => 'spiceaclttypes_spiceacltelem',
    'fields' => array(
        'spiceaclterritoryelement_id' => array(
            'name' => 'spiceaclterritoryelement_id',
            'type' => 'varchar',
            'len' => 36),
        'spiceaclterritorytype_id' => array(
            'name' => 'spiceaclterritorytype_id',
            'type' => 'varchar',
            'len' => 36),
        'sequence' => array(
            'name' => 'sequence',
            'vname' => 'LBL_SEQUENCE',
            'type' => 'int',
            'len' => 2),
    ),
    'indices' => array(
        array(
            'name' => 'type_element',
            'type' => 'unique',
            'fields' => array('spiceaclterritoryelement_id', 'spiceaclterritorytype_id')
        ),
        array(
            'name' => 'type',
            'type' => 'index',
            'fields' => array('spiceaclterritorytype_id')
        )
    )
);

$dictionary ['SpiceACLTerritories_SpiceACLTerritoryElementValues'] = array(
    'table' => 'spiceaclt_spiceacltelemenv',
    'fields' => array(
        'spiceaclterritory_id' => array(
            'name' => 'spiceaclterritory_id',
            'type' => 'varchar',
            'len' => 36),
        'spiceaclterritoryelement_id' => array(
            'name' => 'spiceaclterritoryelement_id',
            'type' => 'varchar',
            'len' => 36),
        'elementvalue' => array(
            'name' => 'elementvalue',
            'type' => 'varchar',
            'len' => 50),
    ),
    'indices' => array(
        array(
            'name' => 'spiceaclt_t_element',
            'type' => 'unique',
            'fields' => array('spiceaclterritory_id', 'spiceaclterritoryelement_id')
        ),
        array(
            'name' => 'spiceaclt_element_value',
            'type' => 'index',
            'fields' => array('spiceaclterritoryelement_id', 'elementvalue')
        )
    )
);

$dictionary ['SpiceACLTerritories_Modules'] = array(
    'table' => 'spiceaclterritories_modules',
    'fields' => array(
        'spiceaclterritorytype_id' => array(
            'name' => 'spiceaclterritorytype_id',
            'type' => 'varchar',
            'len' => 36),
        'module' => array(
            'name' => 'module',
            'vname' => 'LBL_MODULE',
            'type' => 'varchar',
            'len' => 50),
        'relatefrom' => array(
            'name' => 'relatefrom',
            'vname' => 'LBL_RELATEFROM',
            'type' => 'varchar',
            'len' => 50),
        'multipleobjects' => array(
            'name' => 'multipleobjects',
            'type' => 'bool'),
        'multipleusers' => array(
            'name' => 'multipleusers',
            'type' => 'bool'),
        'suppresspanel' => array(
            'name' => 'suppresspanel',
            'type' => 'bool')
    ),
    'indices' => array(
        array(
            'name' => 'idx_spiceaclterrmod_module',
            'type' => 'unique',
            'fields' => array('module')
        ),
        array(
            'name' => 'idx_spiceaclterrmod_terrtypeid',
            'type' => 'index',
            'fields' => array('spiceaclterritorytype_id')
        )
    )
);

$dictionary['spiceaclterritories_hash'] = array(
    'table' => 'spiceaclterritories_hash',
    'fields' => array(
        'hash_id' => array(
            'name' => 'hash_id',
            'type' => 'varchar',
            'required' => true,
            'len' => '36'),
        'spiceaclterritory_id' => array(
            'name' => 'spiceaclterritory_id',
            'type' => 'varchar',
            'required' => true,
            'len' => '36'),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'required' => true,
            'default' => '0')
    ),
    'indices' => array(
        array(
            'name' => 'spiceaclterritories_hashpk',
            'type' => 'unique',
            'fields' => array('hash_id', 'spiceaclterritory_id')
        )
    )
);

$dictionary['spiceaclusers_hash'] = array(
    'table' => 'spiceaclusers_hash',
    'fields' => array(
        'hash_id' => array(
            'name' => 'hash_id',
            'type' => 'varchar',
            'required' => true,
            'len' => '36'),
        'user_id' => array(
            'name' => 'user_id',
            'type' => 'varchar',
            'required' => true,
            'len' => '36'),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'required' => true,
            'default' => '0')
    ),
    'indices' => array(
        array(
            'name' => 'spiceaclusers_hashpk',
            'type' => 'unique',
            'fields' => array('hash_id', 'user_id')
        ),
        array(
            'name' => 'spiceaclusers_idx_hashdel',
            'type' => 'index',
            'fields' => array('hash_id', 'deleted')
        )

    )
);

$dictionary['spiceaclprofiles_hash'] = array(
    'table' => 'spiceaclprofiles_hash',
    'fields' => array(
        'hash_id' => array(
            'name' => 'hash_id',
            'type' => 'varchar',
            'required' => true,
            'isnull' => false,
            'len' => '36'),
        'spiceaclprofile_id' => array(
            'name' => 'spiceaclprofile_id',
            'type' => 'varchar',
            'required' => true,
            'isnull' => false,
            'len' => '36')
    ),
    'indices' => array(
        array(
            'name' => 'spiceaclprofiles_hashpk',
            'type' => 'primary',
            'fields' => array('hash_id', 'spiceaclprofile_id')
        )
    )
);
