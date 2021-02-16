<?php

use SpiceCRM\includes\database\DBManagerFactory;

$vardefs = array(
    'fields' => array(
        'spiceacl_primary_territory' => array(
            'name' => 'spiceacl_primary_territory',
            'vname' => 'LBL_SPICEACL_PRIMARY_TERRITORY',
            'type' => 'territory',
            'dbtype' => 'varchar',
            'len' => 36,
            'required' => true,
            'audited' => true,
            'reportable' => false
        ),
        'spiceacl_primary_territory_link' => array(
            'name' => 'spiceacl_primary_territory_link',
            'vname' => 'LBL_PRIMARY_TERRITORY',
            'type' => 'link',
            'relationship' => strtolower($table_name) . '_primary_territory',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'spiceacl_primary_territory_name' => array(
            'name' => 'spiceacl_primary_territory_name',
            'rname' => 'name',
            'id_name' => 'spiceacl_primary_territory',
            'vname' => 'LBL_SPICEACL_PRIMARY_TERRITORY_NAME',
            'type' => 'relate',
            'link' => 'spiceacl_primary_territory_link',
            'table' => 'spiceaclterritories',
            'isnull' => 'true',
            'required' => true,
            'module' => 'SpiceACLTerritories',
            'source' => 'non-db'
        ),
        'spiceacl_secondary_territories' => array(
            'name' => 'spiceacl_secondary_territories',
            'vname' => 'LBL_SPICEACL_SECONDARY_TERRITORIES',
            'type' => 'territories',
            'source' => 'non-db'
        ),
        'spiceacl_territories_hash' => array(
            'name' => 'spiceacl_territories_hash',
            'vname' => 'LBL_SPICEACL_TERRITORIES_HASH',
            'type' => 'territories',
            'dbtype' => 'varchar',
            'len' => 32,
            'audited' => true,
            'reportable' => false
        )
    ),
    'relationships' => array(
        strtolower($table_name) . '_primary_territory' => array(
            'lhs_module' => 'SpiceACLTerritories',
            'lhs_table' => 'spiceaclterritories',
            'lhs_key' => 'id',
            'rhs_module' => $module,
            'rhs_table' => $table_name,
            'rhs_key' => 'spiceacl_primary_territory',
            'relationship_type' => 'one-to-many'
        )
    ),
    'indices' => array(
        array('name' => 'idx_' . strtolower($table_name) . '_spiceacl_thash', 'type' => 'index', 'fields' => array('spiceacl_territories_hash')),
        array('name' => 'idx_' . strtolower($table_name) . '_createdby_aclterr_hash', 'type' => 'index', 'fields' => array('created_by', 'spiceacl_territories_hash')),
        array('name' => 'idx_' . strtolower($table_name) . '_assign_aclterr_hash', 'type' => 'index', 'fields' => array('assigned_user_id', 'spiceacl_territories_hash')),
        array('name' => 'idx_' . strtolower($table_name) . '_spiceacl_primary_territory', 'type' => 'index', 'fields' => array('spiceacl_primary_territory')),
        array('name' => 'idx_' . strtolower($table_name) . '_createdby_aclprimterr_hash', 'type' => 'index', 'fields' => array('created_by', 'spiceacl_primary_territory')),
        array('name' => 'idx_' . strtolower($table_name) . '_assign_aclprimterr_hash', 'type' => 'index', 'fields' => array('assigned_user_id', 'spiceacl_primary_territory')),

    )
);

// check if the object is self managed ... then add the relationship 
$db = DBManagerFactory::getInstance();
if ($db != null) {
    global $beanList;
    $elementRow = $db->fetchByAssoc($db->query("SELECT * FROM spiceaclterritories_modules WHERE module='$module'"));
    if (!empty($elementRow['spiceaclterritorytype_id']) && empty($elementRow['relatefrom'])) {
        $vardefs['relationships'][strtolower($module) . '_territories'] = array(
            'lhs_module' => $module,
            'lhs_table' => $table_name,
            'lhs_key' => 'spiceacl_territories_hash',
            'rhs_module' => 'SpiceACLTerritories',
            'rhs_table' => 'spiceaclterritories',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'spiceaclterritories_hash',
            'join_key_lhs' => 'hash_id',
            'join_key_rhs' => 'spiceaclterritory_id',
        );
        // link for all Objects
        $vardefs['fields']['spiceacl_territories_link'] = array(
            'name' => 'spiceacl_territories_link',
            'vname' => 'LBL_SPICEACL_TERRITORIES',
            'type' => 'link',
            'relationship' => strtolower($module) . '_territories',
            'link_type' => 'one',
            'source' => 'non-db',
            'default' => true,
            'module' => 'SpiceACLTerritories'
        );
    }
}
